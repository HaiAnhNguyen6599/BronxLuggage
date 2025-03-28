<?php
require "../config.php";
require_once '../functions.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['order_error'] = "Unauthorized access.";
    header("Location: ../account/login.php");
    exit();
}

// Thiết lập số đơn hàng trên mỗi trang
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lấy tổng số đơn hàng
$total_query = $conn->query("SELECT COUNT(*) AS total FROM orders");
$total_row = $total_query->fetch_assoc();
$total_orders = $total_row['total'];
$total_pages = ceil($total_orders / $limit);

// Lấy danh sách đơn hàng
$sql = "SELECT orders.id, users.name AS customer_name, orders.status, orders.created_at,
        (SELECT SUM(order_items.quantity * order_items.price) FROM order_items WHERE order_items.order_id = orders.id) AS total_price
        FROM orders
        JOIN users ON orders.user_id = users.id
        ORDER BY orders.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
</head>

<body>
    <?php include '../includes/topbar.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center">Manage Orders</h2>

                <!-- Hiển thị thông báo -->
                <?php
                $alerts = [
                    'order_update_success' => 'alert-success',
                    'order_update_error' => 'alert-danger'
                ];
                foreach ($alerts as $key => $class) {
                    if (isset($_SESSION[$key])) {
                        echo '<div class="alert ' . $class . ' fade show" id="alert-' . $key . '">' . htmlspecialchars($_SESSION[$key]) . '</div>';
                        unset($_SESSION[$key]);
                    }
                }
                ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Ordered At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)) : ?>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td><?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td>$<?= number_format($order['total_price'], 2) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'processing' ? 'info' : ($order['status'] === 'shipped' ? 'primary' : ($order['status'] === 'delivered' ? 'success' : 'danger'))) ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= $order['created_at'] ?></td>
                                        <td>
                                            <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">View Detail</a>
                                            <!-- <a href="delete_order.php?id=<?= $order['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this order?');">
                                                Delete
                                            </a> -->
                                            <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this Order ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <strong>No orders found.</strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages) : ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="text-center">
                    <a href="../admin/admin.php" class="btn btn-dark">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.querySelectorAll(".alert").forEach(function(alert) {
                    alert.style.transition = "opacity 0.5s ease-out";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                });
            }, 3000);
        });
    </script>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

</html>
<?php
$stmt->close();
$conn->close();
?>