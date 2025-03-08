<?php
require_once 'config.php'; // Chỉ gọi một lần

function getFeaturedProducts() {
    global $conn; // Sử dụng kết nối từ config.php

    $sql = "SELECT 
                p.id,
                p.name,
                COALESCE(pi.image_url, 'default.jpg') AS img,
                COALESCE(AVG(f.rating), 0) AS rating,
                COUNT(f.id) AS reviews,
                MIN(pv.price) AS price
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
            LEFT JOIN feedback f ON p.id = f.product_id
            LEFT JOIN product_variants pv ON p.id = pv.product_id
            GROUP BY p.id, p.name, pi.image_url";
    
    $result = $conn->query($sql);
    $products = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    return $products;
}
?>
