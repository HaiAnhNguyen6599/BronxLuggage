<?php
require "../config.php";

if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['order_delete_error'] = "Unauthorized access.";
    header("Location: manage_orders.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['order_delete_error'] = "Invalid order ID.";
    header("Location: manage_orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Xóa tất cả sản phẩm trong đơn hàng trước
$delete_items_stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
$delete_items_stmt->bind_param("i", $order_id);
$delete_items_stmt->execute();
$delete_items_stmt->close();

// Xóa đơn hàng
$delete_order_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$delete_order_stmt->bind_param("i", $order_id);

if ($delete_order_stmt->execute()) {
    $_SESSION['order_delete_success'] = "Order deleted successfully.";
} else {
    $_SESSION['order_delete_error'] = "Failed to delete order.";
}

$delete_order_stmt->close();
header("Location: manage_orders.php");
exit();
