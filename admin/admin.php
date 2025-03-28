<?php
// Include các file cần thiết
require "../config.php";
require_once '../functions.php';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../account/login.php");
    exit();
}

// Lấy thông tin người dùng từ session
$name = $_SESSION['name'] ?? 'Admin';

// Lấy số liệu thống kê cơ bản
$total_users = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php'; ?>
</head>

<body>
    <?php include '../includes/topbar.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Admin Dashboard</h2>
                        <p class="text-center">Welcome, <?= htmlspecialchars($name) ?>! Manage your e-commerce system
                            here.</p>

                        <!-- Thống kê nhanh -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Users</h5>
                                        <p class="card-text"><?= htmlspecialchars($total_users) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Products</h5>
                                        <p class="card-text">
                                            <?php
                                            $total_products = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
                                            echo htmlspecialchars($total_products) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Orders</h5>
                                        <?php
                                        $total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
                                        echo htmlspecialchars($total_orders) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Liên kết đến các trang quản lý -->
                        <div class="row align-items-stretch">
                            <div class="col-md-4">
                                <div class="card mb-3 h-100 d-flex flex-column">
                                    <div class="card-body text-center flex-grow-1">
                                        <h5 class="card-title">Manage Users</h5>
                                        <p class="card-text">View, edit, or delete user accounts.</p>
                                    </div>
                                    <div class="card-footer bg-white border-0 text-center">
                                        <a href="manage_users.php" class="btn btn-dark w-100">Go to Users</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 h-100 d-flex flex-column">
                                    <div class="card-body text-center flex-grow-1">
                                        <h5 class="card-title">Manage Products</h5>
                                        <p class="card-text">Add, edit, or remove products.</p>
                                    </div>
                                    <div class="card-footer bg-white border-0 text-center">
                                        <a href="manage_products.php" class="btn btn-dark w-100">Go to Products</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3 h-100 d-flex flex-column">
                                    <div class="card-body text-center flex-grow-1">
                                        <h5 class="card-title">Manage Orders</h5>
                                        <p class="card-text">View and update order statuses.</p>
                                    </div>
                                    <div class="card-footer bg-white border-0 text-center">
                                        <a href="manage_orders.php" class="btn btn-dark w-100">Go to Orders</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút đăng xuất -->
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-danger" onclick="confirmLogout()">Logout</a>
                        </div>
                        <script>
                            function confirmLogout() {
                                if (confirm("Are you sure you want to logout?")) { // Show confirmation dialog
                                    window.location.href = '../account/logout.php'; // Redirect to logout page if "OK" is clicked
                                }
                                // If "Cancel" is clicked, do nothing
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $conn->close(); ?>