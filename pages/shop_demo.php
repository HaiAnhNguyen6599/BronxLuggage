<?php

// shop.php

require_once '../config.php';  // Kết nối database
require_once '../functions.php';  // Kết nối file functions.php

// Lấy tham số giới tính từ URL
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';

// Lấy sản phẩm theo giới tính
if ($gender) {
    $products = get_products_by_gender($gender);
} else {
    // Nếu không có giới tính, có thể lấy tất cả sản phẩm
    $products = get_products_by_gender('male');  // Hoặc có thể lấy tất cả mà không phân biệt giới tính
}

// Hiển thị sản phẩm
while ($product = $products->fetch_assoc()) {
    echo '<div class="product">';
    echo '<img src="../' . $product['image_url'] . '" alt="' . $product['name'] . '" />';
    echo '<h2>' . $product['name'] . '</h2>';
    echo '<p>' . $product['description'] . '</p>';
    echo '<p>' . $product['price'] . '</p>';
    echo '</div>';
}
?>