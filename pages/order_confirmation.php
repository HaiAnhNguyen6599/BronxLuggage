<?php
require '../config.php';
require_once '../functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

$user_id =  $_SESSION['user_id'];
$order_id = $_GET['order_id'] ?? null;

if (!$order_id || !is_numeric($order_id)) {
    die("Invalid order ID.");
}

// Lấy thông tin đơn hàng
$sql_order = "SELECT orders.id, orders.created_at, payments.amount, payments.status, payments.payment_method 
              FROM orders 
              JOIN payments ON orders.id = payments.order_id 
              WHERE orders.id = ? AND orders.user_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows == 0) {
    die("Order not found or you don't have permission to view this order.");
}

$order = $result_order->fetch_assoc();

// Lấy danh sách sản phẩm trong đơn hàng
$sql_items = "SELECT products.name, order_items.quantity, order_items.price 
              FROM order_items 
              JOIN products ON order_items.product_id = products.id 
              WHERE order_items.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$order_items = [];
$total_items = 0;
while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
    $total_items += $row['quantity'] * $row['price'];
}

// Đóng kết nối
$stmt_order->close();
$stmt_items->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <?php include '../includes/head.php'; ?>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <!-- Topbar -->
    <?php include '../includes/topbar.php'; ?>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="../pages/account.php">My Account</a>
                    <span class="breadcrumb-item active">Order Confirmation</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Order Confirmation Start -->
    <div class="container-fluid">
        <div class="row px-xl-5 justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-uppercase mb-4">Order Confirmation</h2>
                        <p class="text-muted">Thank you for your purchase!</p>

                        <!-- Order Details -->
                        <div class="mb-4">
                            <h4 class="mb-3">Order Details</h4>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Date:</th>
                                    <td><?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td><?= ucfirst(htmlspecialchars($order['payment_method'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span
                                            class="badge <?= $order['status'] === 'completed' ? 'badge-success' : 'badge-warning' ?>">
                                            <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td>$<?= number_format($order['amount'], 2) ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-4">
                            <h4 class="mb-3">Items Purchased</h4>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td>$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td><strong>$<?= number_format($total_items, 2) ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Buttons -->
                        <div class="text-center">
                            <a href="../pages/index.php" class="btn btn-primary">Back to Home</a>
                            <a href="../pages/order_detail.php?order_id=<?php echo $order_id?>"
                                class="btn btn-secondary ml-2">View My Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Confirmation End -->

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>



    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../js/main.js"></script>
</body>

</html>