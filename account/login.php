<?php
// Include các file cần thiết
require "../config.php";
require_once '../functions.php';

$email = ''; // Khởi tạo biến email để giữ giá trị

// Kiểm tra nếu có lỗi từ session trước đó
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    $email = $_SESSION['email'] ?? ''; // Lấy lại email từ session nếu có
    unset($_SESSION['errors']); // Xóa lỗi sau khi lấy ra
    unset($_SESSION['email']); // Xóa email sau khi lấy ra
} else {
    $errors = [];
}

// Xử lý form khi được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = "Password cannot be empty!";
    }

    // Nếu không có lỗi validate, kiểm tra database
    if (empty($errors)) {
        if (!$conn) {
            $errors['db'] = "Database connection failed!";
        } else {
            $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            if (!$stmt) {
                $errors['db'] = "Query preparation failed: " . $conn->error;
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                // Kiểm tra tài khoản tồn tại
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $name, $email_db, $hashed_password, $role);
                    $stmt->fetch();

                    // Xác minh mật khẩu
                    if (password_verify($password, $hashed_password)) {
                        // Lưu thông tin người dùng vào session
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['name'] = $name;
                        $_SESSION['email'] = $email_db;
                        $_SESSION['role'] = $role;
                        //thông báo đăng nhập thành công
                        $_SESSION['success'] = "Welcome " . htmlspecialchars($name) . " to your account!";
                        header("Location: ../pages/index.php"); // Chuyển hướng khi đăng nhập thành công
                        exit();
                    } else {
                        $errors['password'] = "Incorrect password!"; // Lỗi sai mật khẩu
                    }
                } else {
                    $errors['email'] = "No account found with this email!";
                }
                $stmt->close();
            }
            $conn->close();
        }
    }

    // Nếu có lỗi, lưu vào session và chuyển hướng
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['email'] = $email; // Lưu email để hiển thị lại
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../includes/head.php'; ?>
</head>
<body>
    <?php include '../includes/topbar.php'; ?>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Login to Your Account</h2>
                        <form action="login.php" method="POST">
                            <!-- Email -->
                            <div class="form-group">
                                <input type="email" id="email" name="email" class="form-control" placeholder="Email"
                                    value="<?= htmlspecialchars($email) ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <small class="text-danger"><?= htmlspecialchars($errors['email']) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                <?php if (isset($errors['password'])): ?>
                                    <small class="text-danger"><?= htmlspecialchars($errors['password']) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Database Error (if any) -->
                            <?php if (isset($errors['db'])): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($errors['db']) ?></div>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-dark btn-block">Login</button>

                            <!-- Signup Link -->
                            <div class="text-center mt-3">
                                <small>Don't have an account? <a href="signup.php" class="font-weight-bold">Sign up</a></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
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
    <?php endif; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>