<?php
require "../config.php"; // File kết nối database

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Vui lòng đăng nhập!"]));
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = max(1, intval($_POST['quantity']));

// Kiểm tra sản phẩm đã tồn tại chưa
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Nếu sản phẩm đã có trong giỏ, cộng dồn số lượng
    $new_quantity = $row['quantity'] + $quantity;
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
    $stmt->execute();
} else {
    // Nếu chưa có, thêm mới
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
}

echo json_encode(["success" => true]);
exit();
