<?php
include '../models/user.php';

$user = new User();
$user->deleteUser($_GET['id']);
header("Location: ../views/list_user.php");
?>
