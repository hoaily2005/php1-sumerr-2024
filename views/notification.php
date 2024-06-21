<?php
require_once '../../mail/PHPMailer/src/PHPMailer.php';
require_once '../../mail/PHPMailer/src/SMTP.php';
require_once '../../mail/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOrderConfirmationEmail($customer_email, $customer_name, $order_id, $total_amount, $discount, $customer_address)
{
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 2; 
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hoailxpk03594@gmail.com';
        $mail->Password = 'cfsr dhpm fwdh uxhk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('hoailxpk03594@gmail.com', 'Notification');
        $mail->addAddress($customer_email, $customer_name);

        //Content
        $mail->isHTML(true);
        $mail->Subject = mb_encode_mimeheader('Xác Nhận Đơn Hàng - Cảm Ơn Bạn Đã Mua Hàng!', 'UTF-8', 'Q');
        $mail->Body = "
             <html>
            <head>
            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 10px;
                    text-align: left;
                }
            </style>
            </head>
            <body>
            <p>Kính gửi <strong>$customer_name</strong></p>
            <p>Cảm ơn bạn đã mua hàng!</p>
            <p>Chúng tôi rất vui thông báo rằng đơn hàng <strong>#$order_id</strong> của bạn đã được đặt thành công.</p>
            <p>Dưới đây là chi tiết về đơn hàng của bạn:</p>
            <table>
                <tr>
                    <th>Thông tin</th>
                    <th>Chi tiết</th>
                </tr>
                <tr>
                    <td>Đơn Hàng Số</td>
                    <td>$order_id</td>
                </tr>
                <tr>
                    <td>Được giảm</td>
                    <td>$discount%</td>
                </tr>
                <tr>
                    <td>Tổng Số Tiền</td>
                    <td>$total_amount VND</td>
                </tr>
                <tr>
                    <td>Phương Thức Thanh Toán</td>
                    <td>COD</td>
                </tr>
                <tr>
                    <td>Địa Chỉ Nhận Hàng</td>
                    <td>$customer_address</td>
                </tr>
            </table>
            <p>Bạn sẽ nhận được email khác khi hàng của bạn được giao đi. Bạn có thể theo dõi trạng thái đơn hàng và tìm thêm thông tin về mua hàng của mình bằng cách đăng nhập vào tài khoản của bạn trên trang web của chúng tôi.</p>
            <p>Cảm ơn bạn đã mua hàng với chúng tôi!</p>
            <p>Trân trọng,</p>
            </body>
            </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
function sendOrderConfirmationVNPAY($customer_email, $customer_name, $order_id, $total_amount, $payment_method, $discount, $customer_address)
{
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hoailxpk03594@gmail.com';
        $mail->Password = 'cfsr dhpm fwdh uxhk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('hoailxpk03594@gmail.com', 'Notification'); // Update with your name and email address
        $mail->addAddress($customer_email, $customer_name);

        //Content
        $mail->isHTML(true);
        $mail->Subject = mb_encode_mimeheader('Xác Nhận Đơn Hàng - Cảm Ơn Bạn Đã Mua Hàng!', 'UTF-8', 'Q');
        $mail->Body = "
            <html>
            <head>
            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 10px;
                    text-align: left;
                }
            </style>
            </head>
            <body>
            <p>Kính gửi <strong>$customer_name</strong></p>
            <p>Cảm ơn bạn đã mua hàng!</p>
            <p>Chúng tôi rất vui thông báo rằng đơn hàng <strong>#$order_id</strong> của bạn đã được đặt thành công và đang được xử lý.</p>
            <p>Dưới đây là chi tiết về đơn hàng của bạn:</p>
            <table>
                <tr>
                    <th>Thông tin</th>
                    <th>Chi tiết</th>
                </tr>
                <tr>
                    <td>Đơn Hàng Số</td>
                    <td>$order_id</td>
                </tr>
                <tr>
                    <td>Được giảm</td>
                    <td>$discount%</td>
                </tr>
                <tr>
                    <td>Tổng Số Tiền</td>
                    <td>$total_amount VND</td>
                </tr>
                <tr>
                    <td>Phương Thức Thanh Toán</td>
                    <td>$payment_method</td>
                </tr>
                <tr>
                    <td>Địa Chỉ Nhận Hàng</td>
                    <td>$customer_address</td>
                </tr>
            </table>
            <p>Bạn sẽ nhận được email khác khi hàng của bạn được giao đi. Bạn có thể theo dõi trạng thái đơn hàng và tìm thêm thông tin về mua hàng của mình bằng cách đăng nhập vào tài khoản của bạn trên trang web của chúng tôi.</p>
            <p>Cảm ơn bạn đã mua hàng với chúng tôi!</p>
            <p>Trân trọng,</p>
            </body>
            </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
 

?>
