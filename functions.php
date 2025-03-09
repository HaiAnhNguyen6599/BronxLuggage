<?php
require_once 'config.php'; // Chỉ gọi một lần

// Hàm lấy danh mục sản phẩm
function getCategories($conn) {
    $sql = "
        SELECT c.id, c.name, COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name
        HAVING product_count > 0  -- Chỉ lấy danh mục có sản phẩm
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
                COALESCE(
                    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1), 
                    'default.jpg'
                ) AS img,
                COALESCE(
                    (SELECT MIN(price) FROM product_variants WHERE product_id = p.id), 
                    0
                ) AS price
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

// functions.php

function get_products_by_gender($gender) {
    global $conn;  // Sử dụng kết nối từ config.php

    // Xây dựng câu truy vấn SQL để lấy sản phẩm theo giới tính
    $sql = "SELECT * 
            FROM products p
            left join product_images pi on p.id = pi.product_id
            left join product_variants pv on p.id = pv.product_id
            WHERE gender = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gender);  // "s" là kiểu dữ liệu string
    $stmt->execute();

    // Lấy kết quả và trả về
    $result = $stmt->get_result();
    return $result;
}


?>

