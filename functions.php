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

// Lấy Sizes
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

// Lấy Màu
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

// lấy giới tính
function getGenders($conn)
{
    $sql = "
        SELECT p.gender, COUNT(p.id) as product_count
        FROM products p
        WHERE p.gender IN ('male', 'female', 'kids')
        GROUP BY p.gender
        HAVING product_count > 0  -- Chỉ lấy giới tính có sản phẩm
        ORDER BY FIELD(p.gender, 'male', 'female', 'kids');
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


// Hàm lọc chung cho shop.php
function buildFilterQuery($filters)
{
    $where_clauses = [];
    $params = [];
    $types = "";

    $filter_map = [
        'category' => ['table' => 'c', 'column' => 'name', 'type' => 's'],
        'brand' => ['table' => 'b', 'column' => 'name', 'type' => 's'],
        'color' => ['table' => 'co', 'column' => 'name', 'type' => 's'],
        'gender' => ['table' => 'p', 'column' => 'gender', 'type' => 's'],
        'size' => ['table' => 's', 'column' => 'name', 'type' => 's']
    ];

    foreach ($filter_map as $key => $data) {
        if (!empty($filters[$key])) {
            $values = is_array($filters[$key]) ? $filters[$key] : [$filters[$key]];
            $placeholders = implode(',', array_fill(0, count($values), '?'));
            $where_clauses[] = "{$data['table']}.{$data['column']} IN ($placeholders)";
            $params = array_merge($params, $values);
            $types .= str_repeat($data['type'], count($values));
        }
    }

    $where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";
    return [$where_sql, $params, $types];
}

// function lọc sản phẩm + phân trang
function getFilteredProducts($conn, $filters, $limit, $offset)
{
    list($where_sql, $params, $types) = buildFilterQuery($filters);

    $sql = "SELECT p.*, 
                COALESCE((SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 'default.jpg') AS image,
                COALESCE(AVG(f.rating), 0) AS avg_rating, 
                COUNT(f.id) AS total_reviews 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN colors co ON p.color_id = co.id
            LEFT JOIN sizes s ON p.size_id = s.id
            LEFT JOIN feedback f ON p.id = f.product_id
            $where_sql
            GROUP BY p.id
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);

    // Gắn thêm LIMIT và OFFSET vào tham số
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function getTotalProducts($conn, $filters)
{
    $total_products = 0;
    list($where_sql, $params, $types) = buildFilterQuery($filters);
    
    $sql = "SELECT COUNT(DISTINCT p.id) FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN colors co ON p.color_id = co.id
            LEFT JOIN sizes s ON p.size_id = s.id
            $where_sql";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $stmt->bind_result($total_products);
    $stmt->fetch();
    $stmt->close();

    return $total_products ?? 0;
}


// product.php

function getProductById($conn, $product_id) {
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.description, c.name AS category, b.name AS brand, 
               p.size_id, s.name AS size, p.color_id, col.name AS color, 
               pi.image_url, p.price, p.gender, p.inventory
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN sizes s ON p.size_id = s.id
        LEFT JOIN colors col ON p.color_id = col.id
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE p.id = ?
    ");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getProductBySizes($conn) {
    $stmt = $conn->prepare("SELECT id, name FROM sizes");
    $stmt->execute();
    $result = $stmt->get_result();
    $sizes = [];
    while ($row = $result->fetch_assoc()) {
        $sizes[] = $row;
    }
    return $sizes;
}

// function getProductByBrands($conn) {
//     $stmt = $conn->prepare("SELECT id, name FROM colors");
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $brands = "";
//     while ($row = $result->fetch_assoc()) {
//         $colors[] = $row;
//     }
//     return $brands;
// }
// function getProductByColors($conn) {
//     $stmt = $conn->prepare("SELECT id, name FROM colors");
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $colors = [];
//     while ($row = $result->fetch_assoc()) {
//         $colors[] = $row;
//     }
//     return $colors;
// }

function getProductRating($conn, $product_id) {
    $stmt = $conn->prepare("
        SELECT AVG(rating) AS avg_rating, COUNT(*) AS review_count 
        FROM feedback 
        WHERE product_id = ?
    ");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rating_data = $result->fetch_assoc();
    return [
        'avg_rating' => $rating_data['avg_rating'] !== null ? round($rating_data['avg_rating'], 1) : 0,
        'review_count' => $rating_data['review_count']
    ];
}

function getProductFeedback($conn, $product_id) {
    $feedbacks = [];

    // Kiểm tra kết nối
    if (!$conn) {
        return $feedbacks;
    }

    $query = "
        SELECT f.id, f.rating, f.message, f.created_at, u.name AS user_name
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        WHERE f.product_id = ?
        ORDER BY f.created_at DESC
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = $row;
        }

        $stmt->close();
    }

    return $feedbacks;
}


