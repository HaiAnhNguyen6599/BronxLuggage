<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$servername = "localhost"; // MYSQL IP Address
$username = "root"; // your username
$password = "12345678"; // your password
$database = "ecommerce"; //  database name

// Connect to MySQL to MySQLi
try {
    $conn = new mysqli($servername, $username, $password, $database);

    // Check Connection
    if ($conn->connect_error) {
        throw new Exception("Connection Failed: " . $conn->connect_error);
    }

    // echo "Connection Success!";
} catch (Exception $e) {
    die("Connection Error: " . $e->getMessage());
}
