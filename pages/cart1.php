<?php
require_once "../config.php"; // Kết nối database

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Xử lý cập nhật số lượng sản phẩm bằng AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    if ($_POST['action'] == 'increase' || $_POST['action'] == 'decrease') {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    }

    // Truy vấn lại giỏ hàng
    $stmt = $conn->prepare("SELECT p.name AS product_name, p.price AS product_price, c.quantity AS product_quantity, c.id AS cart_id, c.product_id 
                            FROM cart c 
                            JOIN products p ON c.product_id = p.id 
                            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $row['subtotal'] = $row['product_price'] * $row['product_quantity'];
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'total' => $total,
        'cart_items' => $cart_items
    ]);
    exit();
}

// Lấy danh sách sản phẩm trong giỏ hàng
$stmt = $conn->prepare("SELECT p.name AS product_name, p.price AS product_price, p.inventory, c.quantity AS product_quantity, c.id AS cart_id, c.product_id 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['product_price'] * $row['product_quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
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
                                    <a href="product.php?id=<?= $item['product_id']; ?>" style="color:#6C757D;"><?= $item['product_name']; ?></a>
                                </td>
                                <td class="align-middle">$<?= number_format($item['product_price'], 2); ?></td>

                                <td class="align-middle">
                                    <div class="input-group quantity mx-auto" style="width: 100px;">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-sm btn-primary btn-minus change_quantity" data-action="decrease" data-product-id="<?= $item['product_id'] ?>" data-cart-id="<?= $item['cart_id'] ?>">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="number" class="form-control form-control-sm bg-secondary border-0 text-center" style="padding: 4px 0;"
                                            name="quantity" value="<?= $item['product_quantity']; ?>" id="quantity_<?= $item['cart_id']; ?>"
                                            min="1" max="<?= $item['inventory']; ?>" readonly>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-sm btn-primary btn-plus change_quantity" data-action="increase" data-product-id="<?= $item['product_id'] ?>" data-cart-id="<?= $item['cart_id'] ?>">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle subtotal subtotal_<?= $item['cart_id']; ?>">$<?= number_format($item['subtotal'], 2); ?></td>

                                <td class="align-middle">
                                    <form action="cart.php" method="POST" onsubmit="return confirm('Are you sure?');">
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
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 class="cart-total">$<?= number_format($total, 2); ?></h5>
                        </div>
                        <a href="checkout.php" class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->
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