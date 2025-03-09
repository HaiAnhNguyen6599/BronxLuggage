<?php
require_once 'config.php'; // Chỉ gọi một lần

// Hàm lấy danh mục sản phẩm
function getCategories($conn) {
    $sql = "
        SELECT c.id, c.name, COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name
        ORDER BY c.id;
    ";
    return $conn->query($sql);
}


// Hàm lấy thương hiệu sản phẩm
function getBrands($conn) {
    $sql = "
        SELECT DISTINCT b.id, b.name 
        FROM brands b
        JOIN products p ON b.id = p.brand_id
        ORDER BY b.name ASC
    ";
    return $conn->query($sql);
}

// Lấy 8 sản phẩm rating cao 
function getTopRatedProducts($limit = 8) {
    global $conn; // Sử dụng kết nối từ config.php
    
    $sql = "SELECT 
                p.id, 
                p.name, 
                COALESCE(AVG(f.rating), 0) AS rating,
                COUNT(f.id) AS reviews,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS img,
                (SELECT price FROM product_variants WHERE product_id = p.id ORDER BY price ASC LIMIT 1) AS price
            FROM products p
            LEFT JOIN feedback f ON p.id = f.product_id
            GROUP BY p.id
            ORDER BY rating DESC, reviews DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}
?>
?>
