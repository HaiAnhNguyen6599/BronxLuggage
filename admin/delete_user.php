<?php
require "../config.php";
require_once '../functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['name']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../account/login.php");
    exit();
}

// Check if ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin/manage_users.php?error=Invalid user ID");
    exit();
}

$user_id = (int)$_GET['id'];

// Delete user from database
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: ../admin/manage_users.php?success=User deleted successfully");
    } else {
        header("Location: ../admin/manage_users.php?error=User not found");
    }
} else {
    header("Location: ../admin/manage_users.php?error=Error deleting user: " . $stmt->error);
}

$stmt->close();
$conn->close();
