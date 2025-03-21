<?php
require '../config.php';

// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

// Kiểm tra phương thức yêu cầu
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../pages/checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy và làm sạch dữ liệu từ form
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';

// Kiểm tra dữ liệu đầu vào
$errors = [];
if (empty($phone) || !preg_match("/^[0-9]{10,15}$/", $phone)) {
    $errors[] = "Invalid phone number.";
}
if (empty($address) || strlen($address) > 255) {
    $errors[] = "Address is required and must be under 255 characters.";
}
if (empty($city) || !preg_match("/^[a-zA-Z\s]+$/", $city)) {
    $errors[] = "City name is invalid.";
}
if (!in_array($payment_method, ['cod', 'bank_transfer', 'credit_card'])) {
    $errors[] = "Invalid payment method.";
}

// Nếu có lỗi, chuyển hướng về checkout.php với thông báo
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../pages/checkout.php");
    exit();
}

// Bắt đầu giao dịch
$conn->begin_transaction();

try {
    // Lấy danh sách sản phẩm trong giỏ hàng
    $sql_cart = "SELECT c.product_id, p.price, c.quantity, p.name 
                 FROM cart c 
                 JOIN products p ON c.product_id = p.id 
                 WHERE c.user_id = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    $cart_items = [];
    $total_price = 0;

    while ($row = $result_cart->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['price'] * $row['quantity'];
    }

    if (empty($cart_items)) {
        throw new Exception("Your cart is empty.");
    }

    // Tạo đơn hàng trong `orders`
    $sql_order = "INSERT INTO orders (user_id, status) VALUES (?, 'pending')";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $user_id);
    $stmt_order->execute();
    $order_id = $conn->insert_id;

    // Lưu từng sản phẩm vào `order_items`
    $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($cart_items as $item) {
        $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_item->execute();
    }

    // Lưu thông tin thanh toán vào `payments`
    $sql_payment = "INSERT INTO payments (order_id, amount, status, payment_method) VALUES (?, ?, 'pending', ?)";
    $stmt_payment = $conn->prepare($sql_payment);
    $stmt_payment->bind_param("ids", $order_id, $total_price, $payment_method);
    $stmt_payment->execute();

    // Xóa giỏ hàng sau khi đặt hàng
    $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param("i", $user_id);
    $stmt_clear_cart->execute();

    // Cập nhật thông tin người dùng (phone, address, city)
    $sql_update_user = "UPDATE users SET phone = ?, address = ?, city = ? WHERE id = ?";
    $stmt_update_user = $conn->prepare($sql_update_user);
    $stmt_update_user->bind_param("sssi", $phone, $address, $city, $user_id);
    $stmt_update_user->execute();

    // Commit giao dịch
    $conn->commit();

    // Chuyển hướng đến trang xác nhận đơn hàng
    $_SESSION['success'] = "Order placed successfully! Order ID: $order_id";
    header("Location: ../pages/order_confirmation.php?order_id=$order_id");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../pages/checkout.php");
    exit();
} finally {
    // Đóng các statement
    $stmt_cart->close();
    $stmt_order->close();
    $stmt_item->close();
    $stmt_payment->close();
    $stmt_clear_cart->close();
    $stmt_update_user->close();
    $conn->close();
}
