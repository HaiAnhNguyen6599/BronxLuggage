<?php
require "../config.php";
require_once '../functions.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check admin rights
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../account/login.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $category_id = $_POST['category_id'];
    $brand_id = $_POST['brand_id'];
    $size_id = $_POST['size_id'];
    $color_id = $_POST['color_id'];
    $price = $_POST['price'];
    $gender = $_POST['gender'];

    $sql = "INSERT INTO products (name, description, category_id, brand_id, size_id, color_id, price, gender)
            VALUES ('$name', '$description', '$category_id', '$brand_id', '$size_id', '$color_id', '$price', '$gender')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['add_success'] = "Product added successfully!";
        header("Location: ../admin/manage_products.php");
        exit();
    } else {
        $_SESSION['add_error'] = "Error adding product: " . $conn->error;
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
                <h2 class="card-title text-center">Add Product</h2>

                <?php if (isset($_SESSION['add_error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['add_error']);
                                                    unset($_SESSION['add_error']); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Choose Category</option>
                            <?php
                            $result = $conn->query("SELECT * FROM categories");
                            while ($category = $result->fetch_assoc()) {
                                echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="brand_id">Brand:</label>
                        <select class="form-control" id="brand_id" name="brand_id" required>
                            <option value="">Choose Brand</option>
                            <?php
                            $result = $conn->query("SELECT * FROM brands");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="size_id">Size:</label>
                        <select class="form-control" id="size_id" name="size_id" required>
                            <option value="">Choose Size</option>
                            <?php
                            $result = $conn->query("SELECT * FROM sizes");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="color_id">Color:</label>
                        <select class="form-control" id="color_id" name="color_id" required>
                            <option value="">Choose Color</option>
                            <?php
                            $result = $conn->query("SELECT * FROM colors");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price ($):</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Choose Gender</option>
                            <option value="male">Men</option>
                            <option value="female">Women</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Product</button>
                    <a href="../admin/manage_products.php" class="btn btn-secondary">Back to Product List</a>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <?php $conn->close(); ?>
</body>

</html>