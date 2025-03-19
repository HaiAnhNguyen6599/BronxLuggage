<?php
require "../config.php"; // File kết nối database
require_once '../functions.php';

// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy user_id (nếu có)
$user_id = $_SESSION['user_id'] ?? 0;

// Xử lý yêu cầu AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    header('Content-Type: application/json'); // Đảm bảo trả về JSON
    $cart_id = $_POST['cart_id'];
    $quantity = max(1, intval($_POST['quantity'])); // Đảm bảo số lượng không âm

    try {
        if ($user_id > 0) {
            // Người dùng đã đăng nhập: Cập nhật database
            $stmt = $conn->prepare("SELECT p.id, p.price, p.inventory, c.quantity 
                                    FROM cart c 
                                    JOIN products p ON c.product_id = p.id 
                                    WHERE c.id = ? AND c.user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();

            if (!$item) {
                echo json_encode(["success" => false, "message" => "Item not found in cart"]);
                exit;
            }

            if ($quantity > $item['inventory']) {
                echo json_encode(["success" => false, "message" => "Quantity exceeds available stock"]);
                exit;
            }

            if ($quantity > 0) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $cart_id, $user_id);
                $stmt->execute();
            }

            // Tính lại tổng giá
            $stmt = $conn->prepare("SELECT SUM(p.price * c.quantity) as total 
                                    FROM cart c 
                                    JOIN products p ON c.product_id = p.id 
                                    WHERE c.user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $total_price = $result->fetch_assoc()['total'] ?? 0;

            $item_total = $quantity > 0 ? $item['price'] * $quantity : 0;

            echo json_encode([
                "success" => true,
                "item_total" => number_format($item_total, 2),
                "total_price" => number_format($total_price, 2)
            ]);
        } else {
            // Người dùng chưa đăng nhập: Cập nhật session
            if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
                echo json_encode(["success" => false, "message" => "Cart is empty"]);
                exit;
            }

            $found = false;
            $item_total = 0;
            $total_price = 0;

            foreach ($_SESSION['cart'] as &$item) {
                $item_id = $item['product_id'] . '-' . ($item['size_id'] ?? 0) . '-' . ($item['color_id'] ?? 0);
                if ($item_id === $cart_id) {
                    $product = getProductById($conn, $item['product_id']);
                    if (!$product) {
                        echo json_encode(["success" => false, "message" => "Product not found"]);
                        exit;
                    }

                    if ($quantity > $product['inventory']) {
                        echo json_encode(["success" => false, "message" => "Quantity exceeds available stock"]);
                        exit;
                    }

                    if ($quantity > 0) {
                        $item['quantity'] = $quantity;
                        $item_total = $product['price'] * $quantity;
                    } else {
                        unset($item); // Xóa sản phẩm
                        $item_total = 0;
                    }
                    $found = true;
                    break;
                }
            }
            unset($item); // Hủy tham chiếu

            if (!$found) {
                echo json_encode(["success" => false, "message" => "Item not found in cart"]);
                exit;
            }

            $_SESSION['cart'] = array_values($_SESSION['cart']); // Sắp xếp lại mảng
            foreach ($_SESSION['cart'] as $item) {
                $product = getProductById($conn, $item['product_id']);
                $total_price += $product['price'] * $item['quantity'];
            }

            echo json_encode([
                "success" => true,
                "item_total" => number_format($item_total, 2),
                "total_price" => number_format($total_price, 2)
            ]);
        }
    } catch (Exception $e) {
        // Ghi log lỗi để debug
        error_log("Error in cart.php: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
    }
    exit();
}

// Lấy danh sách sản phẩm trong giỏ hàng
$cart_items = [];
$total_price = 0;

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT c.id as cart_id, p.id, p.name, p.price, p.inventory, c.quantity 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);

    $total_price = array_reduce($cart_items, function ($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);
} else {
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item) && isset($item['product_id']) && isset($item['quantity'])) {
                $product = getProductById($conn, $item['product_id']);
                if ($product) {
                    $cart_items[] = [
                        'cart_id' => $item['product_id'] . '-' . ($item['size_id'] ?? 0) . '-' . ($item['color_id'] ?? 0),
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'inventory' => $product['inventory'],
                        'quantity' => $item['quantity']
                    ];
                }
            }
        }
        $total_price = array_reduce($cart_items, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php' ?>
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php' ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php' ?>
    <!-- Navbar End -->

    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Shopping Cart</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Cart Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-left">Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle" id="cart-items">
                        <?php if (empty($cart_items)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Your cart is empty.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                                <tr data-cart-id="<?php echo $item['cart_id']; ?>">
                                    <td class="align-middle text-left">
                                        <a href="product.php?id=<?php echo $item['id']; ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="align-middle">
                                        <div class="input-group quantity mx-auto" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-minus">-</button>
                                            </div>
                                            <input type="number" min="1" max="<?php echo $item['inventory']; ?>"
                                                class="form-control form-control-sm bg-secondary border-0 text-center quantity-input"
                                                value="<?php echo $item['quantity']; ?>">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-plus">+</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">$<span
                                            class="item-total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart Summary</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 id="total-price">$<?php echo number_format($total_price, 2); ?></h5>
                        </div>
                        <a href="<?php echo $user_id > 0 ? 'checkout.php' : '../account/login.php'; ?>"
                            class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            async function updateCart(cartId, quantity, row) {
                try {
                    const response = await fetch("cart.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `cart_id=${cartId}&quantity=${quantity}`
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        if (quantity === 0) {
                            row.remove();
                        } else {
                            row.querySelector(".item-total").textContent = data.item_total;
                            row.querySelector(".quantity-input").value = quantity;
                        }
                        document.getElementById("total-price").textContent = `$${data.total_price}`;
                    } else {
                        alert(data.message || "Failed to update cart");
                    }
                } catch (error) {
                    console.error("Error updating cart:", error);
                    alert("An error occurred while updating the cart: " + error.message);
                }
            }

            document.querySelectorAll(".quantity-input").forEach(input => {
                input.addEventListener("change", function() {
                    const row = this.closest("tr");
                    const cartId = row.dataset.cartId;
                    const newQuantity = parseInt(this.value) || 1;
                    if (newQuantity >= 1 && newQuantity <= parseInt(this.max)) {
                        updateCart(cartId, newQuantity, row);
                    } else {
                        this.value = this.defaultValue; // Reset về giá trị ban đầu nếu không hợp lệ
                        alert("Quantity must be between 1 and " + this.max);
                    }
                });
            });

            document.querySelectorAll(".btn-minus").forEach(button => {
                button.addEventListener("click", function() {
                    const input = this.closest(".quantity").querySelector(".quantity-input");
                    let value = parseInt(input.value) || 1;
                    if (value > 1) {
                        input.value = --value;
                        input.dispatchEvent(new Event("change"));
                    }
                });
            });

            document.querySelectorAll(".btn-plus").forEach(button => {
                button.addEventListener("click", function() {
                    const input = this.closest(".quantity").querySelector(".quantity-input");
                    let value = parseInt(input.value) || 1;
                    if (value < parseInt(input.max)) {
                        input.value = ++value;
                        input.dispatchEvent(new Event("change"));
                    }
                });
            });

            document.querySelectorAll(".btn-remove").forEach(button => {
                button.addEventListener("click", function() {
                    const row = this.closest("tr");
                    const cartId = row.dataset.cartId;

                    if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?")) {
                        updateCart(cartId, 0, row);
                    }
                });
            });
        });
    </script>

    <!-- Footer Start -->
    <?php include '../includes/footer.php' ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>