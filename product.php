<?php

require 'config.php';

if (isset($_GET['id'])) {
    echo "123";
    $product_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * from products where id =?");
    $stmt->bind_param('i', $product_id);

    $stmt->execute();

    $product = $stmt->get_result();
} else {
    echo "Error No Product Found";
    // header('location: index.php');
}
