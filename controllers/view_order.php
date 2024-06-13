<?php
include_once('../controllers/DBUtil.php');
$dbHelper = new DBUntil();
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $result = $dbHelper->delete("order_details", "id = :order_id", array('order_id' => $order_id));

    if ($result) {
        header("Location: ../../dashmin-1.0.0/views/timezone-master/order_details.php");
        exit();
    } else {
        echo "Đã xảy ra lỗi khi xóa đơn hàng.";
    }
} else {
    echo "ID đơn hàng không hợp lệ.";
}
?>
