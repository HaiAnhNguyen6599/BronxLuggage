<?php
require "../config.php";
require_once '../functions.php';

$user_id = $_SESSION['user_id'] ?? 0;
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
  <!-- Footer Start -->
  <?php include '../includes/footer.php'; ?>
  <!-- Footer End -->

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