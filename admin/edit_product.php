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
    $_SESSION['update_error'] = "Invalid product ID";
    header("Location: ../admin/manage_products.php");
    exit();
}

$product_id = (int) $_GET['id'];

// Fetch product data
$stmt = $conn->prepare("SELECT name, description, category_id, brand_id, size_id, color_id, price, gender FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['update_error'] = "Product not found";
    header("Location: ../admin/manage_products.php");
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Initialize form values
$form_values = [
    'name' => $product['name'],
    'description' => $product['description'],
    'category_id' => $product['category_id'],
    'brand_id' => $product['brand_id'],
    'size_id' => $product['size_id'],
    'color_id' => $product['color_id'],
    'price' => $product['price'],
    'gender' => $product['gender']
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_values['name'] = trim($_POST['name']);
    $form_values['description'] = trim($_POST['description']);
    $form_values['category_id'] = $_POST['category_id'];
    $form_values['brand_id'] = $_POST['brand_id'];
    $form_values['size_id'] = $_POST['size_id'];
    $form_values['color_id'] = $_POST['color_id'];
    $form_values['price'] = $_POST['price'];
    $form_values['gender'] = $_POST['gender'];

    // Validation
    if (empty($form_values['name'])) {
        $errors['name'] = "Product name is required.";
    }
    if (empty($form_values['category_id'])) {
        $errors['category_id'] = "Category is required.";
    }
    if (empty($form_values['brand_id'])) {
        $errors['brand_id'] = "Brand is required.";
    }
    if (empty($form_values['size_id'])) {
        $errors['size_id'] = "Size is required.";
    }
    if (empty($form_values['color_id'])) {
        $errors['color_id'] = "Color is required.";
    }
    if (empty($form_values['price']) || !is_numeric($form_values['price']) || $form_values['price'] < 0) {
        $errors['price'] = "Price must be a positive number.";
    }
    if (empty($form_values['gender'])) {
        $errors['gender'] = "Gender is required.";
    }

    if (empty($errors)) {
        // Update product in database
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, category_id = ?, brand_id = ?, size_id = ?, color_id = ?, price = ?, gender = ? WHERE id = ?");
        $stmt->bind_param("ssiiiidsi", $form_values['name'], $form_values['description'], $form_values['category_id'], $form_values['brand_id'], $form_values['size_id'], $form_values['color_id'], $form_values['price'], $form_values['gender'], $product_id);

        if ($stmt->execute()) {
            $_SESSION['update_success'] = "Product updated successfully";
        } else {
            $_SESSION['update_error'] = "Error updating product: " . $stmt->error;
        }
        $stmt->close();

        header("Location: ../admin/manage_products.php");
        exit();
    } else {
        $_SESSION['update_error'] = "Validation errors occurred. Please check your input.";
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
                <h2 class="card-title text-center">Edit Product</h2>

                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            id="name" name="name" value="<?= htmlspecialchars($form_values['name']) ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                            id="description" name="description"
                            rows="4"><?= htmlspecialchars($form_values['description']) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select class="form-control <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>"
                            id="category_id" name="category_id" required>
                            <option value="">Choose Category</option>
                            <?php
                            $result = $conn->query("SELECT * FROM categories");
                            while ($category = $result->fetch_assoc()) {
                                $selected = $form_values['category_id'] == $category['id'] ? 'selected' : '';
                                echo "<option value='" . $category['id'] . "' $selected>" . htmlspecialchars($category['name']) . "</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['category_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="brand_id">Brand:</label>
                        <select class="form-control <?= isset($errors['brand_id']) ? 'is-invalid' : '' ?>" id="brand_id"
                            name="brand_id" required>
                            <option value="">Choose Brand</option>
                            <?php
                            $result = $conn->query("SELECT * FROM brands");
                            while ($row = $result->fetch_assoc()) {
                                $selected = $form_values['brand_id'] == $row['id'] ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['brand_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['brand_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="size_id">Size:</label>
                        <select class="form-control <?= isset($errors['size_id']) ? 'is-invalid' : '' ?>" id="size_id"
                            name="size_id" required>
                            <option value="">Choose Size</option>
                            <?php
                            $result = $conn->query("SELECT * FROM sizes");
                            while ($row = $result->fetch_assoc()) {
                                $selected = $form_values['size_id'] == $row['id'] ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['size_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['size_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="color_id">Color:</label>
                        <select class="form-control <?= isset($errors['color_id']) ? 'is-invalid' : '' ?>" id="color_id"
                            name="color_id" required>
                            <option value="">Choose Color</option>
                            <?php
                            $result = $conn->query("SELECT * FROM colors");
                            while ($row = $result->fetch_assoc()) {
                                $selected = $form_values['color_id'] == $row['id'] ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['color_id'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['color_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($):</label>
                        <input type="number" step="0.01" min="1" max="1000"
                            class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" id="price"
                            name="price" value="<?= htmlspecialchars($form_values['price'] ?? '') ?>" required>
                        <?php if (isset($errors['price'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" id="gender"
                            name="gender" required>
                            <option value="">Choose Gender</option>
                            <option value="male" <?= $form_values['gender'] === 'male' ? 'selected' : '' ?>>Men</option>
                            <option value="female" <?= $form_values['gender'] === 'female' ? 'selected' : '' ?>>Women
                            </option>
                        </select>
                        <?php if (isset($errors['gender'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['gender']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="../admin/manage_products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Product List
                    </a>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <?php $conn->close(); // Close connection at the end 
    ?>
</body>

</html>