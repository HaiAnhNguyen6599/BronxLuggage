<?php
session_start();
require "../config.php";
require_once '../functions.php';

$user_id = 1; // Giả định user đang đăng nhập có ID = 1

// Lấy thông tin user
$query = "SELECT phone, address, city FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Lấy sản phẩm trong giỏ hàng
$query = "SELECT p.id, p.name, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php' ?>;
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php' ?>;
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <?php include '../includes/navbar.php' ?>;
    <!-- Navbar End -->


    <!-- Breadcrumb Start -->
    <?php include '../includes/breadcumb.php' ?>;

    <!-- Breadcrumb End -->


    <!-- Checkout Start -->
    <div class="container-fluid">
        <form action="process_checkout.php" method="POST">
            <div class="row px-xl-5">
                <div class="col-lg-8">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Billing Address</span></h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="row">
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
                </div>
                <div class="col-lg-4">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Order Summary</span></h5>
                    <div class="bg-light p-30 mb-5">
                        <div class="border-bottom">
                            <h6 class="mb-3">Products</h6>
                            <?php while ($item = $cart_items->fetch_assoc()): ?>
                                <div class="d-flex justify-content-between">
                                    <p><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></p>
                                    <p>$<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                                </div>
                                <?php $total_price += $item['price'] * $item['quantity']; ?>
                            <?php endwhile; ?>
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
                                        value="cod" required>
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
                            <button type="submit" class="btn btn-block btn-primary font-weight-bold py-3">Place
                                Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Checkout End -->


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