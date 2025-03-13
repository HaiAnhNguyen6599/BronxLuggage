<?php
session_start();
require "../config.php";
require_once '../functions.php';

$errors = []; // Mảng chứa lỗi

// Kiểm tra khi người dùng nhấn nút submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Kiểm tra email
    if (empty($email)) {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    }

    // Kiểm tra password
    if (empty($password)) {
        $errors['password'] = "Password cannot be empty!";
    }

    // Nếu không có lỗi, kiểm tra trong CSDL
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Kiểm tra tài khoản có tồn tại hay không
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $username, $hashed_password);
            $stmt->fetch();

            // Xác thực mật khẩu
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                header("Location: ../pages/index.php"); // Chuyển hướng sau khi đăng nhập thành công
                exit();
            } else {
                $errors['password'] = "Incorrect password!";
            }
        } else {
            $errors['email'] = "No account found with this email!";
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <?php include '../includes/head.php'; ?>
    <!-- End Meta Tags -->

    <style>
        .login-container {
            max-width: 400px;
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

        .login-container h2 {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
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

        .login-container button:hover {
            background: #555;
        }

        .login-container .signup-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .login-container .signup-link a {
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }

        .login-container .signup-link a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>

    <!-- Topbar Start -->
    <?php include '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <div class="login-container">
        <h2>Login to Your Account</h2>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" 
                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            <small class="error"><?= isset($errors['email']) ? $errors['email'] : '' ?></small>

            <input type="password" name="password" placeholder="Password">
            <small class="error"><?= isset($errors['password']) ? $errors['password'] : '' ?></small>

            <button type="submit">Login</button>
            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </form>
    </div>

</body>
</html>
