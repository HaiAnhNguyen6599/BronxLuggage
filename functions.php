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


// Filter sản phẩm
// function getFilteredProducts($conn, $filters, $limit, $offset)
// {
//     $where_clauses = [];
//     $params = [];
//     $types = "";

//     // Lọc theo danh mục
//     if (!empty($filters['category'])) {
//         $categories = is_array($filters['category']) ? $filters['category'] : [$filters['category']];
//         $placeholders = implode(',', array_fill(0, count($categories), '?'));
//         $where_clauses[] = "c.name IN ($placeholders)";
//         $params = array_merge($params, $categories);
//         $types .= str_repeat('s', count($categories));
//     }

//     // Lọc theo thương hiệu
//     if (!empty($filters['brand'])) {
//         $brands = is_array($filters['brand']) ? $filters['brand'] : [$filters['brand']];
//         $placeholders = implode(',', array_fill(0, count($brands), '?'));
//         $where_clauses[] = "b.name IN ($placeholders)";
//         $params = array_merge($params, $brands);
//         $types .= str_repeat('s', count($brands));
//     }

//     // Lọc theo màu sắc
//     if (!empty($filters['color'])) {
//         $colors = is_array($filters['color']) ? $filters['color'] : [$filters['color']];
//         $placeholders = implode(',', array_fill(0, count($colors), '?'));
//         $where_clauses[] = "co.name IN ($placeholders)";
//         $params = array_merge($params, $colors);
//         $types .= str_repeat('s', count($colors));
//     }

//     // Lọc theo kích thước
//     if (!empty($filters['size'])) {
//         $sizes = is_array($filters['size']) ? $filters['size'] : [$filters['size']];
//         $placeholders = implode(',', array_fill(0, count($sizes), '?'));
//         $where_clauses[] = "s.name IN ($placeholders)";
//         $params = array_merge($params, $sizes);
//         $types .= str_repeat('s', count($sizes));
//     }

//     // Lọc theo giới tính
//     if (!empty($filters['gender'])) {
//         $genders = is_array($filters['gender']) ? $filters['gender'] : [$filters['gender']];
//         $placeholders = implode(',', array_fill(0, count($genders), '?'));
//         $where_clauses[] = "p.gender IN ($placeholders)";
//         $params = array_merge($params, $genders);
//         $types .= str_repeat('s', count($genders));
//     }

//     // Lọc theo khoảng giá (Slider)
//     if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
//         $where_clauses[] = "p.price BETWEEN ? AND ?";
//         $params[] = $filters['min_price'];
//         $params[] = $filters['max_price'];
//         $types .= "dd";
//     }

//     // Lọc theo khoảng giá từ checkbox
//     if (!empty($filters['price'])) {
//         $price_conditions = [];
//         foreach ($filters['price'] as $range) {
//             [$min, $max] = explode("-", $range) + [null, null];
//             if ($min !== null && $max !== null) {
//                 $price_conditions[] = "(p.price BETWEEN ? AND ?)";
//                 $params[] = $min;
//                 $params[] = $max;
//                 $types .= "dd";
//             } elseif ($min !== null) { // Lọc "Above $500"
//                 $price_conditions[] = "p.price >= ?";
//                 $params[] = $min;
//                 $types .= "d";
//             }
//         }
//         if (!empty($price_conditions)) {
//             $where_clauses[] = "(" . implode(" OR ", $price_conditions) . ")";
//         }
//     }

//     $where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

//     // Truy vấn lấy sản phẩm với phân trang
//     $sql = "SELECT p.*, 
//                 COALESCE(
//                     (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 
//                     'default.jpg') AS image 
//             FROM products p
//             LEFT JOIN categories c ON p.category_id = c.id
//             LEFT JOIN brands b ON p.brand_id = b.id
//             LEFT JOIN colors co ON p.color_id = co.id
//             LEFT JOIN sizes s ON p.size_id = s.id
//             $where_sql
//             LIMIT ? OFFSET ?";

//     $stmt = $conn->prepare($sql);

//     if (!empty($params)) {
//         $types .= "ii"; // 'ii' cho LIMIT và OFFSET
//         $params[] = $limit;
//         $params[] = $offset;
//         $stmt->bind_param($types, ...$params);
//     } else {
//         $stmt->bind_param("ii", $limit, $offset);
//     }

//     $stmt->execute();
//     return $stmt->get_result();
// }

// // Tổng sản phẩm -> Phục vụ cho phân trang
// function getTotalProducts($conn, $filters)
// {
//     $total_products = 0;
//     $where_clauses = [];
//     $params = [];
//     $types = "";

//     // Lọc theo danh mục
//     if (!empty($filters['category'])) {
//         $categories = is_array($filters['category']) ? $filters['category'] : [$filters['category']];
//         $placeholders = implode(',', array_fill(0, count($categories), '?'));
//         $where_clauses[] = "c.name IN ($placeholders)";
//         $params = array_merge($params, $categories);
//         $types .= str_repeat('s', count($categories));
//     }

//     // Lọc theo thương hiệu
//     if (!empty($filters['brand'])) {
//         $brands = is_array($filters['brand']) ? $filters['brand'] : [$filters['brand']];
//         $placeholders = implode(',', array_fill(0, count($brands), '?'));
//         $where_clauses[] = "b.name IN ($placeholders)";
//         $params = array_merge($params, $brands);
//         $types .= str_repeat('s', count($brands));
//     }

//     // Lọc theo màu sắc
//     if (!empty($filters['color'])) {
//         $colors = is_array($filters['color']) ? $filters['color'] : [$filters['color']];
//         $placeholders = implode(',', array_fill(0, count($colors), '?'));
//         $where_clauses[] = "co.name IN ($placeholders)";
//         $params = array_merge($params, $colors);
//         $types .= str_repeat('s', count($colors));
//     }

//     // Lọc theo giới tính
//     if (!empty($filters['gender'])) {
//         $genders = is_array($filters['gender']) ? $filters['gender'] : [$filters['gender']];
//         $placeholders = implode(',', array_fill(0, count($genders), '?'));
//         $where_clauses[] = "p.gender IN ($placeholders)";
//         $params = array_merge($params, $genders);
//         $types .= str_repeat('s', count($genders));
//     }

//     $sql = "SELECT COUNT(*) FROM products p
//             LEFT JOIN categories c ON p.category_id = c.id
//             LEFT JOIN brands b ON p.brand_id = b.id
//             LEFT JOIN colors co ON p.color_id = co.id
//             LEFT JOIN sizes s ON p.size_id = s.id";

//     if (!empty($where_clauses)) {
//         $sql .= " WHERE " . implode(" AND ", $where_clauses);
//     }

//     $stmt = $conn->prepare($sql);
//     if (!empty($params)) {
//         $stmt->bind_param($types, ...$params);
//     }

//     $stmt->execute();
//     $stmt->bind_result($total_products);
//     $stmt->fetch();
//     $stmt->close();

//     return $total_products ?? 0;
// }


function getFilteredProducts($conn, $filters, $limit, $offset)
{
    $where_clauses = [];
    $params = [];
    $types = "";

    // Lọc theo danh mục
    if (!empty($filters['category'])) {
        $categories = is_array($filters['category']) ? $filters['category'] : [$filters['category']];
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $where_clauses[] = "c.name IN ($placeholders)";
        $params = array_merge($params, $categories);
        $types .= str_repeat('s', count($categories));
    }

    // Lọc theo thương hiệu
    if (!empty($filters['brand'])) {
        $brands = is_array($filters['brand']) ? $filters['brand'] : [$filters['brand']];
        $placeholders = implode(',', array_fill(0, count($brands), '?'));
        $where_clauses[] = "b.name IN ($placeholders)";
        $params = array_merge($params, $brands);
        $types .= str_repeat('s', count($brands));
    }

    // Lọc theo màu sắc
    if (!empty($filters['color'])) {
        $colors = is_array($filters['color']) ? $filters['color'] : [$filters['color']];
        $placeholders = implode(',', array_fill(0, count($colors), '?'));
        $where_clauses[] = "co.name IN ($placeholders)";
        $params = array_merge($params, $colors);
        $types .= str_repeat('s', count($colors));
    }

    // Lọc theo giới tính
    if (!empty($filters['gender'])) {
        $genders = is_array($filters['gender']) ? $filters['gender'] : [$filters['gender']];
        $placeholders = implode(',', array_fill(0, count($genders), '?'));
        $where_clauses[] = "p.gender IN ($placeholders)";
        $params = array_merge($params, $genders);
        $types .= str_repeat('s', count($genders));
    }

    // Thêm điều kiện WHERE nếu có bộ lọc
    $where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

    // Truy vấn lấy sản phẩm với rating và số lượng review
    $sql = "SELECT p.*, 
                COALESCE((SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 'default.jpg') AS image,
                COALESCE(AVG(f.rating), 0) AS avg_rating, 
                COUNT(f.id) AS total_reviews 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN colors co ON p.color_id = co.id
            LEFT JOIN feedback f ON p.id = f.product_id
            $where_sql
            GROUP BY p.id
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $types .= "ii"; // 'ii' cho LIMIT và OFFSET
        $params[] = $limit;
        $params[] = $offset;
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    return $stmt->get_result();
}

// Hàm lấy tổng số sản phẩm
function getTotalProducts($conn, $filters)
{
    $total_products = 0;
    $where_clauses = [];
    $params = [];
    $types = "";

    // Lọc theo danh mục
    if (!empty($filters['category'])) {
        $categories = is_array($filters['category']) ? $filters['category'] : [$filters['category']];
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $where_clauses[] = "c.name IN ($placeholders)";
        $params = array_merge($params, $categories);
        $types .= str_repeat('s', count($categories));
    }

    // Lọc theo thương hiệu
    if (!empty($filters['brand'])) {
        $brands = is_array($filters['brand']) ? $filters['brand'] : [$filters['brand']];
        $placeholders = implode(',', array_fill(0, count($brands), '?'));
        $where_clauses[] = "b.name IN ($placeholders)";
        $params = array_merge($params, $brands);
        $types .= str_repeat('s', count($brands));
    }

    // Lọc theo màu sắc
    if (!empty($filters['color'])) {
        $colors = is_array($filters['color']) ? $filters['color'] : [$filters['color']];
        $placeholders = implode(',', array_fill(0, count($colors), '?'));
        $where_clauses[] = "co.name IN ($placeholders)";
        $params = array_merge($params, $colors);
        $types .= str_repeat('s', count($colors));
    }

    // Lọc theo giới tính
    if (!empty($filters['gender'])) {
        $genders = is_array($filters['gender']) ? $filters['gender'] : [$filters['gender']];
        $placeholders = implode(',', array_fill(0, count($genders), '?'));
        $where_clauses[] = "p.gender IN ($placeholders)";
        $params = array_merge($params, $genders);
        $types .= str_repeat('s', count($genders));
    }

    $where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

    $sql = "SELECT COUNT(*) FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN colors co ON p.color_id = co.id
            $where_sql";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $stmt->bind_result($total_products);
    $stmt->fetch();
    $stmt->close();

    return $total_products ?? 0;
}

// function getProductRating($conn, $product_id)
// {
//     $sql = "SELECT 
//                 COALESCE(AVG(rating), 0) AS avg_rating, 
//                 COUNT(*) AS total_reviews 
//             FROM feedback 
//             WHERE product_id = ?";

//     if ($stmt = $conn->prepare($sql)) {
//         $stmt->bind_param("i", $product_id);
//         $stmt->execute();
//         $result = $stmt->get_result()->fetch_assoc();
//         $stmt->close();

//         return [
//             'rating' => round($result['avg_rating'], 1), // Làm tròn 1 chữ số thập phân
//             'reviews' => (int) $result['total_reviews'] // Ép kiểu về số nguyên
//         ];
//     } else {
//         return ['rating' => 0, 'reviews' => 0]; // Trả về giá trị mặc định nếu lỗi
//     }
// }
