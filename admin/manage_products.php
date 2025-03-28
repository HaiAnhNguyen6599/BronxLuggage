<?php
require "../config.php";
require_once '../functions.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Thiết lập số sản phẩm trên mỗi trang
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lấy tổng số sản phẩm
$total_query = $conn->query("SELECT COUNT(*) AS total FROM products");
$total_row = $total_query->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $limit);

// Truy vấn danh sách sản phẩm
$stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.gender, c.name AS category, 
                        b.name AS brand, s.name AS size, col.name AS color 
                        FROM products p 
                        JOIN categories c ON p.category_id = c.id 
                        JOIN brands b ON p.brand_id = b.id 
                        JOIN sizes s ON p.size_id = s.id 
                        JOIN colors col ON p.color_id = col.id 
                        order by p.updated_at DESC
                        LIMIT ? OFFSET ?");

$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$product = $stmt->get_result();


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
                <h2 class="card-title text-center">Manage Products</h2>
                <?php
                $alerts = [
                    'add_success' => 'alert-success',
                    'update_success' => 'alert-success',
                    'delete_success' => 'alert-success',
                    'add_error' => 'alert-danger',
                    'update_error' => 'alert-danger',
                    'delete_error' => 'alert-danger'
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
                <a href="../admin/add_product.php" class="btn btn-success mb-3">Add New Product</a>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Gender</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = ($page - 1) * $limit + 1;
                            while ($row = $product->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $index++ ?></td>
                                    <td><a class="h6 text-decoration-none text-truncate"
                                            href="../pages/product.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a>
                                    </td>
                                    <td>$<?= htmlspecialchars($row['price']) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td><?= htmlspecialchars($row['brand']) ?></td>
                                    <td><?= htmlspecialchars($row['size']) ?></td>
                                    <td><?= htmlspecialchars($row['color']) ?></td>
                                    <td><?= ucfirst(htmlspecialchars($row['gender'])) ?></td>
                                    <td>
                                        <a href="../admin/edit_product.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure to delete this Product ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>