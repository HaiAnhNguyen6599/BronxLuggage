<?php
// Lấy tên file hiện tại
$current_page = basename($_SERVER['PHP_SELF'], ".php");

// Xác định tiêu đề breadcrumb
$breadcrumb_titles = [
    "index" => "Home",
    "shop" => "Shop",
    "contact" => "Contact Us",
    "about" => "About Us",
    "cart" => "Shopping Cart",
    "checkout" => "Checkout"
];
$current_title = isset($breadcrumb_titles[$current_page]) ? $breadcrumb_titles[$current_page] : ucfirst($current_page);

// Lấy bộ lọc từ URL
$currentFilters = $_GET;
unset($currentFilters['page']); // Loại bỏ tham số phân trang
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'head.php'; ?>
    <style>
        .breadcrumb-filter {
            background: #343a40;
            padding: 3px 8px;
            border-radius: 15px;
            display: inline-block;
            margin: 0 5px;
        }

        .remove-filter {
            color: #ffc107;
            text-decoration: none;
            font-weight: bold;
            margin-left: 5px;
        }

        .remove-filter:hover {
            color: #343a40;
        }

        .breadcrumb-separator {
            margin: 0 5px;
        }

        /* Dropdown styles */
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }

        .filter-dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px;
            border-radius: 5px;
        }

        .filter-dropdown:hover .filter-dropdown-content {
            display: block; /* Hiển thị khi hover */
        }

        .filter-item {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>

                    <!-- Hiển thị trang hiện tại -->
                    <?php if ($current_page !== "index") { ?>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-item active"><?= htmlspecialchars($current_title) ?></span>
                    <?php } ?>

                    <!-- Dropdown cho bộ lọc -->
                    <?php
                    $breadcrumbParts = [];
                    foreach ($currentFilters as $key => $values) {
                        if (!is_array($values)) {
                            $values = [$values]; // Chuyển thành mảng nếu chỉ có 1 giá trị
                        }
                        foreach ($values as $value) {
                            if (empty($value)) continue;

                            // Tạo URL khi xóa bộ lọc
                            $filteredParams = $currentFilters;
                            $filteredParams[$key] = array_values(array_diff($values, [$value]));
                            if (empty($filteredParams[$key])) {
                                unset($filteredParams[$key]);
                            }
                            $filterUrl = "?" . http_build_query($filteredParams);

                            // Thêm bộ lọc vào danh sách dropdown
                            $breadcrumbParts[] = '<div class="filter-item">' . ucfirst(htmlspecialchars($value)) .
                                ' <a href="' . $filterUrl . '" class="remove-filter">✖</a></div>';
                        }
                    }

                    // Hiển thị dropdown nếu có bộ lọc
                    if (!empty($breadcrumbParts)) {
                        echo '<span class="breadcrumb-separator">/</span>';
                        echo '<div class="filter-dropdown">';
                        echo '<span class="breadcrumb-item">Filters ▼</span>';
                        echo '<div class="filter-dropdown-content">';
                        echo implode('', $breadcrumbParts); // Hiển thị các bộ lọc trong dropdown
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </nav>
            </div>
        </div>
    </div>
</body>
</html>