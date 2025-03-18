<script>
  function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
      window.location.href = '../account/logout.php';
    }
  }
</script>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta Tags -->
  <?php include 'head.php'; ?>
  <!-- End Meta Tags -->
</head>

<!-- Topbar Start -->
<div class="container-fluid">
  <div class="row bg-secondary py-1 px-xl-5">
    <div class="col-lg-12 text-center text-lg-right">
      <div class="d-inline-flex align-items-center ml-auto">
        <div class="btn-group">
          <?php if (isset($_SESSION['name'])): ?>
            <!-- Nếu đã đăng nhập, hiển thị tên và dropdown Logout -->
            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
              <?= htmlspecialchars($_SESSION['name']); ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="../pages/account.php">My Account</a>
              <a class="dropdown-item" href="../account/change_password.php">Chage Password</a>
              <a class="dropdown-item" href="#" onclick="confirmLogout()">Logout</a>
            </div>
          <?php else: ?>
            <!-- Nếu chưa đăng nhập, hiển thị Sign In và Sign Up -->
            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
              My Account
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="../account/login.php">Sign in</a>
              <a class="dropdown-item" href="../account/signup.php">Sign up</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row align-items-center bg-light py-3 px-xl-5 d-none d-lg-flex">
  <div class="col-lg-4">
    <a href="" class="text-decoration-none">
      <span class="h1 text-uppercase text-primary bg-dark px-2">Bronx</span>
      <span class="h1 text-uppercase text-dark bg-primary px-2 ml-n1">Luggage</span>
    </a>
  </div>
  <div class="col-lg-4 col-6 text-left">
    <form action="">
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for products">
        <div class="input-group-append">
          <span class="input-group-text bg-transparent text-primary">
            <i class="fa fa-search"></i>
          </span>
        </div>
      </div>
    </form>
  </div>
  <div class="col-lg-4 col-6 text-right">
    <p class="m-0">Customer Service</p>
    <h5 class="m-0">+012 345 6789</h5>
  </div>
</div>
</div>
<!-- Topbar End -->