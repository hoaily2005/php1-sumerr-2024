<?php
include_once(__DIR__ . '/../../controllers/DBUtil.php');
include_once(__DIR__ . '/../../controllers/cart/cart.php');
ini_set('display_errors', '1');

$carts = new Cart();
$dbHelper = new DBUntil();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $action = $_GET['action'];

    if ($action == 'add') {
        $id = $_GET['id'];
        $quantity = $_GET['quantity'];
        $detail = $dbHelper->select("select * from products where id = :id", ['id' => $id]);
        $product = $detail[0];
        if (count($detail) > 0) {
            $carts->add([
                'id' => $detail[0]['id'],
                'name' => $detail[0]['name'],
                'price' => $detail[0]['price'],
                'quantity' => $quantity,
                'image' => $product['image'],
            ]);
            header('Location: ../../views/timezone-master/cart.php');
        }
    } elseif ($action == 'remove') {
        $id = $_GET['id'];
        $carts->remove($id);
        header('Location: ../../views/timezone-master/cart.php'); 
    } elseif ($action == 'update') {
        $id = $_GET['id'];
        $quantity = $_GET['quantity'];
        $carts->update($id, $quantity);
        header('Location: ../../views/timezone-master/cart.php');
    }
}
?>
