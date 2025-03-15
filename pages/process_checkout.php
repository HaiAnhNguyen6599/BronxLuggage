<?php
require '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $user_id = $_POST['user_id'];
    $user_id = 1;
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $payment_method = $_POST['payment_method'];

    // Lấy danh sách sản phẩm trong giỏ hàng
    $sql_cart = "SELECT cart.product_id, products.price, cart.quantity 
                 FROM cart 
                 JOIN products ON cart.product_id = products.id 
                 WHERE cart.user_id = ?";
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
        die("Error: Your cart is empty.");
    }

    // Giả định order_id = n, THAY ĐỔI ĐỂ CHECK cho từng đơn hàng
    $order_id = 3;

    // Lưu đơn hàng vào bảng `orders`
    $sql_order = "INSERT INTO orders (id, user_id, status) VALUES (?, ?, 'pending')";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("ii", $order_id, $user_id);
    $stmt_order->execute();

    // Lưu từng sản phẩm vào `order_items`
    foreach ($cart_items as $item) {
        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);
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

    // Chuyển hướng đến trang xác nhận đơn hàng
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
}
?>
