<?php
// lấy tên file PHP đang chạy
$current_page = basename($_SERVER['PHP_SELF']);

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
                    <a href="shop.php?category=<?= $category['name']; ?>" class="nav-item nav-link">
                        <?= htmlspecialchars($category['name']); ?>
                    </a>
                    <?php endwhile; ?>
                </div>
            </nav>
        </div>
        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 py-lg-0 px-0">
                <a href="" class="text-decoration-none d-block d-lg-none">
                    <span class="h1 text-uppercase text-dark bg-light px-2">Bronx</span>
                    <span class="h1 text-uppercase text-light bg-primary px-2 ml-n1">Luggage</span>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav mr-auto py-0">
                        <a href="index.php"
                            class="nav-item nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                        <!-- Home được chọn -->
                        <a href="shop.php"
                            class="nav-item nav-link <?= ($current_page == 'shop.php') ? 'active' : ''; ?>">Shop</a>
                        <a href="product.php"
                            class="nav-item nav-link <?= ($current_page == 'product.php') ? 'active' : ''; ?>">Shop
                            Detail</a>
                        <div class="nav-item dropdown">
                            <a href="#"
                                class="nav-link dropdown-toggle <?php echo ($current_page == 'cart.php' || $current_page == 'checkout.php') ? 'active' : ''; ?>"
                                data-toggle="dropdown">
                                Pages <i class="fa fa-angle-down mt-1"></i>
                            </a>
                            <div class="dropdown-menu bg-primary rounded-0 border-0 m-0">
                                <a href="cart.php"
                                    class="dropdown-item <?php echo ($current_page == 'cart.php') ? 'active' : ''; ?>">Shopping
                                    Cart</a>
                                <a href="checkout.php"
                                    class="dropdown-item <?php echo ($current_page == 'checkout.php') ? 'active' : ''; ?>">Checkout</a>
                            </div>
                        </div>
                        <a href="contact.php"
                            class="nav-item nav-link <?= ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
                    </div>
                    <div class="navbar-nav ml-auto py-0 d-none d-lg-block">
                        <a href="cart.php" class="btn px-0 ml-3">
                            <i class="fas fa-shopping-cart text-primary"></i>
                            <span class="badge text-secondary border border-secondary rounded-circle"
                                style="padding-bottom: 2px">0</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>
<!-- Navbar End -->