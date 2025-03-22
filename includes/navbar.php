<?php
// lấy tên file PHP đang chạy
$current_page = basename($_SERVER['PHP_SELF']);

$user_id = $_SESSION['user_id'] ?? 0;  // Lấy user_id từ session, nếu không có thì gán = 0



$total_products = 0;

// Nếu có giỏ hàng trong session, đếm số lượng sản phẩm khác nhau
if (!empty($_SESSION['cart'])) {
    $total_products = count($_SESSION['cart']); // Mỗi sản phẩm trong session là một item khác nhau
}


// Nếu người dùng đã đăng nhập, lấy số sản phẩm từ database
if (isset($_SESSION['user_id'])) {

    $sql = "SELECT COUNT(DISTINCT product_id) AS total_products FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Cộng dồn số lượng sản phẩm distinct từ database
    $total_products += $row['total_products'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <?php
    include 'head.php'; ?>
    <!-- End Meta Tags -->
</head>

<!-- Nav Start -->
<div class="container-fluid bg-dark mb-30">
    <div class="row px-xl-5">
        <div class="col-lg-3 d-none d-lg-block">
            <a class="btn d-flex align-items-center justify-content-between bg-primary w-100" data-toggle="collapse"
                href="#navbar-vertical" style="height: 65px; padding: 0 30px">
                <h6 class="text-dark m-0">
                    <i class="fa fa-bars mr-2"></i>Categories
                </h6>
                <i class="fa fa-angle-down text-dark"></i>
            </a>
            <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 bg-light"
                id="navbar-vertical" style="width: calc(100% - 30px); z-index: 999">
                <div class="navbar-nav w-100">
                    <?php
                    $categories = getCategories($conn);
                    while ($category = $categories->fetch_assoc()): ?>
                        <a href="../pages/shop.php?category=<?= $category['name']; ?>" class="nav-item nav-link">
                            <?= htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </nav>
        </div>
        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 py-lg-0 px-0">
                <a href="#" class="text-decoration-none d-block d-lg-none">
                    <span class="h1 text-uppercase text-dark bg-light px-2">Bronx</span>
                    <span class="h1 text-uppercase text-light bg-primary px-2 ml-n1">Luggage</span>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav mr-auto py-0">
                        <a href="../pages/index.php"
                            class="nav-item nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                        <!-- Home được chọn -->
                        <div class="nav-item dropdown">
                            <a href="#"
                                class="nav-link dropdown-toggle <?php echo ($current_page == 'shop.php' || $current_page == 'shop.php?gender=male.php' || $current_page == 'shop.php?gender=male') ? 'active' : ''; ?>"
                                data-toggle="dropdown">
                                Shop <i class="fa fa-angle-down mt-1"></i>
                            </a>
                            <div class="dropdown-menu bg-primary rounded-0 border-0 m-0">
                                <a href="../pages/shop.php"
                                    class="dropdown-item <?php echo ($current_page == 'shop.php') ? 'active' : ''; ?>">Both
                                </a>
                                <a href="../pages/shop.php?gender=male"
                                    class="dropdown-item <?php echo ($current_page == 'shop.php?gender=male') ? 'active' : ''; ?>">Male
                                </a>
                                <a href="../pages/shop.php?gender=female"
                                    class="dropdown-item <?php echo ($current_page == 'shop.php?gender=female') ? 'active' : ''; ?>">Female</a>
                            </div>
                        </div>

                        <div class="nav-item dropdown">
                            <a href="#"
                                class="nav-link dropdown-toggle <?php echo ($current_page == 'cart.php' || $current_page == 'checkout.php') ? 'active' : ''; ?>"
                                data-toggle="dropdown">
                                Pages <i class="fa fa-angle-down mt-1"></i>
                            </a>
                            <div class="dropdown-menu bg-primary rounded-0 border-0 m-0">
                                <a href="../pages/cart.php"
                                    class="dropdown-item <?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">Shopping
                                    Cart</a>
                                <a href="../pages/checkout.php"
                                    class="dropdown-item <?php echo ($current_page == 'checkout.php') ? 'active' : ''; ?>">Checkout</a>
                            </div>
                        </div>
                        <a href="../pages/about.php"
                            class="nav-item nav-link <?= ($current_page == 'about.php') ? 'active' : ''; ?>">About
                            Us</a>
                        <a href="../pages/gallery.php"
                            class="nav-item nav-link <?= ($current_page == 'gallery.php') ? 'active' : ''; ?>">Gallery
                        </a>
                        <a href="../pages/contact.php"
                            class="nav-item nav-link <?= ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
                    </div>
                    <div class="navbar-nav ml-auto py-0 d-none d-lg-block">
                        <a href="../pages/cart.php" class="btn px-0 ml-3">
                            <i class="fas fa-shopping-cart text-primary"></i>
                            <span class="badge text-secondary border border-secondary rounded-circle"
                                style="padding-bottom: 2px"><?php echo $total_products; ?></span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
<!-- Navbar End -->