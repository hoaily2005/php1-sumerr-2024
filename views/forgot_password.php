<?php
session_start();
require("../mail/PHPMailer/src/PHPMailer.php");
require("../mail/PHPMailer/src/SMTP.php");
require("../mail/PHPMailer/src/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../models/user.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ!";
    } elseif (!ischeckmail($email)) {
        $errors['email'] = "Email không tồn tại!";
    }

    if (empty($errors)) {
        $recipientEmail = $email;
        $otp = generateOTP($email);
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->Username = 'hoailxpk03594@gmail.com';
            $mail->Password = 'cfsr dhpm fwdh uxhk';

            // Recipients
            $mail->setFrom('hoailxpk03594@gmail.com', 'Reset Password');
            $mail->addAddress($recipientEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'OTP for Password Reset';
            $mail->Body = "<p>Xin chào,</p><p>Dưới đây là mã OTP của bạn để đặt lại mật khẩu: <strong>$otp</strong></p><p>Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này.</p><p>Trân trọng,</p><p>Đội ngũ hỗ trợ của chúng tôi</p>";

            // Send the email
            $mail->send();
            $success = 'Mã OTP đã được gửi đến email của bạn <a href="reset_password.php" class = "btn btn-primary w-100">OTP</a> ';
        } catch (Exception $e) {
            $errors['email'] = "Không thể gửi email. Vui lòng thử lại sau.";
        }
    }
}


function generateOTP($email)
{
    $otp = rand(100000, 999999);
    $dbHelper = new DBUntil();
    $dbHelper->execute("UPDATE users SET otp_code = ? WHERE email = ?", [$otp, $email]);
    $_SESSION['email'] = $email;
    return $otp;
}

function ischeckmail($email)
{
    $dbHelper = new DBUntil();
    $result = $dbHelper->select("SELECT * FROM users WHERE email = ?", [$email]);
    return !empty($result);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .red {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="forgot_password.php" class="">
                                <h3 class="text-primary text-uppercase"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                            </a>
                        </div>
                        <h3 class="text-center mb-5">Forgot Password</h3>
                        <?php if (!empty($success)) : ?>
                            <p class="success"><?php echo $success; ?></p>
                        <?php endif; ?>
                        <form action="forgot_password.php" method="post">
                            <input type="text" class="form-control mb-3" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="Email">
                            <?php if (isset($errors['email'])) : ?>
                                <p class="red"><?php echo $errors['email']; ?></p>
                            <?php endif; ?>
                            <input type="submit" class="btn btn-primary btn-user btn-block btn-lg w-100" name="send" value="Send Email">
                            <hr>
                            <p class="text-center mb-0">Remember Password? <a href="../views/signin.php">Sign In</a></p>
                            <hr>
                            <p class="text-center mb-0">Don't have an Account? <a href="../views/signup.php">Sign Up</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/chart/chart.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>

</html>