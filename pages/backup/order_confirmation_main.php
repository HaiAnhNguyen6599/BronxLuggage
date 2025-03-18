<?php
session_start();
require '../config.php';

// Kiểm tra đăng nhập
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// $user_id = $_SESSION['user_id'];
// Giả định người dùng đã đăng nhập với user_id = 1
$user_id = 1;
$order_id = $_GET['order_id'] ?? null;

// Kiểm tra xem order_id có hợp lệ không
if (!$order_id) {
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
    die("Order not found.");
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
while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Order Confirmation</h2>
        <p>Thank you for your order! Your order has been successfully placed.</p>

        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
        <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($order['amount'], 2) ?></p>
        <p><strong>Payment Method:</strong> <?= ucfirst(str_replace('_', ' ', $order['payment_method'])) ?></p>
        <p><strong>Payment Status:</strong> <?= ucfirst($order['status']) ?></p>

        <h3>Order Items</h3>
        <ul>
            <?php foreach ($order_items as $item): ?>
                <li><?= htmlspecialchars($item['name']) ?> - <?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></li>
            <?php endforeach; ?>
        </ul>

        <a href="index.php" class="btn">Back to Home</a>
    </div>
</body>
</html>
