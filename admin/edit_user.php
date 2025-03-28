<?php
require "../config.php";
require_once '../functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['name']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../account/login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin/manage_user.php");
    exit();
}

$user_id = (int) $_GET['id'];

// Initialize form values (default to database values or empty)
$stmt = $conn->prepare("SELECT name, email, phone, address, city FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../admin/manage_user.php?error=User not found");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Form submission handling
$errors = [];
$form_values = [
    'name' => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'] ?? '',
    'address' => $user['address'] ?? '',
    'city' => $user['city'] ?? ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_values['name'] = trim($_POST['name']);
    $form_values['email'] = trim($_POST['email']);
    $form_values['phone'] = trim($_POST['phone']);
    $form_values['address'] = trim($_POST['address']);
    $form_values['city'] = trim($_POST['city']);

    // Validation
    if (empty($form_values['name'])) {
        $errors['name'] = "Name is required.";
    }

    if (empty($form_values['email'])) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($form_values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (!empty($form_values['phone']) && !preg_match('/^[0-9]{10}$/', $form_values['phone'])) {
        $errors['phone'] = "Phone must be exactly 10 digits.";
    }

    if (!empty($form_values['address']) && !preg_match('/^[a-zA-Z0-9\s,.-]+$/', $form_values['address'])) {
        $errors['address'] = "Address can only contain letters, numbers, spaces, commas, periods, or hyphens.";
    }

    if (empty($errors)) {
        // Update user in database
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $form_values['name'], $form_values['email'], $form_values['phone'], $form_values['address'], $form_values['city'], $user_id);

        if ($stmt->execute()) {
            $_SESSION['user_update_success'] = "User updated successfully";
        } else {
            $_SESSION['user_update_error'] = "Error updating user: " . $stmt->error;
        }
        $stmt->close();

        header("Location: ../admin/manage_users.php");
        exit();
    } else {
        $_SESSION['user_update_error'] = "Validation errors occurred. Please check your input.";
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

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center">Edit User</h2>

                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            id="name" name="name" value="<?= htmlspecialchars($form_values['name']) ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            id="email" name="email" value="<?= htmlspecialchars($form_values['email']) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                            id="phone" name="phone" value="<?= htmlspecialchars($form_values['phone']) ?>"
                            maxlength="10" pattern="[0-9]{10}" placeholder="e.g., 0123456789">
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                            id="address" name="address" value="<?= htmlspecialchars($form_values['address']) ?>"
                            pattern="[a-zA-Z0-9\s,.-]+">
                        <?php if (isset($errors['address'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>"
                            id="city" name="city" value="<?= htmlspecialchars($form_values['city']) ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="../admin/manage_user.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to User List
                    </a>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <?php $conn->close(); // Close connection at the very end 
    ?>
</body>

</html>