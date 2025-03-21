<?php
require "../config.php"; // File kết nối database
require_once '../functions.php';

// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}
$cart_items = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Truy vấn lấy sản phẩm trong giỏ hàng của user, chỉ lấy ảnh có is_primary = TRUE
    $stmt = $conn->prepare("
        SELECT c.id as cart_id, c.product_id, c.quantity, p.name, p.price, pi.image_url 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        LEFT JOIN product_images pi ON c.product_id = pi.product_id AND pi.is_primary = TRUE
        WHERE c.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }

    $stmt->close();
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

        /* CSS for Continue Shopping */
        .btn-secondary {
            background-color: #6c757d;
            /* Màu xám Bootstrap */
            border: none;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            /* Màu tối hơn khi hover */
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
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="../pages/shop.php">Shop</a>
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
                                        <img src="../<?php echo htmlspecialchars($item['image_url'] ?? 'img/default.jpg'); ?>"
                                            alt="" style="width: 50px;">
                                        <a href="product.php?id=<?php echo htmlspecialchars($item['product_id'] ?? ''); ?>"
                                            style="color: black;">
                                            <?php echo htmlspecialchars($item['name'] ?? 'Unknown Product'); ?>

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
                                            <input type="number" min="1"
                                                class="form-control form-control-sm bg-secondary border-0 text-center quantity-input"
                                                value="<?php echo htmlspecialchars($item['quantity'] ?? 1); ?>">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-plus">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- <td class="align-middle">$<span
                                            class="item-total"><?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2); ?></span>
                                    </td> -->
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-danger btn-remove"
                                            data-cart-id="<?php echo htmlspecialchars($item['cart_id'] ?? ''); ?>">
                                            <?php echo $item['cart_id'] ?>
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="text-left mt-3">
                    <a href="../pages/shop.php" class="btn btn-secondary px-4 py-2">
                        <i class="fa fa-arrow-left mr-1"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart
                        Summary</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 id="total-price">$</h5>
                        </div>
                        <a href="<?php echo $user_id > 0 ? 'checkout.php' : '../account/login.php'; ?>"
                            class="btn btn-block btn-primary font-weight-bold my-3 py-3">Proceed To Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>


        $(document).ready(function () {
            updateCartTotal(); // Tính tổng tiền ngay khi tải trang

            $(".btn-minus, .btn-plus").click(function () {
                let row = $(this).closest("tr");
                let cartId = row.attr("data-cart-id");
                let quantityInput = row.find(".quantity-input");
                let newQuantity = parseInt(quantityInput.val());

                if ($(this).hasClass("btn-minus")) {
                    newQuantity = Math.max(1, newQuantity - 1);
                } else if ($(this).hasClass("btn-plus")) {
                    newQuantity += 1;
                }

                quantityInput.val(newQuantity);
                updateCart(cartId, newQuantity, row);
            });

            $(".quantity-input").on("keyup", function (event) {
                if (event.key === "Enter") {
                    let row = $(this).closest("tr");
                    let cartId = row.attr("data-cart-id");
                    let newQuantity = parseInt($(this).val());

                    if (isNaN(newQuantity) || newQuantity < 1) {
                        newQuantity = 1;
                        $(this).val(newQuantity);
                    }

                    updateCart(cartId, newQuantity, row);
                }
            });

            function updateCart(cartId, quantity, row) {
                $.post("../actions/update_cart.php", {
                    cart_id: cartId,
                    quantity: quantity
                }, function (response) {
                    let data = JSON.parse(response);
                    if (data.status === "success") {
                        updateCartTotal(); // Cập nhật tổng tiền sau khi update thành công
                    } else {
                        alert(data.message);
                    }
                });
            }

            $(".btn-remove").click(function () {
                let cartId = $(this).data("cart-id");
                let row = $(this).closest("tr");

                if (confirm("Are you sure you want to remove this item from your cart?")) {
                    $.post("../actions/remove_from_cart.php", { cart_id: cartId }, function (response) {
                        let data = JSON.parse(response);
                        if (data.status === "success") {
                            row.remove(); // Xóa sản phẩm khỏi giao diện
                            location.reload(); // Load lại trang cart
                        } else {
                            alert(data.message);
                        }
                    });
                }
            });

            function updateCartTotal() {
                let total = 0;
                $(".quantity-input").each(function () {
                    let row = $(this).closest("tr");
                    let price = parseFloat(row.find("td:nth-child(2)").text().replace("$", ""));
                    let quantity = parseInt($(this).val()) || 1;
                    total += price * quantity;
                });
                $("#total-price").text("$" + total.toFixed(2));
            }
        });



    </script>
    <?php include '../includes/footer.php'; ?>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    <script src="js/main.js"></script>
</body>

</html>