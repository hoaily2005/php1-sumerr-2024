<?php
// Start session
session_start();
session_destroy();
header("Location: ../views/signin.php");
exit();
?>