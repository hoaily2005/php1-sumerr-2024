<?php
include_once('../DBUtil.php');
ini_set('display_errors', '1');

$dbHelper = new DBUntil();
$id = $_GET['id'];

if (is_numeric($id)) {
    $condition = "id = :id";
    $params = [':id' => $id];
    $dbHelper->delete('coupons', $condition, $params);
}

header("Location: ../../views/index_cupons.php");
exit();
?>
