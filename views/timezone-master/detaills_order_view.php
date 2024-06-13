<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['username'])) {
    header("Location: ../signin.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../../controllers/view_order.php");
    exit();
}

$id = $_GET['id'];
include_once('../../controllers/DBUtil.php');
$dbHelper = new DBUntil();

//Check thông tin user
$username = $_SESSION['username'];
$user_info = $dbHelper->select("SELECT id FROM users WHERE username=:username", array('username' => $username));
$user_id = null;
if (!empty($user_info)) {
    $user_id = $user_info[0]['id'];
}

$orderDetails = [];
if ($user_id) {
    $orderDetails = $dbHelper->select("
        SELECT od.*, o.status AS order_status
        FROM order_details od
        INNER JOIN orders o ON od.order_id = o.id
        WHERE o.user_id = :user_id AND od.id = :order_id
    ", array('user_id' => $user_id, 'order_id' => $id));
}

if (empty($orderDetails)) {
    header("Location: ../../controllers/view_order.php");
    exit();
}
$orderDetail = $orderDetails[0];

?>

<!DOCTYPE html>
<html lang="en">

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Watch shop | eCommers</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!--? Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/logo.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->
    <header>
        <!-- Header Start -->
        <div class="header-area">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="menu-wrapper">
                        <!-- Logo -->
                        <div class="logo">
                            <a href="index.html"><img src="assets/img/logo/logo.png" alt=""></a>
                        </div>
                        <!-- Main-menu -->
                        <div class="main-menu d-none d-lg-block">
                            <nav>
                                <ul id="navigation">

                                    <li><a href="index_user.php">shop</a></li>
                                    <li><a href="about.php">about</a></li>
                                    <li class="hot"><a href="#">Latest</a>
                                        <ul class="submenu">
                                            <li><a href="index_user.php"> Product list</a></li>
                                            <li><a href="product_details.php"> Product Details</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="#">Pages</a>
                                        <ul class="submenu">
                                            <li><a href="cart.php">Cart</a></li>
                                            <li><a href="checkout.php">Product Checkout</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Header Right -->
                        <div class="header-right">
                            <ul>
                                <a href="../../controllers/logout.php" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    <?php if (isset($_SESSION['username'])) : ?>
                                        <span class="d-none d-lg-inline-flex fa fa-user"> Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>

                                    <?php else : ?>
                                        <span>Bạn vui lòng đăng nhập để sử dụng dịch vụ</span>
                                        <a href="../signin.php" class="d-block btn btn-primary w-100">Sign in now</a>
                                    <?php endif; ?>
                                </a>
                            </ul>
                        </div>
                    </div>
                    <!-- Mobile Menu -->
                    <div class="col-12">
                        <div class="mobile_menu d-block d-lg-none"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>
    <?php foreach ($orderDetails as $orderDetail) : ?>
        <div class="container mt-5">
            <h1>Chi tiết đơn hàng #<?php echo $orderDetail['id']; ?></h1>
            <hr>
            <h2>Thông tin khách hàng</h2>
            <p>Số Điện Thoại: <?php echo $orderDetail['customer_phone']; ?></p>
            <p>Người nhận: <?php echo $orderDetail['customer_name']; ?></p>
            <p>Địa chỉ nhận: <?php echo $orderDetail['customer_address']; ?></p>
            <hr>
            <h2>Chi tiết sản phẩm</h2>
            <p>Tên Sản Phẩm: <?php echo $orderDetail['product_name']; ?></p>
            <p>Số Lượng: <?php echo $orderDetail['quantity']; ?></p>
            <hr>
            <h2>Thông tin đơn hàng</h2>
            <p>Ngày đặt hàng: <?php echo $orderDetail['created_at']; ?></p>
            <p>Trạng thái: <?php echo $orderDetail['order_status']; ?></p>
            <p>Tổng thanh toán: <?php echo number_format($orderDetail['total_amount'], 0, ',', '.') . ' VND'; ?></p>
        </div>
    <?php endforeach; ?>
    <div class="container mt-5">
        <a href="../../views/timezone-master/order_details.php" class="btn btn-danger"><i class="fas fa-arrow-left me-2"></i>Back</a>
        <a href="../../views/timezone-master/index_user.php" class="btn btn-danger"><i class="fas fa-shopping-cart me-2"></i>Tiếp tục mua sắm</a>
    </div>
    <!-- Bootstrap JS -->
    <!-- JS here -->
    <!-- All JS Custom Plugins Link Here here -->
    <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="./assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>

    <!-- One Page, Animated-HeadLin -->
    <script src="./assets/js/wow.min.js"></script>
    <script src="./assets/js/animated.headline.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>

    <!-- Scroll up, nice-select, sticky -->
    <script src="./assets/js/jquery.scrollUp.min.js"></script>
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>

    <!-- contact js -->
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>

</body>

</html>