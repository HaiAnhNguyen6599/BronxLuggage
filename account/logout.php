<?php
session_start();

// Hủy session và chuyển hướng về trang login
session_destroy();
header("Location: ../pages/index.php");
exit();
?>
