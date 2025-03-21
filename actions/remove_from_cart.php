<?php
require '../config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in to remove items."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'] ?? null;

if (!$cart_id) {
    echo json_encode(["status" => "error", "message" => "Invalid cart item."]);
    exit();
}

// Xóa sản phẩm khỏi giỏ hàng
$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Item removed from cart."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to remove item."]);
}

$stmt->close();
$conn->close();
?>
