<?php
require_once '../config.php';

$priceFilter = isset($_POST['price']) ? $_POST['price'] : [];
$colorFilter = isset($_POST['color']) ? $_POST['color'] : [];
$sizeFilter = isset($_POST['size']) ? $_POST['size'] : [];

$query = "SELECT * FROM products WHERE 1";

if (!empty($priceFilter)) {
    $priceConditions = [];
    foreach ($priceFilter as $range) {
        list($min, $max) = explode("-", $range);
        $priceConditions[] = "(price BETWEEN $min AND $max)";
    }
    $query .= " AND (" . implode(" OR ", $priceConditions) . ")";
}

if (!empty($colorFilter)) {
    $query .= " AND color IN ('" . implode("','", $colorFilter) . "')";
}

if (!empty($sizeFilter)) {
    $query .= " AND size IN ('" . implode("','", $sizeFilter) . "')";
}

$result = mysqli_query($conn, $query);
while ($product = mysqli_fetch_assoc($result)) {
    echo '<div class="col-lg-4 col-md-6 col-sm-6 pb-1">
            <div class="product-item bg-light mb-4">
                <div class="product-img position-relative overflow-hidden">
                    <img class="img-fluid d-block mx-auto" style="max-width: 250px; height: 200px; object-fit: cover;" src="../' . htmlspecialchars($product['image']) . '" alt="default.png">
                    <div class="product-action">
                        <a class="btn btn-outline-dark btn-square" href=""><i class="fa fa-shopping-cart"></i></a>
                        <a class="btn btn-outline-dark btn-square" href=""><i class="fa fa-search"></i></a>
                    </div>
                </div>
                <div class="text-center py-4">
                    <a class="h6 text-decoration-none text-truncate" href="product.php?id=' . $product['id'] . '">
                        ' . htmlspecialchars($product['name']) . '
                    </a>
                    <div class="d-flex align-items-center justify-content-center mt-2">
                        <h5>$' . number_format($product['price'], 2) . '</h5>
                    </div>
                </div>
            </div>
        </div>';
}
