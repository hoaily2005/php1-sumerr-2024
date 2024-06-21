<?php
include_once(__DIR__ . '../../notification.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define("VNPAY_TMN_CODE", "G8G83EPY");
define("VNPAY_HASH_SECRET", "389Z2WYH4ISFYFZ3BVJZ3K88DIM0IC70");
define("VNPAY_URL", "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html");
define("VNPAY_RETURN_URL", "http://localhost:3000/xampp/htdocs/php1/asm2/dashmin-1.0.0/views/timezone-master/order_details.php");
$dsn = 'mysql:host=localhost;dbname=asm-gd1;charset=utf8mb4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    // Thiết lập PDO để ném ngoại lệ cho các lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Lỗi kết nối: ' . $e->getMessage();
    die(); // Dừng chương trình nếu không thể kết nối được đến cơ sở dữ liệu
}
//Config input format
//Expire
$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
//End Config

function convertToDateTime($dateString)
{
    // Tạo đối tượng DateTime với định dạng yyyyMMddHHmmss và đặt múi giờ GMT+7
    $dateTime = DateTime::createFromFormat('YmdHis', $dateString, new DateTimeZone('Asia/Bangkok')); // GMT+7

    if ($dateTime === false) {
        return "Invalid date format.";
    } else {
        return $dateTime;
    }
}

class PaymentService
{
    static public function createUrlPayment($order_id, $total)
    {
        $vnp_TmnCode = VNPAY_TMN_CODE; // Your VNPAY Terminal Code
        $vnp_HashSecret = VNPAY_HASH_SECRET; // Your VNPAY Hash Secret
        $vnp_Url = VNPAY_URL; // VNPAY Payment URL
        $vnp_Returnurl = VNPAY_RETURN_URL; // Your return URL after payment

        $vnp_TxnRef = $order_id; // Order ID (should be generated uniquely for each transaction)
        $vnp_OrderInfo = 'Payment for order ' . $order_id; // Order description or information
        $vnp_OrderType = 'billpayment'; // Order type (if applicable)
        $vnp_Amount = $total * 100; // Amount in the smallest currency unit (cents)
        $vnp_Locale = 'vn'; // Locale
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // Customer's IP address
        $vnp_CreateDate = date('YmdHis'); // Current date time in VNPAY format

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        // Sort the data by keys
        ksort($inputData);

        // Create the query string for URL
        $query = http_build_query($inputData);

        // Create the secure hash
        $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

        // Append the secure hash to the URL
        $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

        // Redirect to VNPAY payment page
        header('Location: ' . $vnp_Url);
        exit();
    }
    static public function handleVnpayCallback()
    {
        global $pdo;
        if ($_GET['vnp_ResponseCode'] == '00') { 
            $order_id = $_GET['vnp_TxnRef']; 
            $success_status = "thành công"; 
            $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['status' => $success_status, 'order_id' => $order_id]);
    
            // Gửi email thông báo đơn hàng đã thanh toán thành công
            $sql = "SELECT * FROM orders WHERE order_id = :order_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['order_id' => $order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($order) {
                $customer_email = $order['customer_email'];
                $customer_name = $order['customer_name'];
                $total_amount = $order['total_amount'];
                $payment_method = $order['payment_method'];
                $discount = $order['discount'];
                $customer_address = $order['customer_address'];
    
                // Gọi hàm gửi email thông báo đơn hàng đã thanh toán thành công
                if (sendOrderConfirmationVNPAY($customer_email, $customer_name, $order_id, $total_amount, $payment_method, $discount, $customer_address)) {
                    echo '<script>window.location.href="../timezone-master/order_details.php?order_id=' . $order_id . '";</script>';
                    exit();
                } else {
                    echo "Error: Unable to send confirmation email.";
                }
            } else {
                echo "Error: Order not found.";
            }
        } else {
            echo "Payment failed or invalid response.";
        }
    }
}

if (isset($_GET['vnp_ResponseCode'])) {
    PaymentService::handleVnpayCallback();
}


