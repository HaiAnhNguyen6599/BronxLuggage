<?php
require "../config.php";
require_once '../functions.php';

if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['order_error'] = "Unauthorized access.";
    header("Location: ../admin/manage_orders.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['order_error'] = "Invalid order ID.";
    header("Location: ../admin/manage_orders.php");
    exit();
}

$order_id = (int) $_GET['id'];

// Cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);

    if ($update_stmt->execute()) {
        $_SESSION['order_update_success'] = "Order status updated successfully.";
    } else {
        $_SESSION['order_update_error'] = "Failed to update order status.";
    }
    $update_stmt->close();
    header("Location: ../admin/manage_orders.php");
    exit();
}

// Lấy thông tin đơn hàng
$order_sql = "SELECT orders.id, users.name AS customer_name, users.email, users.phone, users.address, orders.status, orders.created_at
              FROM orders
              JOIN users ON orders.user_id = users.id
              WHERE orders.id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['order_error'] = "Order not found.";
    header("Location: ../admin/manage_orders.php");
    exit();
}

// Lấy danh sách sản phẩm trong đơn hàng
$item_sql = "SELECT products.id as product_id, products.name AS product_name, order_items.quantity, order_items.price 
             FROM order_items
             JOIN products ON order_items.product_id = products.id
             WHERE order_items.order_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$items = $item_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$order_stmt->close();
$item_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="../assets/css/order_detail.css">
</head>

<body>
    <?php include '../includes/topbar.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center">Order Detail #<?= $order['id'] ?></h2>

                <?php if (isset($_SESSION['order_success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['order_success'] ?></div>
                    <?php unset($_SESSION['order_success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['order_error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['order_error'] ?></div>
                    <?php unset($_SESSION['order_error']); ?>
                <?php endif; ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        setTimeout(function () {
                            document.querySelectorAll(".alert").forEach(function (alert) {
                                alert.style.transition = "opacity 0.5s ease-out";
                                alert.style.opacity = "0";
                                setTimeout(() => alert.remove(), 500);
                            });
                        }, 3000); // 3 giây sau tự động ẩn
                    });
                </script>

                <!-- Thông tin khách hàng -->
                <div class="mb-3">
                    <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                    <p><strong>Status:</strong>
                        <span class="badge badge-<?= getStatusClass($order['status']) ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </p>
                    <p><strong>Created At:</strong> <?= $order['created_at'] ?></p>
                </div>

                <!-- Form cập nhật trạng thái -->
                <form method="POST" onsubmit="return confirm('Are you sure you want to update the order status?');">
                    <div class="form-group">
                        <label for="status"><strong>Update Status:</strong></label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Pending
                            </option>
                            <option value="processing" <?= ($order['status'] == 'processing') ? 'selected' : '' ?>>
                                Processing</option>
                            <option value="shipped" <?= ($order['status'] == 'shipped') ? 'selected' : '' ?>>Shipped
                            </option>
                            <option value="delivered" <?= ($order['status'] == 'delivered') ? 'selected' : '' ?>>Delivered
                            </option>
                            <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>

                <!-- Bảng sản phẩm -->
                <h4 class="mt-4">Order Items</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach ($items as $item): ?>
                            <?php $subtotal = $item['quantity'] * $item['price']; ?>
                            <tr>
                                <td><a class="h6 text-decoration-none text-truncate"
                                        href="../pages/product.php?id=<?php echo $item['product_id'] ?>"><?php echo $item['product_name'] ?></a>
                                </td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>$<?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <?php $total += $subtotal; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong>$<?= number_format($total, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>

                <a href="../admin/manage_orders.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

</html>

<?php
function getStatusClass($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>