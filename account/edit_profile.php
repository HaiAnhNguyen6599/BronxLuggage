<?php
require "../config.php"; // File kết nối database
require_once '../functions.php'; // File chứa các hàm bổ trợ (nếu có)

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: login.php"); // Đường dẫn tương đối từ account/
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
    header("Location: login.php"); // Đường dẫn tương đối từ account/
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
    $delete_phone = isset($_POST['delete_phone']);
    $delete_address = isset($_POST['delete_address']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($name)) {
        $errors['name'] = "Name cannot be empty!";
    }
    if (empty($email)) {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    } elseif ($email !== $user['email']) {
        // Kiểm tra email mới có trùng không
        $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $email_check->bind_param("si", $email, $user['id']);
        $email_check->execute();
        if ($email_check->get_result()->num_rows > 0) {
            $errors['email'] = "This email is already taken!";
        }
        $email_check->close();
    }
    if (!empty($phone) && !preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors['phone'] = "Invalid phone number!";
    }

    // Nếu không có lỗi, cập nhật thông tin
    if (empty($errors)) {
        $phone_to_save = $delete_phone ? null : $phone;
        $address_to_save = $delete_address ? null : $address;
        $city_to_save = $delete_address ? null : $city;

        $update_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ? WHERE id = ?");
        $update_stmt->bind_param("sssssi", $name, $email, $phone_to_save, $address_to_save, $city_to_save, $user['id']);
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email; // Cập nhật email trong session
            $user = array_merge($user, [
                'name' => $name,
                'email' => $email,
                'phone' => $phone_to_save,
                'address' => $address_to_save,
                'city' => $city_to_save
            ]);
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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

                        <!-- Thông báo -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success" role="alert"><?= $success ?></div>
                        <?php endif; ?>
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger" role="alert"><?= $errors['general'] ?></div>
                        <?php endif; ?>

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
                                <div class="input-group">
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="delete_phone" id="delete_phone" 
                                                   <?= !$user['phone'] ? 'disabled' : ''; ?>>
                                            <label class="ml-2 mb-0" for="delete_phone">Delete</label>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors['phone'])): ?>
                                    <small class="text-danger"><?= $errors['phone'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Address -->
                            <div class="form-group">
                                <label for="address">Address</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="address" name="address" 
                                           value="<?= htmlspecialchars($user['address'] ?? ''); ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="delete_address" id="delete_address" 
                                                   <?= !$user['address'] ? 'disabled' : ''; ?>>
                                            <label class="ml-2 mb-0" for="delete_address">Delete</label>
                                        </div>
                                    </div>
                                </div>
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
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