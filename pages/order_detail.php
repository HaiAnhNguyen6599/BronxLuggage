<?php
require "../config.php"; // File kết nối database
require_once '../functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

// Lấy ID đơn hàng từ URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Kiểm tra đơn hàng tồn tại và quyền truy cập
$stmt = $conn->prepare("SELECT user_id, created_at, status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Order not found!";
    exit();
}

$order = $result->fetch_assoc();
if ($order['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin') {
    echo "You do not have permission to view this order!";
    exit();
}

// Lấy chi tiết sản phẩm trong đơn hàng
$stmt = $conn->prepare("
    SELECT oi.product_id, p.name, p.price, oi.quantity, pi.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

// Tính tổng tiền và lưu danh sách sản phẩm
$total = 0;
$items = [];
while ($item = $items_result->fetch_assoc()) {
    $item_total = $item['price'] * $item['quantity'];
    $total += $item_total;
    $item['item_total'] = $item_total;
    $items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags và CSS -->
    <?php include '../includes/head.php'; ?>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>
    <!-- Topbar -->
    <?php include '../includes/topbar.php'; ?>

    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Breadcrumb -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="../pages/account.php">Account</a>
                    <span class="breadcrumb-item active">Order Details</span>
                </nav>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-uppercase mb-4">Order Details</h2>
                        <!-- <p><strong>Order ID:</strong> <?= $order_id ?></p> -->
                        <p><strong>Order Date:</strong> <?= date('d-m-Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Status:</strong> <?= ucfirst($order['status']); ?></p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Product</th>
                                        <!-- <th>Image</th> -->
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            
                                            <td><a href="product.php?id=<?php echo htmlspecialchars($item['product_id'] ?? ''); ?>" style="color: black;""><?= htmlspecialchars($item['name']) ?></a></td>
                                            <!-- <td><img src="../img/products/<?= $item['image_url'] ?: 'default.jpg' ?>" alt="Product Image" width="50"></td> -->
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= number_format($item['price'], 2) ?> $</td>
                                            <td><?= number_format($item['item_total'], 2) ?> $</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <h3 class="mt-3">Total Amount: <?= number_format($total, 2) ?> $</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="../js/main.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>