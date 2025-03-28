<?php
require "../config.php";
require_once '../functions.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../account/login.php");
    exit();
}

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Cấu hình phân trang
$limit = 5; // Số user trên mỗi trang
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;

// Lấy tổng số user
$total_query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_users = $total_row['total'];
$total_pages = ceil($total_users / $limit);

// Truy vấn danh sách user với phân trang
$query = "SELECT id, name, email, phone, address, city, role 
          FROM users WHERE role = 'customer' 
          ORDER BY updated_at DESC 
          LIMIT $limit OFFSET $offset";
$users = $conn->query($query);

// Số dòng của users
$num_rows = $users->num_rows;
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
        <?php
        $alerts = [
            'user_add_success' => 'alert-success',
            'user_update_success' => 'alert-success',
            'user_delete_success' => 'alert-success',
            'user_add_error' => 'alert-danger',
            'user_update_error' => 'alert-danger',
            'user_delete_error' => 'alert-danger'
        ];

        foreach ($alerts as $key => $class) {
            if (isset($_SESSION[$key])) {
                echo '<div class="alert ' . $class . ' fade show" id="alert-' . $key . '">' . htmlspecialchars($_SESSION[$key]) . '</div>';
                unset($_SESSION[$key]); // Xóa session ngay sau khi hiển thị
            }
        }
        ?>

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
        <div class="row justify-content-center mt-5">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Manage Customers</h2>

                        <?php if ($num_rows == 0): ?>
                            <div class="alert alert-warning text-center">
                                No customers found in the database.
                            </div>
                        <?php else: ?>
                            <p class="text-center">Found <?= $total_users ?> customer(s).</p>
                        <?php endif; ?>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($num_rows > 0): ?>
                                    <?php
                                    $index = ($page - 1) * $limit + 1;
                                    while ($row = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $index++ ?></td>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['phone'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($row['address'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($row['city'] ?? 'N/A') ?></td>
                                            <td><?= ucfirst(htmlspecialchars($row['role'])) ?></td>
                                            <td>
                                                <a href="../admin/edit_user.php?id=<?= $row['id'] ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="../admin/delete_user.php?id=<?= $row['id'] ?>"
                                                    class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No Users Found!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>

                        <div class="text-center">
                            <a href="../admin/admin.php" class="btn btn-dark">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$users->free();
$conn->close();
?>