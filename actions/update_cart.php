<?php
session_start();
require_once "../config.php"; // Kết nối database



// Kiểm tra người dùng đã đăng nhập
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(["status" => "error", "message" => "User not logged in"]);
//     exit();
// }

// $user_id = $_SESSION['user_id'];

// // Kiểm tra dữ liệu đầu vào
// if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cart_id'], $_POST['quantity'])) {
//     $cart_id = intval($_POST['cart_id']);
//     $quantity = intval($_POST['quantity']);

//     if ($quantity < 1) {
//         echo json_encode(["status" => "error", "message" => "Quantity must be at least 1"]);
//         exit();
//     }

//     // Cập nhật số lượng trong giỏ hàng
//     $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
//     $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

//     if ($stmt->execute()) {
//         // Lấy lại tổng tiền của sản phẩm sau khi cập nhật
//         $stmt = $conn->prepare("SELECT price FROM products p JOIN cart c ON p.id = c.product_id WHERE c.id = ?");
//         $stmt->bind_param("i", $cart_id);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $product = $result->fetch_assoc();
//         $total_price = $product ? $product['price'] * $quantity : 0;

//         echo json_encode(["status" => "success", "new_total" => number_format($total_price, 2)]);
//     } else {
//         echo json_encode(["status" => "error", "message" => "Failed to update cart"]);
//     }

//     $stmt->close();
// } else {
//     echo json_encode(["status" => "error", "message" => "Invalid request"]);
// }


if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        $quantity = 1;
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update cart."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}


?>
