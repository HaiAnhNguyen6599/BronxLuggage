<?php
require "../config.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You need to Login to leave a review.";
    header("Location: ../pages/product.php?id=" . $_POST['product_id']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0; // Kiểm tra nếu rating không được chọn
    $message = trim($_POST['message']);

    // Kiểm tra dữ liệu đầu vào
    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Invalid rating value.";
        header("Location: ../pages/product.php?id=$product_id");
        exit();
    }

    if (empty($message)) {
        $_SESSION['error'] = "Please leave a review message.";
        header("Location: ../pages/product.php?id=$product_id");
        exit();
    }

    // Thêm review mới vào database
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, product_id, message, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisd", $user_id, $product_id, $message, $rating);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Lưu thông báo thành công vào session
    $_SESSION['success'] = "Thank you for your Review";
    header("Location: ../pages/product.php?id=$product_id");
    exit();
}
