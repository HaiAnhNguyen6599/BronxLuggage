<?php
require '../config.php';



// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     session_start();

//     if (!isset($_SESSION['user_id'])) {
//         header("Location: login.php");
//         exit();
//     }

//     $user_id = $_SESSION['user_id'];
//     $phone = $_POST['phone'];
//     $address = $_POST['address'];
//     $city = $_POST['city'];
//     $payment_method = $_POST['payment_method'];

//     // Lấy danh sách sản phẩm trong giỏ hàng
//     $sql_cart = "SELECT cart.product_id, products.price, cart.quantity 
//                  FROM cart 
//                  JOIN products ON cart.product_id = products.id 
//                  WHERE cart.user_id = ?";
//     $stmt_cart = $conn->prepare($sql_cart);
//     $stmt_cart->bind_param("i", $user_id);
//     $stmt_cart->execute();
//     $result_cart = $stmt_cart->get_result();

//     $cart_items = [];
//     $total_price = 0;

//     while ($row = $result_cart->fetch_assoc()) {
//         $cart_items[] = $row;
//         $total_price += $row['price'] * $row['quantity'];
//     }

//     if (empty($cart_items)) {
//         die("Error: Your cart is empty.");
//     }

//     // Tạo đơn hàng trong `orders` (Tự động tạo ID)
//     $sql_order = "INSERT INTO orders (user_id, status) VALUES (?, 'pending')";
//     $stmt_order = $conn->prepare($sql_order);
//     $stmt_order->bind_param("i", $user_id);
//     $stmt_order->execute();

//     // Lấy order_id vừa tạo
//     $order_id = $conn->insert_id;
//     echo "ID của bản ghi vừa chèn là: " . $inserted_id;

//     // Lưu từng sản phẩm vào `order_items`
//     $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
//     $stmt_item = $conn->prepare($sql_item);

//     foreach ($cart_items as $item) {
//         $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
//         $stmt_item->execute();
//     }

//     // Lưu thông tin thanh toán vào `payments`
//     $sql_payment = "INSERT INTO payments (order_id, amount, status, payment_method) VALUES (?, ?, 'pending', ?)";
//     $stmt_payment = $conn->prepare($sql_payment);
//     $stmt_payment->bind_param("ids", $order_id, $total_price, $payment_method);
//     $stmt_payment->execute();

//     // Xóa giỏ hàng sau khi đặt hàng
//     $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
//     $stmt_clear_cart = $conn->prepare($sql_clear_cart);
//     $stmt_clear_cart->bind_param("i", $user_id);
//     $stmt_clear_cart->execute();

//     // Chuyển hướng đến trang xác nhận đơn hàng
//     header("Location: order_confirmation.php?order_id=$order_id");
//     exit();
// }




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
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: checkout.php");
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
if (empty($phone)) {
    $errors[] = "Phone number is required.";
} elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
    $errors[] = "Invalid phone number format.";
}
if (empty($address)) {
    $errors[] = "Address is required.";
}
if (empty($city)) {
    $errors[] = "City is required.";
}
if (!in_array($payment_method, ['cod', 'bank_transfer', 'credit_card'])) {
    $errors[] = "Invalid payment method.";
}

// Nếu có lỗi, chuyển hướng về checkout.php với thông báo
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: checkout.php");
    exit();
}

// Bắt đầu giao dịch
$conn->begin_transaction();

try {
    // Lấy danh sách sản phẩm trong giỏ hàng và kiểm tra tồn kho
    $sql_cart = "SELECT c.product_id, p.price, c.quantity, p.inventory, p.name 
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
        // Kiểm tra tồn kho
        if ($row['quantity'] > $row['inventory']) {
            throw new Exception("Not enough stock for product: " . htmlspecialchars($row['name']) . ". Available: " . $row['inventory'] . ", Requested: " . $row['quantity']);
        }
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

    // Lấy order_id vừa tạo
    $order_id = $conn->insert_id;

    // Lưu từng sản phẩm vào `order_items` và cập nhật tồn kho
    $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    $sql_update_inventory = "UPDATE products SET inventory = inventory - ? WHERE id = ?";
    $stmt_update_inventory = $conn->prepare($sql_update_inventory);

    foreach ($cart_items as $item) {
        // Lưu chi tiết đơn hàng
        $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_item->execute();

        // Cập nhật tồn kho
        $stmt_update_inventory->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt_update_inventory->execute();
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
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
} catch (Exception $e) {
    // Rollback giao dịch nếu có lỗi
    $conn->rollback();

    // Lưu thông báo lỗi vào session và chuyển hướng
    $_SESSION['error'] = $e->getMessage();
    header("Location: checkout.php");
    exit();
} finally {
    // Đóng các statement
    if (isset($stmt_cart)) $stmt_cart->close();
    if (isset($stmt_order)) $stmt_order->close();
    if (isset($stmt_item)) $stmt_item->close();
    if (isset($stmt_update_inventory)) $stmt_update_inventory->close();
    if (isset($stmt_payment)) $stmt_payment->close();
    if (isset($stmt_clear_cart)) $stmt_clear_cart->close();
    if (isset($stmt_update_user)) $stmt_update_user->close();
    $conn->close();
}
