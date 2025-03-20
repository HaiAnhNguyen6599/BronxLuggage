<?php
require "../config.php";
require_once '../functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta Tags -->
  <?php include '../includes/head.php'; ?>
  <!-- End Meta Tags -->
</head>

<body>
  <!-- Topbar Start -->
  <?php include '../includes/topbar.php'; ?>
  <!-- Topbar End -->

  <!-- Navbar Start-->
  <?php include '../includes/navbar.php'; ?>
  <!-- Navbar End -->

  <!-- Carousel Start -->
  <div class="container-fluid mb-3">
    <div class="row px-xl-5">
      <div class="col-lg-12">
        <div id="header-carousel" class="carousel slide carousel-fade mb-30 mb-lg-0" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#header-carousel" data-slide-to="0" class="active"></li>
            <li data-target="#header-carousel" data-slide-to="1"></li>
            <!-- <li data-target="#header-carousel" data-slide-to="2"></li> -->
          </ol>
          <div class="carousel-inner">
            <!-- Men Luggage -->
            <div class="carousel-item position-relative active" style="height: 430px">
              <img class="position-absolute w-100 h-100" src="../img/carousel-1.jpg"
                style="object-fit: cover" />
              <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="p-3" style="max-width: 700px">
                  <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown">
                    Men Luggage
                  </h1>
                  <p class="mx-md-5 px-5 animate__animated animate__bounceIn">
                    Lorem rebum magna amet lorem magna erat diam stet. Sadips
                    duo stet amet amet ndiam elitr ipsum diam
                  </p>
                  <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp"
                    href="shop.php?gender=male">Shop Now</a>
                </div>
              </div>
            </div>

            <!-- Women Luggage -->
            <div class="carousel-item position-relative" style="height: 430px">
              <img class="position-absolute w-100 h-100" src="../img/carousel-2.jpg"
                style="object-fit: cover" />
              <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                <div class="p-3" style="max-width: 700px">
                  <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown">
                    Women Luggage
                  </h1>
                  <p class="mx-md-5 px-5 animate__animated animate__bounceIn">
                    Lorem rebum magna amet lorem magna erat diam stet. Sadips
                    duo stet amet amet ndiam elitr ipsum diam
                  </p>
                  <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp"
                    href="shop.php?gender=female">Shop Now</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- Carousel End -->


  <!-- Categories Start -->
  <div class="container-fluid pt-5">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
      <span class="bg-secondary pr-3">Categories</span>
    </h2>
    <div class="row px-xl-5 pb-3">
      <?php
      $categories = getCategories($conn);
      while ($row = $categories->fetch_assoc()) { ?>
        <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
          <a class="text-decoration-none" href="shop.php?category=<?= $row['name'] ?>">
            <div class="cat-item d-flex align-items-center mb-4">
              <div class="overflow-hidden" style="width: 120px; height: 120px; border-radius: 10px;">
                <img class="img-fluid" src="../img/categories/cat-<?= $row['id'] ?>.jpg"
                  alt="<?= $row['name'] ?>" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
              <div class="flex-fill pl-3">
                <h6><?= $row['name'] ?></h6>
                <small class="text-body"><?= $row['product_count'] ?> Products</small>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
  <!-- Categories End -->

  <!-- Featured Products Start -->
  <div class="container-fluid pt-5 pb-3">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
      <span class="bg-secondary pr-3">Featured Products</span>
    </h2>
    <div class="row px-xl-5">
      <?php
      $products = getTopRatedProducts(); // Lấy 8 sản phẩm có rating cao nhất
      foreach ($products as $product): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
          <div class="product-item bg-light mb-4">
            <div class="product-img position-relative overflow-hidden">
              <img class="img-fluid w-100" src="../<?php echo $product['img'] ?: 'default.jpg'; ?>"
                alt="<?php echo $product['name']; ?>" style="height: 400px; object-fit: cover;">
              <div class="product-action">
                <a class="btn btn-outline-dark btn-square" href="#"><i class="fa fa-shopping-cart"></i></a>
                <a class="btn btn-outline-dark btn-square"
                  href="product.php?id=<?php echo $product['id']; ?>"><i class="fa fa-search"></i></a>
              </div>
            </div>
            <div class="text-center py-4">
              <a class="h6 text-decoration-none text-truncate"
                href="product.php?id=<?php echo $product['id']; ?>">
                <?php echo $product['name']; ?>
              </a>
              <div class="d-flex align-items-center justify-content-center mt-2">
                <h5>$<?php echo number_format($product['price'], 2); ?></h5>
              </div>
              <div class="d-flex align-items-center justify-content-center mb-1">
                <?php for ($i = 0; $i < 5; $i++): ?>
                  <?php if ($i < floor($product['rating'])): ?>
                    <small class="fa fa-star text-primary mr-1"></small>
                  <?php elseif ($i < $product['rating']): ?>
                    <small class="fa fa-star-half-alt text-primary mr-1"></small>
                  <?php else: ?>
                    <small class="far fa-star text-primary mr-1"></small>
                  <?php endif; ?>
                <?php endfor; ?>
                <small>(<?php echo $product['reviews']; ?>)</small>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <!-- Featured Products End -->

  <!-- Brands Start -->

  <div class="container-fluid py-5">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
      <span class="bg-secondary pr-3">Featured Brands</span>
    </h2>
    <div class="row px-xl-5">
      <div class="col">
        <div class="owl-carousel vendor-carousel">
          <?php
          $brands = getBrands($conn);
          while ($brand = $brands->fetch_assoc()): ?>
            <div class="bg-light p-4 d-flex justify-content-center align-items-center" style="height: 150px;">
              <a href="shop.php?brand=<?= $brand['name'] ?>">
                <img src="../img/brands/brand-<?= $brand['id'] ?>.jpg" alt="<?= $brand['name'] ?>"
                  style="max-width: 100%; max-height: 100%; object-fit: contain;">
              </a>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
  <!-- Brands End -->


  <!-- Footer Start -->
  <?php include '../includes/footer.php'; ?>
  <!-- Footer End -->

  <!-- Back to Top -->
  <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>
  <!-- Success Message -->
  <!-- <?php if (isset($_SESSION['success'])): ?>
    <div id="successMessage" style="display: block; background: #4CAF50; color: white; padding: 10px; text-align: center; position: fixed; top: 10px; left: 50%; transform: translateX(-50%); border-radius: 5px;">
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
          var successMessage = document.getElementById("successMessage");
          if (successMessage) {
            successMessage.style.display = "none";
          }
        }, 3000);
      });
    </script>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?> -->
  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

  <!-- Contact Javascript File -->
  <script src="../mail/jqBootstrapValidation.min.js"></script>
  <script src="../mail/contact.js"></script>

  <!-- Template Javascript -->
  <script src="../js/main.js"></script>
</body>

</html>

<?php
// Đóng kết nối
$conn->close();
?>