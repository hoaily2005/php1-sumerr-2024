<?php
include_once('../controllers/DBUtil.php');

$dbHelper = new DBUntil();
$type = $_GET['type'];
$id = $_GET['id'];

if ($type == 'category') {
    $dbHelper->delete('categories', "id=$id");
} elseif ($type == 'product') {
    $dbHelper->delete('products', "id=$id");
}

header("Location: ../views/index.php");
exit();
?>