<?php
session_start();
include "../controllers/DBUtil.php";

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

    // Validate passwords
    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu!";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Mật khẩu không khớp!";
    }

    // Validate OTP 
    if (empty($otp)) {
        $errors['otp'] = "Vui lòng nhập mã OTP!";
    } else {
        $email = $_SESSION['email'];
        $dbHelper = new DBUntil();
        $result = $dbHelper->select("SELECT otp_code FROM users WHERE email = ?", [$email]);

        if (empty($result)) {
            $errors['otp'] = "Không tìm thấy mã OTP cho email này!";
        } else {
            $otpFromDB = $result[0]['otp_code'];

            if ($otp !== $otpFromDB) {
                $errors['otp'] = "Mã OTP không chính xác!";
            } else {
                $new_password = password_hash($password, PASSWORD_BCRYPT);
                $dbHelper->execute("UPDATE users SET password = ?, otp_code = NULL WHERE email = ?", [$new_password, $email]);

                $success = "Mật khẩu của bạn đã được cập nhật thành công! <a href='signin.php' class='btn btn-primary w-100'>Quay lại trang đăng nhập</a>";
                unset($_SESSION['email']);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
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
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Reset Password Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="reset_password.php" class="">
                                <h3 class="text-primary text-uppercase"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                            </a>
                        </div>
                        <h3 class="text-center mb-5">Reset Password</h3>
                        <?php if ($success) : ?>
                            <p class="success"><?php echo $success; ?></p>
                        <?php endif; ?>
                        <form action="reset_password.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingOTP" name="otp" placeholder="OTP">
                                <label for="floatingOTP">OTP</label>
                                <?php if (isset($errors['otp'])) : ?>
                                    <p class="red"><?php echo $errors['otp']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="New Password">
                                <label for="floatingPassword">New Password</label>
                                <?php if (isset($errors['password'])) : ?>
                                    <p class="red"><?php echo $errors['password']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="floatingConfirmPassword" name="confirm_password" placeholder="Confirm New Password">
                                <label for="floatingConfirmPassword">Confirm New Password</label>
                                <?php if (isset($errors['confirm_password'])) : ?>
                                    <p class="red"><?php echo $errors['confirm_password']; ?></p>
                                <?php endif; ?>
                            </div>
                            <input class="btn btn-primary btn-user btn-block btn-lg w-100" type="submit" name="submit" value="Reset Password" />
                            <p class="text-center mb-0">Remember your password? <a href="signin.php">Sign In</a></p>
                            <p class="text-center mb-0">Don't have an account? <a href="signup.php">Sign Up</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reset Password End -->
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