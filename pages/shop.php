<?php
require "../config.php";
require_once '../functions.php';

// Lấy user_id từ session
$user_id = $_SESSION['user_id'] ?? 0;

// Nhận các giá trị lọc từ `$_GET`
$filters = $_GET;

// Số sản phẩm mỗi trang
$limit = 9;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lấy tổng số sản phẩm
$total_products = getTotalProducts($conn, $filters);

$total_pages = max(1, ceil($total_products / $limit));

if ($total_products > 0 && $page > $total_pages) {
    header("Location: ?" . http_build_query(array_merge($_GET, ['page' => 1])));
    exit;
}
// Lấy danh sách sản phẩm theo bộ lọc và phân trang
$products = getFilteredProducts($conn, $filters, $limit, $offset);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../includes/head.php' ?>
</head>
<style>
    .product-img img {
        width: 100%;
        height: 400px;
        /* Điều chỉnh theo ý muốn */
        object-fit: cover;
        /* Đảm bảo hình ảnh không bị méo */
    }

    /* CSS cho ảnh No Product Found */
    .no-product-img {
        max-width: 300px;
        /* Giữ nguyên kích thước tối đa */
        width: 100%;
        /* Đảm bảo ảnh responsive */
        height: auto;
        /* Giữ tỷ lệ ảnh */
        margin: 0 auto;
        /* Căn giữa ảnh */
        display: block;
        /* Đảm bảo ảnh là block element để căn giữa hoạt động */
    }

    /* CSS cho toàn bộ container của thông báo không có sản phẩm */
    .no-product-container {
        padding: 20px;
        /* Thêm khoảng cách bên trong */
        background-color: #fff;
        /* Nền trắng để khớp với product-item bg-light */
        border-radius: 5px;
        /* Bo góc nhẹ cho đồng bộ */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        /* Thêm bóng nhẹ giống product-item */
        margin-bottom: 24px;
        /* Khoảng cách dưới tương tự mb-4 */
    }

    /* CSS cho tiêu đề "No Products Found in Stock" */
    .no-product-container h4 {
        color: #333;
        /* Màu chữ tối để đồng bộ với template */
        font-size: 1.5rem;
        /* Kích thước chữ phù hợp với h6 trong product-item */
        margin-top: 15px;
        /* Khoảng cách trên với ảnh */
        font-weight: 500;
        /* Độ đậm vừa phải */
    }

    /* CSS cho product-list-header */
    .product-list-header {
        text-align: center;
        /* Căn giữa tiêu đề */
        margin-bottom: 30px;
        /* Khoảng cách dưới */
        position: relative;
        /* Giữ position relative nếu cần */
    }

    /* CSS cho span bên trong */
    .product-list-header span {
        background-color: #6c757d;
        /* Màu nền bg-secondary (xám) */
        padding-right: 12px;
        /* pr-3 trong Bootstrap */
        padding-left: 12px;
        /* Thêm padding trái để cân đối */
        display: inline-block;
        /* Chỉ bao quanh nội dung chữ */
        color: black;
        /* Chữ Đen */
        text-transform: uppercase;
        /* Chữ in hoa */
        font-size: 1.75rem;
        /* Kích thước chữ lớn hơn một chút vì là h3 */
        font-weight: 500;
        /* Độ đậm vừa phải */
    }

    /* Loại bỏ đường gạch ngang (nếu có) */
    .product-list-header::after {
        content: none;
        /* Xóa pseudo-element nếu có đường gạch */
    }
</style>

<body>
    <!-- Topbar Start -->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
    <!-- Navbar End -->


    <!-- Breadcrumb Start -->
    <?php include '../includes/breadcumb.php' ?>
    <!-- Breadcrumb End -->

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div
            class="alert <?php echo strpos($_SESSION['cart_message'], 'Error') === false ? 'alert-success' : 'alert-danger'; ?> mt-3">
            <?php echo $_SESSION['cart_message']; ?>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>
    <!-- Shop Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <!-- Shop Sidebar Start -->
            <div class="col-lg-3 col-md-4">
                <form action="../pages/shop.php" method="GET">
                    <!-- Filter Start -->
                    <!-- Lọc theo Category -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Filter
                            by category</span></h5>
                    <div class="bg-light p-4 mb-30">
                        <?php
                        // Kiểm tra nếu category tồn tại trong URL, nếu không thì gán là mảng rỗng
                        $selected_categories = $_GET['category'] ?? [];
                        if (!is_array($selected_categories)) {
                            $selected_categories = [$selected_categories];
                        }

                        $categories = getCategories($conn);
                        while ($row = $categories->fetch_assoc()) {
                            $isChecked = in_array($row['name'], $selected_categories) ? 'checked' : '';
                        ?>
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" name="category[]" value="<?= $row['name'] ?>"
                                    class="custom-control-input" id="category-<?= $row['id'] ?>" <?= $isChecked ?>>
                                <label class="custom-control-label"
                                    for="category-<?= $row['id'] ?>"><?= $row['name'] ?></label>
                            </div>
                        <?php } ?>


                    </div>

                    <!-- Lọc theo thương hiệu -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Filter
                            by brand</span></h5>
                    <div class="bg-light p-4 mb-30">
                        <?php
                        $selected_brands = $_GET['brand'] ?? [];
                        if (!is_array($selected_brands))
                            $selected_brands = [$selected_brands];

                        $brands = getBrands($conn);
                        while ($row = $brands->fetch_assoc()) {
                            $isChecked = in_array($row['name'], $selected_brands) ? 'checked' : '';
                        ?>
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" name="brand[]" value="<?= $row['name'] ?>"
                                    class="custom-control-input" id="brand-<?= $row['id'] ?>" <?= $isChecked ?>>
                                <label class="custom-control-label"
                                    for="brand-<?= $row['id'] ?>"><?= $row['name'] ?></label>
                            </div>
                        <?php } ?>
                    </div>


                    <!-- Lọc theo màu sắc -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Filter
                            by color</span></h5>
                    <div class="bg-light p-4 mb-30">
                        <?php
                        $selected_colors = $_GET['color'] ?? [];
                        if (!is_array($selected_colors))
                            $selected_colors = [$selected_colors];

                        $colors = getColors($conn);
                        while ($row = $colors->fetch_assoc()) {
                            $isChecked = in_array($row['name'], $selected_colors) ? 'checked' : '';
                        ?>
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" name="color[]" value="<?= $row['name'] ?>"
                                    class="custom-control-input" id="color-<?= $row['id'] ?>" <?= $isChecked ?>>
                                <label class="custom-control-label"
                                    for="color-<?= $row['id'] ?>"><?= $row['name'] ?></label>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Lọc theo kích thước -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Filter
                            by size</span></h5>
                    <div class="bg-light p-4 mb-30">
                        <?php
                        $selected_sizes = $_GET['size'] ?? [];
                        if (!is_array($selected_sizes))
                            $selected_sizes = [$selected_sizes];

                        $sizes = getSizes($conn);
                        while ($row = $sizes->fetch_assoc()) {
                            $isChecked = in_array($row['name'], $selected_sizes) ? 'checked' : '';
                        ?>
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" name="size[]" value="<?= $row['name'] ?>"
                                    class="custom-control-input" id="size-<?= $row['id'] ?>" <?= $isChecked ?>>
                                <label class="custom-control-label" for="size-<?= $row['id'] ?>"><?= $row['name'] ?></label>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Lọc theo Giới tính -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span
                            class="bg-secondary pr-3">Filter
                            by gender</span></h5>
                    <div class="bg-light p-4 mb-30">
                        <?php
                        $selected_genders = $_GET['gender'] ?? [];
                        if (!is_array($selected_genders))
                            $selected_genders = [$selected_genders];

                        $genders = getGenders($conn);
                        while ($row = $genders->fetch_assoc()) {
                            $isChecked = in_array($row['gender'], $selected_genders) ? 'checked' : '';
                        ?>
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" name="gender[]" value="<?= $row['gender'] ?>"
                                    class="custom-control-input" id="gender-<?= $row['gender'] ?>" <?= $isChecked ?>>
                                <label class="custom-control-label"
                                    for="gender-<?= $row['gender'] ?>"><?= ucfirst($row['gender']) ?></label>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Lọc theo Giá -->
                    <h5 class="section-title position-relative text-uppercase mb-3">
                        <span class="bg-secondary pr-3">Filter by price</span>
                    </h5>
                    <div class="bg-light p-4 mb-30">
                        <?php
                        // Danh sách khoảng giá
                        $price_ranges = [
                            "0-50" => "Under $50",
                            "50-100" => "$50 - $100",
                            "100-200" => "$100 - $200",
                            "200-500" => "$200 - $500",
                            "500-1000" => "Above $500"
                        ];

                        // Kiểm tra giá trị đã chọn
                        $selected_prices = $_GET['price'] ?? [];

                        // Đảm bảo $selected_prices là mảng
                        if (!is_array($selected_prices)) {
                            $selected_prices = [$selected_prices];
                        }

                        foreach ($price_ranges as $range => $label) {
                            $isChecked = in_array($range, $selected_prices) ? 'checked' : '';
                            $id = "price-" . str_replace("-", "_", $range); // ID duy nhất cho mỗi checkbox
                        ?>
                            <div
                                class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input" name="price[]" value="<?= $range ?>"
                                    id="<?= $id ?>" <?= $isChecked ?>>
                                <label class="custom-control-label" for="<?= $id ?>"><?= $label ?></label>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Filter End -->
                    <button id="applyFilters" name="search" class="btn btn-primary w-100 mt-3">Apply Filters</button>

                </form>

                <!-- Size End -->
            </div>
            <!-- Shop Sidebar End -->


            <!-- Shop Product Start -->
            <div class="col-lg-9 col-md-8">
                <div class="row pb-3">
                    <!-- Header -->
                    <div class="col-12">
                        <h3 class="product-list-header"><span class="bg-secondary pr-3">List Of Products</span></h3>
                    </div>
                    <!-- Product Loop -->
                    <?php
                    if ($products->num_rows > 0) {
                        while ($row = $products->fetch_assoc()) { ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 pb-1">
                                <div class="product-item bg-light mb-4">
                                    <div class="product-img position-relative overflow-hidden">
                                        <img class="img-fluid w-100" src="../<?php echo $row['image']; ?>" alt="">
                                        <div class="product-action">
                                            <a class="btn btn-outline-dark btn-square"
                                                href="product.php?id=<?php echo $row['id'] ?>"><i
                                                    class="fa fa-shopping-cart"></i></a>
                                        </div>
                                    </div>
                                    <div class="text-center py-4">
                                        <a class="h6 text-decoration-none text-truncate"
                                            href="product.php?id=<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a>
                                        <div class="d-flex align-items-center justify-content-center mt-2">
                                            <h5>$<?php echo $row['price'] ?></h5>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center mb-1">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <?php if ($i < floor($row['avg_rating'])): ?>
                                                    <small class="fa fa-star text-primary mr-1"></small>
                                                <?php elseif ($i < $row['avg_rating']): ?>
                                                    <small class="fa fa-star-half-alt text-primary mr-1"></small>
                                                <?php else: ?>
                                                    <small class="far fa-star text-primary mr-1"></small>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            <small>(<?php echo $row['total_reviews']; ?>)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="col-12 text-center">
                            <div class="no-product-container">
                                <img src="../img/no-product-found.png" alt="No Products" class="no-product-img">
                                <h4>Sorry, No Products Found in Stock !</h4>
                            </div>
                        </div>
                    <?php } ?>
                </div>


                <!-- Phân trang -->
                <div class="col-12">
                    <?php if ($total_pages > 1) { ?>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <?php if ($page > 1) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                                    </li>
                                <?php } ?>

                                <!-- Số trang -->
                                <?php
                                // Số lượng trang tối đa hiển thị xung quanh trang hiện tại (trái và phải)
                                $range = 2;
                                $show_dots = false;

                                // Trang đầu tiên
                                if ($total_pages > 1) {
                                ?>
                                    <li class="page-item <?= ($page == 1) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a>
                                    </li>
                                    <?php
                                    // Hiển thị dấu "..." nếu trang 2 cách xa trang hiện tại
                                    if ($page - $range > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        $show_dots = true;
                                    }
                                }

                                // Các trang xung quanh trang hiện tại
                                for ($i = max(2, $page - $range); $i <= min($total_pages - 1, $page + $range); $i++) {
                                    ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                    </li>
                                <?php
                                }

                                // Trang cuối cùng và dấu "..." nếu cần
                                if ($total_pages > 1) {
                                    if ($page + $range < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        $show_dots = true;
                                    }
                                ?>
                                    <li class="page-item <?= ($page == $total_pages) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>"><?= $total_pages ?></a>
                                    </li>
                                <?php
                                }
                                ?>

                                <!-- Next Button -->
                                <?php if ($page < $total_pages) { ?>
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Shop Product End -->
    </div>
    </div>
    <!-- Shop End -->


    <!-- Footer Start -->
    <?php include '../includes/footer.php' ?>
    <!-- Footer End -->



    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>