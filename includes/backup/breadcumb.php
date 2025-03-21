<?php
// Lấy tên file hiện tại
$current_page = basename($_SERVER['PHP_SELF'], ".php");

// Xác định đường dẫn và tiêu đề breadcrumb dựa vào trang hiện tại
$breadcrumb_titles = [
    "index" => "Home",
    "shop" => "Shop",
    "contact" => "Contact Us",
    "about" => "About Us",
    "cart" => "Shopping Cart",
    "checkout" => "Checkout"
];

// Kiểm tra nếu có tiêu đề phù hợp với trang hiện tại
$current_title = isset($breadcrumb_titles[$current_page]) ? $breadcrumb_titles[$current_page] : ucfirst($current_page);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <?php
    include 'head.php'; ?>
    <!-- End Meta Tags -->

    <style>
        breadcrumb-filter {
            background: #f8f9fa;
            padding: 3px 8px;
            border-radius: 15px;
            display: inline-block;
            margin: 0 5px;
        }

        .remove-filter {
            color: red;
            text-decoration: none;
            font-weight: bold;
            margin-left: 5px;
        }

        .remove-filter:hover {
            color: darkred;
        }

        .breadcrumb-separator {
            margin: 0 5px;
        }
    </style>
</head>


<div class="container-fluid">
    <div class="row px-xl-5">
        <div class="col-12">
            <nav class="breadcrumb bg-light mb-30">
                <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>

                <!-- Lấy trang hiện hiện -->
                <?php if ($current_page !== "index") { ?>
                    <span class="breadcrumb-item active"><?= htmlspecialchars($current_title) ?></span>
                <?php } ?>

                <?php
                $currentFilters = $_GET;
                unset($currentFilters['page']);;

                foreach ($currentFilters as $key => $values) {
                    if (!is_array($values)) {
                        $values = [$values]; // Chuyển thành mảng nếu chỉ có 1 giá trị
                    }

                    foreach ($values as $value) {
                        if (empty($value)) continue; // Bỏ qua nếu giá trị rỗng

                        // Tạo URL mới khi xóa bộ lọc này
                        $filteredParams = $currentFilters;
                        unset($filteredParams[$key]);
                        $filteredParams[$key] = array_values(array_diff($values, [$value]));
                        if (empty($filteredParams[$key])) {
                            unset($filteredParams[$key]);
                        }
                        $filterUrl = "?" . http_build_query($filteredParams);

                        // Thêm vào breadcrumb list
                        $breadcrumbParts[] = '<span class="breadcrumb-filter">' . ucfirst(htmlspecialchars($value)) .
                            ' <a href="' . $filterUrl . '" class="remove-filter">✖</a></span>';
                    }
                }

                // Hiển thị nếu có bộ lọc
                if (!empty($breadcrumbParts)) {
                    echo ' <span class="breadcrumb-separator">/</span> ' . implode(' <span class="breadcrumb-separator">/</span> ', $breadcrumbParts);
                }
                ?>
            </nav>
        </div>
    </div>
</div>