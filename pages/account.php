<?php
require "../config.php"; // File kết nối database
require_once '../functions.php'; // File chứa các hàm bổ trợ (nếu có)

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: ../account/login.php");
    exit();
}

// Lấy thông tin người dùng từ database
$user_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, name, email, phone, address, city FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    echo "Không tìm thấy người dùng với email: " . htmlspecialchars($user_email) . ". Đăng xuất...";
    session_destroy();
    header("Refresh: 3; url=../account/login.php");
    exit();
}
$user_id = $user['id'];

// Phân trang cho Order History
$limit = 5; // Số đơn hàng mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Đếm tổng số đơn hàng
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$total_orders = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $limit);

// Lấy danh sách đơn hàng cho trang hiện tại
$orders_stmt = $conn->prepare("SELECT id, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$orders_stmt->bind_param("iii", $user_id, $limit, $offset);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <?php include '../includes/head.php'; ?>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>
                    <span class="breadcrumb-item active">My Account</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Account Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-12">
                <h2 class="text-uppercase mb-4">My Account</h2>
                <div class="row">
                    <!-- Account Information -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Account Information</h5>
                                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?: 'Not provided'; ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?: 'Not provided'; ?></p>
                                <p><strong>City:</strong> <?= htmlspecialchars($user['city']) ?: 'Not provided'; ?></p>
                                <a href="../account/edit_profile.php" class="btn btn-primary mt-2">Edit Profile</a>
                            </div>
                        </div>
                    </div>

                    <!-- Order History -->
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Order History</h5>
                                <?php if ($orders->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($order = $orders->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>#<?= $order['id']; ?></td>
                                                        <td><?= date('d-m-Y H:i', strtotime($order['created_at'])); ?></td>
                                                        <td><?= ucfirst($order['status']); ?></td>
                                                        <td><a href="order_detail.php?order_id=<?= $order['id']; ?>" class="btn btn-sm btn-primary">View Details</a></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Phân trang Bootstrap 4 -->
                                    <nav aria-label="Order History Pagination">
                                        <ul class="pagination justify-content-center mt-3">
                                            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                <?php else: ?>
                                    <p class="text-muted">No orders yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Account End -->

    <!-- Footer Start -->
    <?php include '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>

</html>

<?php
$stmt->close();
$count_stmt->close();
$orders_stmt->close();
$conn->close();
?>