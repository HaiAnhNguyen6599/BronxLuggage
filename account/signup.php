<?php
require "../config.php";
require_once '../functions.php';

$errors = []; // Mảng chứa lỗi

// Kiểm tra khi người dùng nhấn nút submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra name
    if (empty($name)) {
        $errors['name'] = "name cannot be empty!";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "name must be at least 3 characters!";
    }

    // Kiểm tra email
    if (empty($email)) {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    }

    // Kiểm tra password
    if (empty($password)) {
        $errors['password'] = "Password cannot be empty!";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters!";
    }

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match!";
    }

    // Nếu không có lỗi, tiến hành lưu vào CSDL
    if (empty($errors)) {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful!";
            header("Location: login.php");
            exit();
        } else {
            $errors['database'] = "An error occurred during registration, please try again!";
        }

        $stmt->close();
        $conn->close();
    }
}
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

    <!-- Breadcrumb Start -->
    <?php include '../includes/breadcumb.php' ?>
    <!-- Breadcrumb End -->

    <!-- <div class="signup-container">
        <h2>Create an Account</h2>
        <form action="signup.php" method="POST">
            <input type="text" name="name" placeholder="Username" 
                value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
            <small class="error"><?= isset($errors['name']) ? $errors['name'] : '' ?></small>

            <input type="email" name="email" placeholder="Email" 
                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            <small class="error"><?= isset($errors['email']) ? $errors['email'] : '' ?></small>

            <input type="password" name="password" placeholder="Password">
            <small class="error"><?= isset($errors['password']) ? $errors['password'] : '' ?></small>

            <input type="password" name="confirm_password" placeholder="Confirm Password">
            <small class="error"><?= isset($errors['confirm_password']) ? $errors['confirm_password'] : '' ?></small>

            <button type="submit">Sign Up</button>
            <div class="login-link">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </form>

    </div> -->
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Create an Account</h2>
                        <?php if (isset($errors['database'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $errors['database'] ?>
                            </div>
                        <?php endif; ?>
                        <form action="signup.php" method="POST">
                            <!-- Name -->
                            <div class="form-group">
                                <input type="text" name="name" class="form-control" placeholder="Name"
                                    value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                                <?php if (isset($errors['name'])): ?>
                                    <small class="text-danger"><?= $errors['name'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Email"
                                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <small class="text-danger"><?= $errors['email'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <input type="password" name="password" class="form-control" placeholder="Password">
                                <?php if (isset($errors['password'])): ?>
                                    <small class="text-danger"><?= $errors['password'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <small class="text-danger"><?= $errors['confirm_password'] ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-dark btn-block">Sign Up</button>

                            <!-- Login Link -->
                            <div class="text-center mt-3">
                                <small>Already have an account? <a href="login.php" class="font-weight-bold">Sign in</a></small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Start -->
    <?php include '../includes/footer.php'; ?>
    <!-- Footer End -->
    <!-- JavaScript Libraries  -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>