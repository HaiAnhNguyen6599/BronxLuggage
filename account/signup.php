<?php
session_start();
require "../config.php";
require_once '../functions.php';

$errors = []; // Mảng chứa lỗi

// Kiểm tra khi người dùng nhấn nút submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra username
    if (empty($username)) {
        $errors['username'] = "Username cannot be empty!";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username must be at least 3 characters!";
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
        $stmt->bind_param("sss", $username, $email, $hashed_password);

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
     <style>
        /* Định dạng container chính */
.signup-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 30px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
.error { 
            color: red; 
            font-size: 14px; 
            margin-bottom: 10px;
        }
/* Tiêu đề form */
.signup-container h2 {
    text-align: center;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 20px;
}

/* Input fields */
.signup-container input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

/* Nút Đăng Ký */
.signup-container button {
    width: 100%;
    padding: 12px;
    background: #333;
    color: white;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.signup-container button:hover {
    background: #555;
}

/* Liên kết đăng nhập */
.signup-container .login-link {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
}

.signup-container .login-link a {
    font-weight: bold;
    color: #333;
    text-decoration: none;
}

.signup-container .login-link a:hover {
    color: #007bff;
}

     </style>
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

    <div class="signup-container">
        <h2>Create an Account</h2>
        <form action="signup.php" method="POST">
            <input type="text" name="username" placeholder="Username" 
                value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
            <small class="error"><?= isset($errors['username']) ? $errors['username'] : '' ?></small>

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

    </div>

</body>
</html>