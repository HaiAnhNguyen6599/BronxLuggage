<?php
session_start();

// Hủy session và chuyển hướng về trang index
session_destroy();
header("Location: ../pages/index.php");
exit();
?>
