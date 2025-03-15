<?php
session_start();
require '../config.php';

echo "123";
// Kiểm tra đăng nhập
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'] ?? null;
$total_price = 0;

// Kiểm tra phương thức thanh toán
if (!$payment_method) {
    die("Please select a payment method.");
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Tạo đơn hàng
    $sql_order = "INSERT INTO orders (user_id, status) VALUES (?, 'pending')";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $user_id);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;

    // Lấy sản phẩm trong giỏ hàng
    $sql_cart = "SELECT product_id, quantity, (SELECT price FROM products WHERE id = cart.product_id) AS price 
                 FROM cart WHERE user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    while ($row = $result_cart->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $total_price += $price * $quantity;

        // Thêm vào bảng order_items
        $sql_order_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_order_item = $conn->prepare($sql_order_item);
        $stmt_order_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt_order_item->execute();
    }

    // Lưu thông tin thanh toán
    $sql_payment = "INSERT INTO payments (order_id, amount, status, payment_method) VALUES (?, ?, 'pending', ?)";
    $stmt_payment = $conn->prepare($sql_payment);
    $stmt_payment->bind_param("ids", $order_id, $total_price, $payment_method);
    $stmt_payment->execute();

    // Xóa giỏ hàng sau khi đặt hàng
    $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param("i", $user_id);
    $stmt_clear_cart->execute();

    // Hoàn tất transaction
    $conn->commit();

    // Chuyển hướng đến trang xác nhận
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();

} catch (Exception $e) {
    // Rollback nếu có lỗi
    $conn->rollback();
    die("Error processing order: " . $e->getMessage());
}
?>
