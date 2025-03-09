<?php
$servername = "localhost"; // Hoặc địa chỉ IP của máy chủ MySQL
$username = "root"; // Thay bằng username của bạn
$password = "12345678"; // Thay bằng password của bạn
$database = "ecommerce"; // Tên database

// Kết nối MySQL với MySQLi
try {
    $conn = new mysqli($servername, $username, $password, $database);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        throw new Exception("Connection Failed: " . $conn->connect_error);
    }

    // echo "Connection Success!";
} catch (Exception $e) {
    die("Connection Error: " . $e->getMessage());
}
