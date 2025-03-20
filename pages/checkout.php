<?php
require "../config.php";
require_once '../functions.php';

// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin user
$stmt = $conn->prepare("SELECT name, email, phone, address, city FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Lấy sản phẩm trong giỏ hàng
$stmt = $conn->prepare("SELECT p.id, p.name, c.quantity, p.price 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

$total_price = 0;



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php' ?>
</head>

<body>

    <?php include '../includes/topbar.php' ?>
    <?php include '../includes/navbar.php' ?>
    <?php
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
}
?>
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Checkout</span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tao validate cho form -->
    <div class="container-fluid">
        <form id="billing-form" action="../actions/process_checkout.php" method="POST">
            <div class="row px-xl-5">
                <div class="col-lg-8">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Billing Address</span></h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Name</label>
                                <input class="form-control" type="text"
                                    value="<?= htmlspecialchars($user['name'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email</label>
                                <input class="form-control" type="email"
                                    value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Mobile No</label>
                                <input class="form-control" type="text" name="phone"
                                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address</label>
                                <input class="form-control" type="text" name="address"
                                    value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>City</label>
                                <input class="form-control" type="text" name="city"
                                    value="<?= htmlspecialchars($user['city'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    <!-- Javascirpt validate -->
                    <!-- <script>
                    document.getElementById('billing-form').addEventListener('submit', function(event) {
                        let phone = document.querySelector('input[name="phone"]').value.trim();
                        let address = document.querySelector('input[name="address"]').value.trim();
                        let city = document.querySelector('input[name="city"]').value.trim();
                        let phoneRegex = /^[0-9]{10,15}$/; // Chỉ nhận số từ 10-15 chữ số

                        let errors = [];

                        if (!phoneRegex.test(phone)) {
                            errors.push("Số điện thoại không hợp lệ. Vui lòng nhập 10-15 chữ số.");
                        }

                        if (address.length < 5) {
                            errors.push("Địa chỉ phải có ít nhất 5 ký tự.");
                        }

                        if (city.length < 2) {
                            errors.push("Tên thành phố phải có ít nhất 2 ký tự.");
                        }

                        if (errors.length > 0) {
                            event.preventDefault(); // Ngăn chặn submit nếu có lỗi
                            alert(errors.join("\n"));
                        }
                    });
                    </script> -->

                </div>
                <div class="col-lg-4">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Order Summary</span></h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="border-bottom">
                            <h6 class="mb-3">Products</h6>
                            <?php if (empty($cart_items)): ?>
                            <p>Your cart is empty.</p>
                            <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between">
                                <p><?= htmlspecialchars($item['name'] ?? 'Unknown Product') ?> x
                                    <?= htmlspecialchars($item['quantity'] ?? 0) ?></p>
                                <p>$<?= number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 2) ?></p>
                            </div>
                            <?php $total_price += ($item['price'] ?? 0) * ($item['quantity'] ?? 0); ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="pt-2">
                            <div class="d-flex justify-content-between mt-2">
                                <h5>Total</h5>
                                <h5>$<?= number_format($total_price, 2) ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <h5 class="section-title position-relative text-uppercase mb-3"><span
                                class="bg-secondary pr-3">Payment</span></h5>
                        <div class="bg-light p-30">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="cod" name="payment_method"
                                        value="cod" required checked>
                                    <label class="custom-control-label" for="cod">Cash on Delivery</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="bank_transfer"
                                        name="payment_method" value="bank_transfer">
                                    <label class="custom-control-label" for="bank_transfer">Bank Transfer</label>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="credit_card"
                                        name="payment_method" value="credit_card">
                                    <label class="custom-control-label" for="credit_card">Credit Card</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-block btn-primary font-weight-bold py-3"
                                <?php echo empty($cart_items) ? 'disabled' : ''; ?>>Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php include '../includes/footer.php' ?>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    <script src="js/main.js"></script>
</body>

</html>