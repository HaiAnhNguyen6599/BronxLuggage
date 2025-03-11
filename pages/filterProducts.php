<?php
function filterProducts($conn, $filters, $limit, $offset)
{
    $whereClauses = [];
    $params = [];
    $types = "";

    if (!empty($filters['price_min'])) {
        $whereClauses[] = "p.price >= ?";
        $params[] = $filters['price_min'];
        $types .= "d";
    }
    if (!empty($filters['price_max'])) {
        $whereClauses[] = "p.price <= ?";
        $params[] = $filters['price_max'];
        $types .= "d";
    }
    if (!empty($filters['color'])) {
        $whereClauses[] = "c.name IN (" . implode(",", array_fill(0, count($filters['color']), "?")) . ")";
        $params = array_merge($params, $filters['color']);
        $types .= str_repeat("s", count($filters['color']));
    }
    if (!empty($filters['size'])) {
        $whereClauses[] = "s.name IN (" . implode(",", array_fill(0, count($filters['size']), "?")) . ")";
        $params = array_merge($params, $filters['size']);
        $types .= str_repeat("s", count($filters['size']));
    }
    if (!empty($filters['category'])) {
        $whereClauses[] = "cat.name IN (" . implode(",", array_fill(0, count($filters['category']), "?")) . ")";
        $params = array_merge($params, $filters['category']);
        $types .= str_repeat("s", count($filters['category']));
    }
    if (!empty($filters['brand'])) {
        $whereClauses[] = "b.name IN (" . implode(",", array_fill(0, count($filters['brand']), "?")) . ")";
        $params = array_merge($params, $filters['brand']);
        $types .= str_repeat("s", count($filters['brand']));
    }

    $sql = "
        SELECT p.id, p.name as product_name, p.price, b.name as brand, c.name as color,
               cat.name as category, s.name as size,
               COALESCE(AVG(f.rating), 0) AS rating, COUNT(f.id) AS reviews,
               COALESCE((SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 'default.jpg') AS image
        FROM products p
        LEFT JOIN feedback f ON p.id = f.product_id
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN colors c ON p.color_id = c.id
        LEFT JOIN categories cat ON p.category_id = cat.id
        LEFT JOIN sizes s ON p.size_id = s.id
    ";

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    $sql .= " GROUP BY p.id, p.name, p.price, b.name, cat.name, c.name, s.name LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}
