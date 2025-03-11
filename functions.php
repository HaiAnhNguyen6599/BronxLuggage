<?php
require_once 'config.php'; // Chỉ gọi một lần

// Hàm lấy danh mục sản phẩm
function getCategories($conn)
{
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
function getBrands($conn)
{
    $sql = "
        SELECT DISTINCT b.id, b.name 
        FROM brands b
        JOIN products p ON b.id = p.brand_id
        ORDER BY b.name ASC
    ";
    return $conn->query($sql);
}

function getSizes($conn)
{
    $sql = "
        SELECT s.id, s.name, COUNT(p.id) as product_count
        FROM sizes s
        LEFT JOIN products p ON s.id = p.size_id
        GROUP BY s.id, s.name
        HAVING product_count > 0  -- Chỉ lấy kích thước có sản phẩm
        ORDER BY s.id;
    ";
    return $conn->query($sql);
}

function getColors($conn)
{
    $sql = "
        SELECT co.id, co.name, COUNT(p.id) as product_count
        FROM colors co
        LEFT JOIN products p ON co.id = p.color_id
        GROUP BY co.id, co.name
        HAVING product_count > 0  -- Chỉ lấy màu sắc có sản phẩm
        ORDER BY co.id;
    ";
    return $conn->query($sql);
}


// Lấy 8 sản phẩm rating cao 
function getTopRatedProducts($limit = 8)
{
    global $conn; // Sử dụng kết nối từ config.php

    $sql = "SELECT 
                p.id, 
                p.name, 
                p.price,
                COALESCE(AVG(f.rating), 0) AS rating,
                COUNT(f.id) AS reviews,
                COALESCE(
                    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 
                    'default.jpg'
                ) AS img
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


function get_products_by_gender($gender)
{
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



function countProducts($conn)
{
    $sql = "SELECT COUNT(*) AS total FROM products";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}

function getProducts($conn, $limit, $offset)
{
    $sql = "
        SELECT p.id, 
               p.name as product_name, 
               p.price, 
               b.name as brand,
               c.name as color,
               cat.name as category,
               s.name as size,
               COALESCE(AVG(f.rating), 0) AS rating, 
               COUNT(f.id) AS reviews,
               COALESCE(
                   (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 
                   'default.jpg'
               ) AS image
        FROM products p
        LEFT JOIN feedback f ON p.id = f.product_id
        LEFT JOIN brands b on p.brand_id = b.id
        LEFT JOIN colors c on p.color_id = c.id
        LEFT jOIN categories cat on p.category_id = cat.id
        LEFT JOIN sizes s on p.size_id = s.id
        GROUP BY p.id, p.name, p.price, b.name, cat.name, c.name, s.name
        LIMIT ? OFFSET ?
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_errno($stmt)) {
        die("Query Error: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}
