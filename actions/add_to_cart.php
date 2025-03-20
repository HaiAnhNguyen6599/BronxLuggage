<?php
require "../config.php";
require_once '../functions.php';

// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $size_id = $_POST['size_id'];
    $color_id = $_POST['color_id'];
    $quantity = max(1, intval($_POST['quantity']));

    // Kiểm tra tồn kho
    $product = getProductById($conn, $product_id);
    if (!$product || $quantity > $product['inventory']) {
        $_SESSION['cart_message'] = "Error: Requested quantity exceeds available stock!";
        header("Location: product.php?id=$product_id");
        exit;
    }

    // Khởi tạo giỏ hàng trong session nếu chưa có hoặc không phải mảng
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $cart_item = [
        'product_id' => $product_id,
        'size_id' => $size_id,
        'color_id' => $color_id,
        'quantity' => $quantity
    ];

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    $found = false;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            // Kiểm tra $item có phải mảng và chứa các khóa cần thiết không
            if (
                is_array($item) &&
                isset($item['product_id']) && $item['product_id'] == $product_id &&
                isset($item['size_id']) && $item['size_id'] == $size_id &&
                isset($item['color_id']) && $item['color_id'] == $color_id
            ) {
                $new_quantity = $item['quantity'] + $quantity;
                if ($new_quantity > $product['inventory']) {
                    $_SESSION['cart_message'] = "Error: Total quantity exceeds available stock!";
                    header("Location: product.php?id=$product_id");
                    exit;
                }
                $item['quantity'] = $new_quantity;
                $found = true;
                break;
            }
        }
        unset($item); // Hủy tham chiếu sau khi dùng &$item
    }

    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }

    $_SESSION['cart_message'] = "Product added to cart successfully!";
    header("Location: ../pages/product.php?id=$product_id");
    exit;
}
