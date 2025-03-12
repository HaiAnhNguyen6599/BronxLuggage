<?php
require "../config.php";
require_once '../functions.php';


// Nhận các giá trị lọc từ `$_GET`
$filters = $_GET;

// Số sản phẩm mỗi trang
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Lấy tổng số sản phẩm
$total_products = getTotalProducts($conn, $filters);

// $total_pages = ($total_products > 0) ? ceil($total_products / $limit) : 1;
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


    <!-- Shop Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <!-- Shop Sidebar Start -->
            <div class="col-lg-3 col-md-4">
                <form action="shop.php" method="GET">
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
                                <label class="custom-control-label" for="gender-<?= $row['gender'] ?>"><?= ucfirst($row['gender']) ?></label>
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
                            <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input" name="price[]" value="<?= $range ?>" id="<?= $id ?>" <?= $isChecked ?>>
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
                    <!-- Product Loop -->
                    <?php
                    while ($row = $products->fetch_assoc()) { ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 pb-1">
                            <div class="product-item bg-light mb-4">
                                <div class="product-img position-relative overflow-hidden">
                                    <img class="img-fluid w-100" src="../<?php echo $row['image']; ?>" alt="">
                                    <div class="product-action">
                                        <a class="btn btn-outline-dark btn-square"
                                            href="product.php?id=<?php echo $row['id'] ?>"><i
                                                class="fa fa-shopping-cart"></i></a>
                                        <a class="btn btn-outline-dark btn-square" href=""><i class="fa fa-search"></i></a>
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
                    <?php } ?>

                    <!-- Phân trang -->
                    <div class="col-12">
                        <?php if ($total_pages > 1) { ?>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <!-- Previous Button -->
                                    <?php if ($page > 1) { ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                                        </li>
                                    <?php } ?>

                                    <!-- Số trang -->
                                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                        </li>
                                    <?php } ?>

                                    <!-- Next Button -->
                                    <?php if ($page < $total_pages) { ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
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


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


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