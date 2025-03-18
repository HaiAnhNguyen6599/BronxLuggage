<?php
// Include các file cần thiết
require "../config.php";
require_once '../functions.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = '';

// Lấy thông tin người dùng hiện tại từ session
$user_id = $_SESSION['user_id'];

// Xử lý form khi được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = trim($_POST['old_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate mật khẩu cũ
    if (empty($old_password)) {
        $errors['old_password'] = "Old password cannot be empty!";
    }

    // Validate mật khẩu mới
    if (empty($new_password)) {
        $errors['new_password'] = "New password cannot be empty!";
    } elseif (strlen($new_password) < 8) {
        $errors['new_password'] = "New password must be at least 8 characters long!";
    }

    // Validate xác nhận mật khẩu
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your new password!";
    } elseif ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match!";
    }

    // Nếu không có lỗi validate, kiểm tra database
    if (empty($errors)) {
        if (!$conn) {
            $errors['db'] = "Database connection failed!";
        } else {
            // Lấy mật khẩu hiện tại từ database
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            if (!$stmt) {
                $errors['db'] = "Query preparation failed: " . $conn->error;
            } else {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($hashed_password);
                    $stmt->fetch();

                    // Xác minh mật khẩu cũ
                    if (password_verify($old_password, $hashed_password)) {
                        // Mã hóa mật khẩu mới
                        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Cập nhật mật khẩu mới vào database
                        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                        if (!$update_stmt) {
                            $errors['db'] = "Update preparation failed: " . $conn->error;
                        } else {
                            $update_stmt->bind_param("si", $new_hashed_password, $user_id);
                            if ($update_stmt->execute()) {
                                $_SESSION['success'] = "Password changed successfully!";
                                header("Location: change_password.php"); // Tải lại trang để hiển thị thông báo
                                exit();
                            } else {
                                $errors['db'] = "Failed to update password: " . $update_stmt->error;
                            }
                            $update_stmt->close();
                        }
                    } else {
                        $errors['old_password'] = "Incorrect old password!";
                    }
                } else {
                    $errors['db'] = "User not found!";
                }
                $stmt->close();
            }
            $conn->close();
        }
    }

    // Nếu có lỗi, lưu vào session và tải lại trang
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: change_password.php");
        exit();
    }
}

// Kiểm tra lỗi từ session
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}

// Kiểm tra thông báo thành công từ session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
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
                        <h2 class="card-title text-center mb-4">Change Your Password</h2>

                        <!-- Hiển thị thông báo thành công -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form action="change_password.php" method="POST">
                            <!-- Old Password -->
                            <div class="form-group">
                                <input type="password" id="old_password" name="old_password" class="form-control" placeholder="Old Password">
                                <?php if (isset($errors['old_password'])): ?>
                                    <small class="text-danger"><?= htmlspecialchars($errors['old_password']) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- New Password -->
                            <div class="form-group">
                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="New Password">
                                <?php if (isset($errors['new_password'])): ?>
                                    <small class="text-danger"><?= htmlspecialchars($errors['new_password']) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm New Password -->
                            <div class="form-group">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm New Password">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <small class="text-danger"><?= htmlspecialchars($errors['confirm_password']) ?></small>
                                <?php endif; ?>
                            </div>

                            <!-- Database Error (if any) -->
                            <?php if (isset($errors['db'])): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($errors['db']) ?></div>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-dark btn-block">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>