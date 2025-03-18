<?php
require "../config.php";
require_once '../functions.php';

// $user_id = 1;  // Lấy thông tin của người dùng, ví dụ ở đây là user_id = 1

$user_id = $_SESSION['user_id'];  // Lấy thông tin của người dùng đã đăng nhập

// Truy vấn thông tin giỏ hàng của người dùng
$sql = "SELECT p.name AS product_name, p.price AS product_price, c.quantity AS product_quantity, c.id AS cart_id, c.product_id
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['product_price'] * $row['product_quantity'];  // Tính lại subtotal cho mỗi sản phẩm
    $row['subtotal'] = $subtotal;  // Gán giá trị subtotal vào mảng kết quả
    $cart_items[] = $row;
    $total += $subtotal;  // Tổng giá trị giỏ hàng
}

$shipping = 0;

// Xử lý hành động tăng hoặc giảm số lượng
if (isset($_POST['action']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Lấy thông tin sản phẩm để kiểm tra số lượng trong kho
    $sql = "SELECT inventory, price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $inventory = $product['inventory']; // Số lượng tồn kho
    $product_price = $product['price']; // Giá của sản phẩm

    // Kiểm tra hành động tăng số lượng
    if ($_POST['action'] == 'increase' && $quantity <= $inventory) {
        // Cập nhật giỏ hàng nếu số lượng không vượt quá số lượng trong kho
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    }
    // Kiểm tra hành động giảm số lượng
    elseif ($_POST['action'] == 'decrease' && $quantity >= 1) {
        // Cập nhật giỏ hàng nếu số lượng >= 1
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    }

    // Tính lại giỏ hàng và tổng giá trị
    $cart_items = [];
    $total = 0;
    // Truy vấn thông tin giỏ hàng của người dùng
    $sql = "SELECT p.name AS product_name, p.price AS product_price, c.quantity AS product_quantity, c.id AS cart_id, c.product_id
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $subtotal = $row['product_price'] * $row['product_quantity'];  // Tính lại subtotal cho mỗi sản phẩm
        $row['subtotal'] = $subtotal;  // Gán giá trị subtotal vào mảng kết quả
        $cart_items[] = $row;
        $total += $subtotal;  // Tổng giá trị giỏ hàng
    }

    // Xử lý phí vận chuyển
    $shipping = 0; // Có thể thêm logic xử lý phí vận chuyển ở đây

    // Trả về JSON cập nhật thông tin giỏ hàng
    echo json_encode([
        'status' => 'success',
        'total' => $total + $shipping, // Tổng cộng có tính cả phí vận chuyển
        'cart_items' => $cart_items
    ]);
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_POST['action']) && isset($_POST['product_id']) && $_POST['action'] == 'remove') {
    $product_id = $_POST['product_id'];

    // Xóa sản phẩm khỏi giỏ hàng
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    if ($stmt->execute()) {
        // Cập nhật lại giỏ hàng và tổng giá trị sau khi xóa
        $total = 0;
        $cart_items = [];
        $sql = "SELECT p.name AS product_name, p.price AS product_price, c.quantity AS product_quantity, c.id AS cart_id, c.product_id
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $subtotal = $row['product_price'] * $row['product_quantity'];
            $row['subtotal'] = $subtotal;
            $cart_items[] = $row;
            $total += $subtotal;
        }
    } else {
        // Nếu có lỗi trong việc xóa sản phẩm
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove product']);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="shop.php">Shop</a>
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
                        <?php foreach ($cart_items as $item) : ?>
                            <tr>
                                <td class="align-middle" style="text-align:left;">
                                    <a href="product.php?id=<?php echo $item['product_id']; ?>" style="color:#6C757D;"><?php echo $item['product_name']; ?></a>
                                </td>
                                <td class="align-middle">$<?php echo number_format($item['product_price'], 2); ?></td>

                                <td class="align-middle">
                                    <div class="input-group quantity mx-auto" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-sm btn-primary btn-minus change_quantity" data-action="decrease" data-product-id="<?= $item['product_id'] ?>" data-cart-id="<?= $item['cart_id'] ?>">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="number" class="form-control form-control-sm bg-secondary border-0 text-center" style="padding: 4px 0;" name="quantity" value="<?php echo $item['product_quantity']; ?>" id="quantity_<?php echo $item['cart_id']; ?>" min="1" max="<?php echo $product['inventory']; ?>" readonly>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-sm btn-primary btn-plus change_quantity" data-action="increase" data-product-id="<?= $item['product_id'] ?>" data-cart-id="<?= $item['cart_id'] ?>">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle subtotal subtotal_<?php echo $item['cart_id']; ?>">$<?php echo number_format($item['subtotal'], 2); ?></td>

                                <td class="align-middle">
                                    <form action="cart.php" method="POST" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart Summary</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="border-bottom pb-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Subtotal</h6>
                            <h6 class="cart-total">$<?php echo number_format($total, 2); ?></h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">$<?php echo number_format($shipping, 2); ?></h6>
                        </div>
                    </div>
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5>$<?php echo number_format($total + $shipping, 2); ?></h5>
                        </div>
                        <a href="checkout.php" class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->


    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".change_quantity").forEach(function(button) {
                button.addEventListener("click", function() {
                    var action = this.getAttribute("data-action");
                    var productId = this.getAttribute("data-product-id");
                    var cartId = this.getAttribute("data-cart-id");
                    var quantityInput = document.getElementById("quantity_" + cartId);
                    var currentQuantity = parseInt(quantityInput.value);

                    // Cập nhật số lượng trong input
                    if (action === "increase") {
                        quantityInput.value = currentQuantity + 1;
                    } else if (action === "decrease" && currentQuantity > 1) {
                        quantityInput.value = currentQuantity - 1;
                    }

                    var quantity = quantityInput.value;

                    // Gửi yêu cầu AJAX để cập nhật giỏ hàng
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "cart.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status == "success") {
                                // Cập nhật tổng giá trị giỏ hàng
                                document.querySelector(".cart-total").textContent = "$" + response.total.toFixed(2);

                                // Cập nhật subtotal của sản phẩm đã thay đổi
                                response.cart_items.forEach(function(item) {
                                    if (item.product_id == productId) {
                                        // Cập nhật subtotal cho sản phẩm
                                        document.querySelector(".subtotal_" + item.cart_id).textContent = "$" + item.subtotal.toFixed(2);
                                    }
                                });
                            }
                        }
                    };

                    xhr.send("action=" + action + "&product_id=" + productId + "&quantity=" + quantity);
                });
            });
        });
    </script>

    <script type="text/javascript">
        function confirmDelete() {
            return confirm("Are you sure you want to remove this item from your cart?");
        }
    </script>

</body>

</html>