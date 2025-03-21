<?php
session_start();
require_once "../config.php"; // Kết nối database

// Kiểm tra người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

// Kiểm tra dữ liệu đầu vào
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        $quantity = 1;
    }

    $user_id = $_SESSION['user_id'];

    // Kiểm tra sản phẩm có tồn tại không
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        header("Location: ../pages/product.php?id=$product_id");
        exit();
    }

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Nếu sản phẩm đã có, cập nhật số lượng
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    } else {
        // Nếu chưa có, thêm mới vào giỏ hàng
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product added to cart successfully.";
    } else {
        $_SESSION['error'] = "Failed to add product to cart.";
    }

    $stmt->close();
    header("Location: ../pages/product.php?id=$product_id");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../pages/index.php");
    exit();
}
?>
