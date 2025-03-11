<?php
include '../config.php'; // Kết nối database
include 'filterProducts.php'; // Hàm lọc sản phẩm

// Số sản phẩm mỗi trang
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Lấy các tiêu chí lọc từ URL và đảm bảo giá trị là mảng
$filters = [
    'price_min' => isset($_GET['price_min']) ? (float)$_GET['price_min'] : null,
    'price_max' => isset($_GET['price_max']) ? (float)$_GET['price_max'] : null,
    'color'     => isset($_GET['color']) && is_array($_GET['color']) ? $_GET['color'] : [],
    'size'      => isset($_GET['size']) && is_array($_GET['size']) ? $_GET['size'] : [],
    'category'  => isset($_GET['category']) && is_array($_GET['category']) ? $_GET['category'] : [],
    'brand'     => isset($_GET['brand']) && is_array($_GET['brand']) ? $_GET['brand'] : [],
];

$products = filterProducts($conn, $filters, $limit, $offset);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Cửa hàng</h1>
    <form method="GET" action="demo.php">
        <label>Giá thấp nhất: <input type="number" name="price_min" value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>"></label>
        <label>Giá cao nhất: <input type="number" name="price_max" value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>"></label>

        <fieldset>
            <legend>Màu sắc</legend>
            <label><input type="checkbox" name="color[]" value="Red" <?= in_array('Red', $filters['color']) ? 'checked' : '' ?>> Đỏ</label>
            <label><input type="checkbox" name="color[]" value="Blue" <?= in_array('Blue', $filters['color']) ? 'checked' : '' ?>> Xanh</label>
            <label><input type="checkbox" name="color[]" value="Black" <?= in_array('Black', $filters['color']) ? 'checked' : '' ?>> Đen</label>
        </fieldset>

        <fieldset>
            <legend>Kích thước</legend>
            <label><input type="checkbox" name="size[]" value="S" <?= in_array('S', $filters['size']) ? 'checked' : '' ?>> S</label>
            <label><input type="checkbox" name="size[]" value="M" <?= in_array('M', $filters['size']) ? 'checked' : '' ?>> M</label>
            <label><input type="checkbox" name="size[]" value="L" <?= in_array('L', $filters['size']) ? 'checked' : '' ?>> L</label>
        </fieldset>

        <fieldset>
            <legend>Danh mục</legend>
            <label><input type="checkbox" name="category[]" value="Backpack" <?= in_array('Backpack', $filters['category']) ? 'checked' : '' ?>> Ba lô</label>
            <label><input type="checkbox" name="category[]" value="Handbag" <?= in_array('Handbag', $filters['category']) ? 'checked' : '' ?>> Túi xách</label>
        </fieldset>

        <fieldset>
            <legend>Thương hiệu</legend>
            <label><input type="checkbox" name="brand[]" value="Nike" <?= in_array('Nike', $filters['brand']) ? 'checked' : '' ?>> Nike</label>
            <label><input type="checkbox" name="brand[]" value="Adidas" <?= in_array('Adidas', $filters['brand']) ? 'checked' : '' ?>> Adidas</label>
        </fieldset>

        <button type="submit">Lọc</button>
    </form>
    <div class="product-list">
        <?php if (empty($products)): ?>
            <p>Không tìm thấy sản phẩm nào!</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                    <p>Giá: <?= number_format($product['price'], 0, ',', '.') ?> VND</p>
                    <p>Thương hiệu: <?= htmlspecialchars($product['brand']) ?></p>
                    <p>Danh mục: <?= htmlspecialchars($product['category']) ?></p>
                    <p>Màu sắc: <?= htmlspecialchars($product['color']) ?></p>
                    <p>Kích thước: <?= htmlspecialchars($product['size']) ?></p>
                    <p>Đánh giá: <?= number_format($product['rating'], 1) ?> ★ (<?= $product['reviews'] ?> đánh giá)</p>
                    <a href="product_detail.php?id=<?= $product['id'] ?>">Xem chi tiết</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">« Trước</a>
        <?php endif; ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Tiếp »</a>
    </div>
</body>

</html>