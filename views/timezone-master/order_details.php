<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['username'])) {
    header("Location: ../signin.php"); // Redirect to login page if not authenticated
    exit();
}
include_once('../../controllers/DBUtil.php');
$dbHelper = new DBUntil();

// Retrieve the user ID
$username = $_SESSION['username'];
$user_info = $dbHelper->select("SELECT id FROM users WHERE username=:username", array('username' => $username));
$user_id = null;
if (!empty($user_info)) {
    $user_id = $user_info[0]['id']; // Get the user ID
}

// Query to fetch orders associated with the user ID
$orderDetails = [];
if ($user_id) {
    $orderDetails = $dbHelper->select("
        SELECT od.*, o.status AS order_status
        FROM order_details od
        INNER JOIN orders o ON od.order_id = o.id
        WHERE o.user_id = :user_id
    ", array('user_id' => $user_id));
}

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
        <!-- Hero Area Start-->
    <div class="container mt-5">
        <?php if ($orderDetails) : ?>
            <h1 class="mb-4">Order Details</h1>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Số Điện Thoại</th>
                            <th>Người nhận</th>
                            <th>Địa Chỉ Nhận</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Số Lượng</th>
                            <th>Tổng thanh toán</th>
                            <th>Ngày đặt hàng</th>
                            <th>Trạng thái</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $orderDetail) : ?>
                            <tr>
                                <td><?php echo $orderDetail['id']; ?></td>
                                <td><?php echo $orderDetail['customer_phone']; ?></td>
                                <td><?php echo $orderDetail['customer_name']; ?></td>
                                <td><?php echo $orderDetail['customer_address']; ?></td>
                                <td><?php echo $orderDetail['product_name']; ?></td>
                                <td><?php echo $orderDetail['quantity']; ?></td>
                                <td><?php echo number_format($orderDetail['total_amount'], 0, ',', '.') . ' VND'; ?></td>
                                <td><?php echo $orderDetail['created_at']; ?></td>
                                <td><?php echo $orderDetail['order_status']; ?></td>
                                <td>
                                    <a href="../../controllers/view_order.php?id=<?php echo $orderDetail['id']; ?>" class="btn btn-danger">Hủy</a>
                                    <a href="../../views/timezone-master/detaills_order_view.php?id=<?php echo $orderDetail['id']; ?>" class="btn btn-danger">Xem</a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="alert alert-info" role="alert">
                No orders found.
            </div>
        <?php endif; ?>
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