<?php
require "../config.php"; // File kết nối database
require_once '../functions.php'; // File chứa các hàm bổ trợ (nếu có)

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: ../account/login.php"); // Đường dẫn tương đối từ account/
    exit();
}

// Lấy thông tin người dùng hiện tại
$user_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, name, email, phone, address, city FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    session_destroy();
    header("Location: ../account/login.php"); // Đường dẫn tương đối từ account/
    exit();
}

$errors = [];
$success = '';

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);

    // Validate dữ liệu đầu vào
    if (empty($name)) {
        $errors['name'] = "Name cannot be empty!";
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    } elseif ($email !== $user['email']) {
        $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $email_check->bind_param("si", $email, $user['id']);
        $email_check->execute();
        if ($email_check->get_result()->num_rows > 0) {
            $errors['email'] = "This email is already taken!";
        }
        $email_check->close();
    }

    // Validate phone
    if (empty($phone)) {
        $errors['phone'] = "Phone number cannot be empty!";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors['phone'] = "Phone number must be 10 digits!";
    }

    // Validate address
    if (empty($address)) {
        $errors['address'] = "Address cannot be empty!";
    }
    if (empty($city)) {
        $errors['city'] = "City cannot be empty!";
    }
    // Nếu không có lỗi, cập nhật thông tin
    if (empty($errors)) {
        $phone_to_save = $phone; // Luôn sử dụng giá trị mới
        $address_to_save = $address; // Luôn sử dụng giá trị mới
        $city_to_save = !empty($city) ? $city : $user['city'];

        $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ? WHERE id = ?");
        $update_stmt->bind_param("sssssi", $name, $email, $phone_to_save, $address_to_save, $city_to_save, $user['id']);
        if ($update_stmt->execute()) {
            $_SESSION['update_success'] = "Profile updated successfully!";
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $user = array_merge($user, [
                'name' => $name,
                'email' => $email,
                'phone' => $phone_to_save,
                'address' => $address_to_save,
                'city' => $city_to_save
            ]);
            header("Location: ../pages/account.php");
        } else {
            $errors['general'] = "Failed to update profile. Please try again.";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags -->
    <?php include '../includes/head.php'; ?>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

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
                    <a class="breadcrumb-item text-dark" href="../pages/index.php">Home</a>
                    <a class="breadcrumb-item text-dark" href="../pages/account.php">My Account</a>
                    <span class="breadcrumb-item active">Edit Profile</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Edit Profile Start -->
    <div class="container-fluid">
        <div class="row px-xl-5 justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-uppercase mb-4">Edit Profile</h2>


                        <form action="edit_profile.php" method="POST">
                            <!-- Name -->
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= htmlspecialchars($user['name']); ?>" required>
                                <?php if (isset($errors['name'])): ?>
                                    <small class="text-danger"><?= $errors['name'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($user['email']); ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <small class="text-danger"><?= $errors['email'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Phone -->
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" maxlength="10"
                                    pattern="[0-9]{10}">
                                <?php if (isset($errors['phone'])): ?>
                                    <small class="text-danger"><?= $errors['phone'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Address -->
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?= htmlspecialchars($user['address'] ?? ''); ?>">
                                <?php if (isset($errors['address'])): ?>
                                    <small class="text-danger"><?= $errors['address'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- City -->
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="<?= htmlspecialchars($user['city'] ?? ''); ?>">
                            </div>

                            <!-- Buttons -->
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="../pages/account.php" class="btn btn-secondary ml-2">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Profile End -->

    <!-- Footer Start -->
    <?php include '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
        </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
        </script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>