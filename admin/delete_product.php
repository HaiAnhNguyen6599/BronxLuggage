<?php
require "../config.php";


// Kiểm tra quyền admin
if (!isset($_SESSION['name']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['delete_error'] = "Unauthorized access.";
    header("Location: ../admin/manage_products.php");
    exit();
}

// Kiểm tra ID sản phẩm hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['delete_error'] = "Invalid product ID.";
    header("Location: ../admin/manage_products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Thực hiện truy vấn xóa
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $_SESSION['delete_success'] = "Product deleted successfully.";
} else {
    $_SESSION['delete_error'] = "Error deleting product: " . $stmt->error;
}

$stmt->close();
header("Location: ../admin/manage_products.php");
exit();
