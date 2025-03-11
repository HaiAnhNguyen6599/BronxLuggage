<?php
require "../config.php";
require_once '../functions.php';

$where_clauses = [];
$params = [];
$types = "";

// Lọc theo danh mục
if (!empty($_GET['category'])) {
    $categories = $_GET['category'];
    if (!is_array($categories))
        $categories = [$categories];

    $placeholders = implode(',', array_fill(0, count($categories), '?'));
    $where_clauses[] = "c.name IN ($placeholders)";
    $params = array_merge($params, $categories);
    $types .= str_repeat('s', count($categories));
}

// Lọc theo thương hiệu
if (!empty($_GET['brand'])) {
    $brands = $_GET['brand'];
    if (!is_array($brands))
        $brands = [$brands];

    $placeholders = implode(',', array_fill(0, count($brands), '?'));
    $where_clauses[] = "b.name IN ($placeholders)";
    $params = array_merge($params, $brands);
    $types .= str_repeat('s', count($brands));
}

// Lọc theo màu sắc
if (!empty($_GET['color'])) {
    $colors = $_GET['color'];
    if (!is_array($colors))
        $colors = [$colors];

    $placeholders = implode(',', array_fill(0, count($colors), '?'));
    $where_clauses[] = "co.name IN ($placeholders)";
    $params = array_merge($params, $colors);
    $types .= str_repeat('s', count($colors));
}

// Lọc theo kích thước
if (!empty($_GET['size'])) {
    $sizes = $_GET['size'];
    if (!is_array($sizes))
        $sizes = [$sizes];

    $placeholders = implode(',', array_fill(0, count($sizes), '?'));
    $where_clauses[] = "s.name IN ($placeholders)";
    $params = array_merge($params, $sizes);
    $types .= str_repeat('s', count($sizes));
}

// Tạo câu lệnh SQL với điều kiện WHERE
$sql = "SELECT p.*, 
            COALESCE(
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1), 
                'default.jpg') AS image 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN colors co ON p.color_id = co.id
        LEFT JOIN sizes s ON p.size_id = s.id";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// Thực hiện truy vấn
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$products = $stmt->get_result();



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
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Shop List</span>
                </nav>
            </div>
        </div>
    </div>
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
                    <!-- Filter End -->
                    <button id="applyFilters" name="search" class="btn btn-primary w-100 mt-3">Apply Filters</button>
                </form>
                <!-- Size End -->
            </div>
            <!-- Shop Sidebar End -->


            <!-- Shop Product Start -->
            <div class="col-lg-9 col-md-8">
                <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <button class="btn btn-sm btn-light"><i class="fa fa-th-large"></i></button>
                                <button class="btn btn-sm btn-light ml-2"><i class="fa fa-bars"></i></button>
                            </div>
                            <div class="ml-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light dropdown-toggle"
                                        data-toggle="dropdown">Sorting</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Latest</a>
                                        <a class="dropdown-item" href="#">Popularity</a>
                                        <a class="dropdown-item" href="#">Best Rating</a>
                                    </div>
                                </div>
                                <div class="btn-group ml-2">
                                    <button type="button" class="btn btn-sm btn-light dropdown-toggle"
                                        data-toggle="dropdown">Showing</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">10</a>
                                        <a class="dropdown-item" href="#">20</a>
                                        <a class="dropdown-item" href="#">30</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product 1 -->
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
                                        <!-- Lây foeach bên shop.php còn lại -->
                                        <small class="fa fa-star text-primary mr-1"></small>
                                        <small class="fa fa-star text-primary mr-1"></small>
                                        <small class="fa fa-star text-primary mr-1"></small>
                                        <small class="fa fa-star text-primary mr-1"></small>
                                        <small class="fa fa-star text-primary mr-1"></small>
                                        <small>(99)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</span></a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
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