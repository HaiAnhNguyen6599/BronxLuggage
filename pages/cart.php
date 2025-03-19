<?php
require "../config.php"; // File kết nối database
require_once '../functions.php';

// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy user_id (nếu có)
$user_id = $_SESSION['user_id'] ?? 0;

// Xử lý yêu cầu cập nhật số lượng (gửi qua AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    header('Content-Type: application/json');
    $cart_id = $_POST['cart_id'] ?? '';
    $new_quantity = max(1, intval($_POST['quantity'] ?? 1));

    // Debug: Ghi log dữ liệu nhận được
    error_log("Updating quantity: cart_id=$cart_id, quantity=$new_quantity, user_id=$user_id");

    if (empty($cart_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart_id']);
        exit;
    }

    if ($user_id > 0) {
        // Kiểm tra xem cart_id có tồn tại không
        $stmt = $conn->prepare("SELECT c.product_id FROM cart c WHERE c.id = ? AND c.user_id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Database error: Prepare failed']);
            exit;
        }
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_item = $result->fetch_assoc();
        $stmt->close();

        if (!$cart_item) {
            echo json_encode(['success' => false, 'message' => 'Cart item not found. It may have been removed.']);
            exit;
        }

        // Kiểm tra tồn kho trước khi cập nhật
        $stmt = $conn->prepare("SELECT p.inventory, p.name FROM products p WHERE p.id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Database error: Prepare failed']);
            exit;
        }
        $stmt->bind_param("i", $cart_item['product_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        if ($new_quantity > $product['inventory']) {
            echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock for ' . htmlspecialchars($product['name']) . '. Available: ' . $product['inventory']]);
            exit;
        }

        // Cập nhật số lượng trong database
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Database error: Prepare failed']);
            exit;
        }
        $stmt->bind_param("iii", $new_quantity, $cart_id, $user_id);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            error_log("Execute failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
        }
    } else {
        // Cập nhật số lượng trong session
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if (!is_array($item) || !isset($item['product_id']) || !isset($item['size_id']) || !isset($item['color_id'])) {
                    continue;
                }
                $generated_cart_id = $item['product_id'] . '-' . $item['size_id'] . '-' . $item['color_id'];
                if ($generated_cart_id === $cart_id) {
                    // Kiểm tra tồn kho
                    $product = getProductById($conn, $item['product_id']);
                    if (!$product) {
                        echo json_encode(['success' => false, 'message' => 'Product not found']);
                        exit;
                    }
                    if ($new_quantity > $product['inventory']) {
                        echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock for ' . htmlspecialchars($product['name']) . '. Available: ' . $product['inventory']]);
                        exit;
                    }
                    $item['quantity'] = $new_quantity;
                    $found = true;
                    break;
                }
            }
            unset($item); // Hủy tham chiếu
            if ($found) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        }
    }
    exit;
}

// Đồng bộ giỏ hàng từ session sang database khi người dùng vừa đăng nhập
if ($user_id > 0 && isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (!is_array($item) || !isset($item['product_id']) || !isset($item['quantity']) || !isset($item['size_id']) || !isset($item['color_id'])) {
            error_log("Invalid cart item during sync: " . print_r($item, true));
            continue;
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng của user trong database chưa
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND size_id = ? AND color_id = ?");
        $stmt->bind_param("iiii", $user_id, $item['product_id'], $item['size_id'], $item['color_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing_item = $result->fetch_assoc();
        $stmt->close();

        if ($existing_item) {
            // Nếu sản phẩm đã có, cập nhật số lượng
            $new_quantity = $existing_item['quantity'] + $item['quantity'];
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_quantity, $existing_item['id']);
            $stmt->execute();
            $stmt->close();
        } else {
            // Nếu sản phẩm chưa có, thêm mới vào database
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, size_id, color_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiii", $user_id, $item['product_id'], $item['quantity'], $item['size_id'], $item['color_id']);
            $stmt->execute();
            $stmt->close();
        }
    }
    // Xóa session cart sau khi đồng bộ
    unset($_SESSION['cart']);
}

// Xử lý yêu cầu xóa sản phẩm (gửi qua AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    header('Content-Type: application/json'); // Đảm bảo trả về JSON
    $cart_id = $_POST['cart_id'] ?? '';
    error_log("Removing cart_id: " . $cart_id); // Debug

    if (empty($cart_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart_id']);
        exit;
    }

    if ($user_id > 0) {
        // Xóa khỏi database nếu người dùng đã đăng nhập
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Database error: Prepare failed']);
            exit;
        }
        $stmt->bind_param("ii", $cart_id, $user_id);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            error_log("Execute failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Database error: Execute failed']);
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        // Xóa khỏi session nếu chưa đăng nhập
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $found = false;
            foreach ($_SESSION['cart'] as $key => $item) {
                if (!is_array($item) || !isset($item['product_id']) || !isset($item['size_id']) || !isset($item['color_id'])) {
                    error_log("Invalid cart item at key $key: " . print_r($item, true));
                    continue;
                }
                $generated_cart_id = $item['product_id'] . '-' . $item['size_id'] . '-' . $item['color_id'];
                error_log("Comparing cart_id: $cart_id with generated: $generated_cart_id"); // Debug
                if ($generated_cart_id === $cart_id) {
                    unset($_SESSION['cart'][$key]);
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        }
    }
    exit;
}

// Lấy danh sách sản phẩm trong giỏ hàng
$cart_items = [];
$total_price = 0;

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT c.id as cart_id, p.id, p.name, p.price, p.inventory, c.quantity, c.size_id, c.color_id 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Nếu giỏ hàng rỗng, không cần tiếp tục
    if (empty($cart_items)) {
        $cart_items = [];
    } else {
        // Lấy thêm thông tin size và color
        foreach ($cart_items as &$item) {
            $size = getSizeById($conn, $item['size_id']);
            $color = getColorById($conn, $item['color_id']);
            $item['size_name'] = $size['name'] ?? 'N/A';
            $item['color_name'] = $color['name'] ?? 'N/A';
            $item['image_url'] = getProductImages($conn, $item['id'])[0]['image_url'] ?? 'img/default.jpg';
        }
        unset($item);

        $total_price = array_reduce($cart_items, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }
} else {
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (!is_array($item) || !isset($item['product_id']) || !isset($item['quantity']) || !isset($item['size_id']) || !isset($item['color_id'])) {
                error_log("Invalid cart item in display: " . print_r($item, true));
                continue;
            }
            $product = getProductById($conn, $item['product_id']);
            if ($product) {
                $size = getSizeById($conn, $item['size_id']);
                $color = getColorById($conn, $item['color_id']);
                $cart_items[] = [
                    'cart_id' => $item['product_id'] . '-' . $item['size_id'] . '-' . $item['color_id'],
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'inventory' => $product['inventory'],
                    'quantity' => $item['quantity'],
                    'size_name' => $size['name'] ?? 'N/A',
                    'color_name' => $color['name'] ?? 'N/A',
                    'image_url' => getProductImages($conn, $product['id'])[0]['image_url'] ?? 'img/default.jpg'
                ];
            }
        }
        $total_price = array_reduce($cart_items, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }
}

// Hàm hỗ trợ
if (!function_exists('getSizeById')) {
    function getSizeById($conn, $size_id)
    {
        $stmt = $conn->prepare("SELECT name FROM sizes WHERE id = ?");
        $stmt->bind_param("i", $size_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $size = $result->fetch_assoc();
        $stmt->close();
        return $size ?: ['name' => 'N/A'];
    }
}

if (!function_exists('getColorById')) {
    function getColorById($conn, $color_id)
    {
        $stmt = $conn->prepare("SELECT name FROM colors WHERE id = ?");
        $stmt->bind_param("i", $color_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $color = $result->fetch_assoc();
        $stmt->close();
        return $color ?: ['name' => 'N/A'];
    }
}

if (!function_exists('getProductImages')) {
    function getProductImages($conn, $product_id)
    {
        $stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? LIMIT 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $images;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body>
    <?php include '../includes/topbar.php'; ?>
    <?php include '../includes/navbar.php'; ?>

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

    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover text-center mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        <?php if (empty($cart_items)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Your cart is empty.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                                <tr data-cart-id="<?php echo htmlspecialchars($item['cart_id'] ?? ''); ?>">
                                    <td class="align-middle text-left">
                                        <img src="../<?php echo htmlspecialchars($item['image_url'] ?? 'img/default.jpg'); ?>" alt="" style="width: 50px;">
                                        <a href="product.php?id=<?php echo htmlspecialchars($item['id'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?>
                                            <?php if ($user_id > 0 || isset($item['size_name'])): ?>
                                                (Size: <?php echo htmlspecialchars($item['size_name'] ?? 'N/A'); ?>,
                                                Color: <?php echo htmlspecialchars($item['color_name'] ?? 'N/A'); ?>)
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">$<?php echo number_format($item['price'] ?? 0, 2); ?></td>
                                    <td class="align-middle">
                                        <div class="input-group quantity mx-auto" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-minus">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <input type="number" min="1" max="<?php echo htmlspecialchars($item['inventory'] ?? 0); ?>"
                                                class="form-control form-control-sm bg-secondary border-0 text-center quantity-input"
                                                value="<?php echo htmlspecialchars($item['quantity'] ?? 1); ?>">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-plus">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">$<span class="item-total"><?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2); ?></span></td>
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-danger btn-remove" data-cart-id="<?php echo htmlspecialchars($item['cart_id'] ?? ''); ?>">
                                            <i class="fa fa-times"></i>
                                        </button>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".quantity").forEach(function(quantityWrapper) {
                let quantityInput = quantityWrapper.querySelector(".quantity-input");
                let minusButton = quantityWrapper.querySelector(".btn-minus");
                let plusButton = quantityWrapper.querySelector(".btn-plus");
                let maxStock = parseInt(quantityInput.getAttribute("max"));
                let cartId = quantityWrapper.closest("tr").getAttribute("data-cart-id");

                plusButton.addEventListener("click", function(e) {
                    e.preventDefault();
                    let currentQuantity = parseInt(quantityInput.value) || 0;
                    if (currentQuantity < maxStock) {
                        quantityInput.value = currentQuantity + 1;
                        updateQuantity(cartId, quantityInput.value, quantityInput);
                        updateItemTotal(quantityWrapper.closest("tr"));
                    }
                });

                minusButton.addEventListener("click", function(e) {
                    e.preventDefault();
                    let currentQuantity = parseInt(quantityInput.value) || 0;
                    if (currentQuantity > 1) {
                        quantityInput.value = currentQuantity - 1;
                        updateQuantity(cartId, quantityInput.value, quantityInput);
                        updateItemTotal(quantityWrapper.closest("tr"));
                    }
                });

                quantityInput.addEventListener("change", function() {
                    let value = parseInt(this.value) || 1;
                    if (value < 1) value = 1;
                    if (value > maxStock) value = maxStock;
                    this.value = value;
                    updateQuantity(cartId, this.value, this);
                    updateItemTotal(quantityWrapper.closest("tr"));
                });
            });

            function updateQuantity(cartId, quantity, quantityInput) {
                let previousQuantity = parseInt(quantityInput.getAttribute("data-previous-value") || quantityInput.value);
                quantityInput.setAttribute("data-previous-value", quantity);

                console.log("Updating quantity: cartId=" + cartId + ", quantity=" + quantity); // Debug

                fetch("cart.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: "action=update_quantity&cart_id=" + encodeURIComponent(cartId) + "&quantity=" + quantity
                    })
                    .then(response => {
                        console.log("Response status:", response.status); // Debug
                        if (!response.ok) {
                            throw new Error("Network response was not ok: " + response.statusText);
                        }
                        return response.text(); // Lấy text trước để debug
                    })
                    .then(text => {
                        console.log("Raw response:", text); // Debug
                        try {
                            const data = JSON.parse(text);
                            if (!data.success) {
                                alert("Failed to update quantity: " + (data.message || "Unknown error"));
                                if (data.message.includes("Cart item not found")) {
                                    // Nếu giỏ hàng không còn, làm mới trang
                                    window.location.reload();
                                } else {
                                    quantityInput.value = previousQuantity; // Khôi phục số lượng
                                    updateItemTotal(quantityInput.closest("tr"));
                                }
                            }
                        } catch (error) {
                            console.error("JSON parse error:", error);
                            throw new Error("Invalid JSON response: " + text);
                        }
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                        alert("An error occurred while updating the quantity: " + error.message);
                        quantityInput.value = previousQuantity; // Khôi phục số lượng
                        updateItemTotal(quantityInput.closest("tr"));
                    });
            }

            document.querySelectorAll(".btn-remove").forEach(function(button) {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    let cartId = this.getAttribute("data-cart-id");
                    console.log("Removing item with cart_id:", cartId);
                    let row = this.closest("tr");

                    fetch("cart.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: "action=remove&cart_id=" + encodeURIComponent(cartId)
                        })
                        .then(response => {
                            console.log("Response status:", response.status);
                            console.log("Response headers:", response.headers.get("content-type"));
                            if (!response.ok) {
                                throw new Error("Network response was not ok: " + response.statusText);
                            }
                            return response.text();
                        })
                        .then(text => {
                            console.log("Raw response:", text);
                            try {
                                const data = JSON.parse(text);
                                console.log("Parsed response data:", data);
                                if (data.success) {
                                    row.remove();
                                    updateCartTotal();
                                    if (document.querySelectorAll("tbody tr").length === 0) {
                                        document.querySelector("tbody").innerHTML = '<tr><td colspan="5" class="text-center">Your cart is empty.</td></tr>';
                                    }
                                } else {
                                    alert("Failed to remove item: " + (data.message || "Unknown error"));
                                    if (data.message.includes("Item not found")) {
                                        window.location.reload();
                                    }
                                }
                            } catch (error) {
                                console.error("JSON parse error:", error);
                                throw new Error("Invalid JSON response: " + text);
                            }
                        })
                        .catch(error => {
                            console.error("Fetch error:", error);
                            alert("An error occurred while removing the item: " + error.message);
                        });
                });
            });

            function updateItemTotal(row) {
                let price = parseFloat(row.querySelector("td:nth-child(2)").textContent.replace("$", ""));
                let quantity = parseInt(row.querySelector(".quantity-input").value);
                let itemTotal = price * quantity;
                row.querySelector(".item-total").textContent = itemTotal.toFixed(2);
                updateCartTotal();
            }

            function updateCartTotal() {
                let total = 0;
                document.querySelectorAll(".item-total").forEach(function(totalElement) {
                    total += parseFloat(totalElement.textContent);
                });
                document.getElementById("total-price").textContent = "$" + total.toFixed(2);
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    <script src="js/main.js"></script>
</body>

</html>