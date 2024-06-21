<?php
require_once '../notification.php';
include_once('../../controllers/DBUtil.php');
include_once('../../controllers/cart/cart.php');
ini_set('display_errors', '1');

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: ../signin.php");
    exit();
}

// Khởi động session nếu chưa tồn tại
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy thông tin người dùng nếu đã đăng nhập
$email = '';
$phone = '';
$address = '';
if (isset($_SESSION['username'])) {
    $dbHelper = new DBUntil();
    $username = $_SESSION['username'];
    $user_info = $dbHelper->select("SELECT email, phone, id, address FROM users WHERE username=:username", array('username' => $username));

    if (!empty($user_info)) {
        $user_id = $user_info[0]['id'];
        $email = $user_info[0]['email'];
        $phone = $user_info[0]['phone'];
        $address = $user_info[0]['address'];
    }
}

// Khởi tạo đối tượng DBUntil và lấy danh sách sản phẩm
$dbHelper = new DBUntil();
$categories = $dbHelper->select("SELECT * FROM products");
$errors = [];
$carts = new Cart();
$discount = 0;

// Lấy giảm giá nếu có
if (isset($_SESSION['discount'])) {
    $discount = $_SESSION['discount'];
}

// Hàm kiểm tra mã giảm giá
function checkCode($code)
{
    global $dbHelper;
    $sql = $dbHelper->select(
        "SELECT * FROM coupons WHERE code = :code AND quantity > 0 AND 
        startDate <= :currentDate AND endDate >= :currentDate",
        array(
            'code' => $code,
            'currentDate' => date("Y-m-d")
        )
    );
    return count($sql) > 0 ? $sql[0] : null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'checkout') {
        if ($_POST['action'] == 'checkCode') {
            if (!empty($_POST['code'])) {
                $isCheck = checkCode($_POST['code']);
                if (!empty($isCheck)) {
                    $_SESSION['discount'] = $isCheck['discount'];
                    $_SESSION['coupon_code'] = $isCheck['code'];
                    $discount = $isCheck['discount'];
                }
            }
        } elseif ($_POST['action'] == 'checkout') {
            // Xử lý lưu thông tin đơn hàng vào database ở đây...
            $customer_name = $_POST['middleName'] . ' ' . $_POST['name'];
            $customer_email = $_POST['email'];
            $customer_phone = $_POST['phone'];
            $customer_address = $_POST['address'];
            // Assign payment method, defaulting to 'COD' if not provided or not 'VNPAY'
            $paymentMethod = isset($_POST['payment']) ? $_POST['payment'] : '';
            $discount = $_SESSION['discount'];
            $total_amount = $carts->getTotal() - ($discount * $carts->getTotal() / 100);

            // Lưu thông tin đơn hàng vào database
            $order_status = ($paymentMethod == 'vnpay') ? 'Success' : 'Pending';

            $order_id = $dbHelper->insert("orders", array(
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'customer_address' => $customer_address,
                'payment_method' => $paymentMethod,
                'total_amount' => $total_amount,
                'status' => $order_status,
                'discount' => $discount,
                'coupon_code' => isset($_SESSION['coupon_code']) ? $_SESSION['coupon_code'] : null
            ));

            // Lưu chi tiết đơn hàng vào database
            foreach ($carts->getCart() as $item) {
                $subTotal = $item['quantity'] * $item['price'];
                $dbHelper->insert("order_details", array(
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $subTotal,
                    'total_amount' => $total_amount,
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'customer_phone' => $customer_phone,
                    'customer_address' => $customer_address,
                ));
            }
            if ($paymentMethod == 'vnpay') {
                require_once '../timezone-master/payment_service.php';
                PaymentService::createUrlPayment($order_id, $total_amount); // gọi hàm thanh toán VNPAY
                exit();
            }
            // if (sendOrderConfirmationEmail($customer_email, $customer_name, $order_id, $total_amount, $payment_method, $discount, $customer_address)) {
            //     echo '<script>window.location.href="../timezone-master/order_details.php";</script>';
            //     exit();
            // } else {
            //     $error_message = "Không thể gửi email.";
            // }
            if (sendOrderConfirmationEmail($customer_email, $customer_name, $order_id, $total_amount, $discount, $customer_address)) {
                echo '<script>window.location.href="../timezone-master/order_details.php";</script>';
                exit();
            } elseif ($paymentMethod == 'vnpay' && sendOrderConfirmationVNPAY($customer_email, $customer_name, $order_id, $total_amount, $payment_method, $discount, $customer_address)) {
                echo '<script>window.location.href="../timezone-master/order_details.php";</script>';
                exit();
            } else {
                $error_message = "Không thể gửi email.";
            }
            
        }
    }
}
?>

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
    <main>
        <!-- Hero Area Start-->
        <div class="slider-area ">
            <div class="single-slider slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap text-center">
                                <h2>Checkout</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--================Checkout Area =================-->
        <section class="checkout_area section_padding">
            <div class="container">
                <div class="py-5 text-center">
                    <h2>Checkout form</h2>
                </div>
                <div class="row">
                    <div class="col-md-4 order-md-2 mb-4">
                        <h4 class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Your cart</span>
                        </h4>
                        <ul class="list-group mb-3">
                            <?php
                            // Vòng lặp hiển thị thông tin giỏ hàng 
                            foreach ($carts->getCart() as $item) {
                                $subTotal = $item['quantity'] * $item['price'];
                                echo "<li class='list-group-item d-flex justify-content-between lh-condensed'>";
                                echo "<div>";
                                echo "<h6 class='my-0'>$item[name]</h6>";
                                echo "<td>";
                                if (isset($item['image']) && !empty($item['image'])) {
                                    echo "<img src='" . htmlspecialchars($item['image']) . "' alt='" . htmlspecialchars($item['name']) . "' class='img-fluid' style='max-width: 50px;'>";
                                }
                                echo "</td>";
                                echo "<small class='text-muted'>Quantity: $item[quantity]</small>";
                                echo "</div>";
                                echo "<span class='text-muted'>$item[price]</span>";
                                echo "</li>";
                            }
                            ?>
                        </ul>
                        <div class="list-group mb-3">
                            <span>Discount: <span style="color: red;"><?= number_format($discount * $carts->getTotal() / 100, 2) ?> VND</span></span><br>
                            <span>Total: <span style="color: red;"><?= number_format($carts->getTotal() - ($discount * $carts->getTotal()) / 100, 2) ?> VND</span></span>
                        </div>
                    </div>
                    <div class="col-md-8 order-md-1">
                        <h4 class="mb-3">Customer Information</h4>
                        <form id="checkout" class="needs-validation" novalidate action="" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="middleName">Middle name</label>
                                    <input type="text" class="form-control" id="middleName" name="middleName" placeholder="Họ + Tên lót" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Tên" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" value="<?php echo $email; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone number" value="<?php echo $phone; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" value="<?php echo $address; ?>" required>
                            </div>
                            <hr class="mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="same-address">
                                <label class="custom-control-label" for="same-address">Shipping address is the same as my billing address</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="save-info">
                                <label class="custom-control-label" for="save-info">Save this information for next time</label>
                            </div>
                            <hr class="mb-4">
                            <h4 class="mb-3">Payment</h4>
                            <div class="checkout__input__checkbox">
                                <label for="payment-cod">
                                    COD
                                    <input name="payment" value="cod" type="radio" id="payment-cod">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="checkout__input__checkbox">
                                <label for="payment-vnpay">
                                    VNPAY
                                    <input name="payment" value="vnpay" type="radio" id="payment-vnpay">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <button class="btn btn-danger btn-lg btn-block" name="action" value="checkout" type="submit">Thanh Toán Ngay</button>
                        </form>
                        <hr class="mb-4">
                        <?php
                        // Hiển thị thông báo thành công hoặc lỗi sau khi submit form
                        if (isset($success_message)) {
                            echo "<div class='alert alert-success mt-4' role='alert'>$success_message</div>";
                        }
                        if (isset($error_message)) {
                            echo "<div class='alert alert-danger mt-4' role='alert'>$error_message</div>";
                        }
                        ?>

                    </div>
                </div>
                <footer class="my-5 pt-5 text-muted text-center text-small">
                    <p class="mb-1">&copy; 2017-2019 Company Name</p>
                    <ul class="list-inline">
                        <li class="list-inline-item"><a href="#">Privacy</a></li>
                        <li class="list-inline-item"><a href="#">Terms</a></li>
                        <li class="list-inline-item"><a href="#">Support</a></li>
                    </ul>
                </footer>
            </div>
        </section>
        <!--================End Checkout Area =================-->
    </main>
    <footer>
        <!-- Footer Start-->
        <div class="footer-area footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6">
                        <div class="single-footer-caption mb-50">
                            <div class="single-footer-caption mb-30">
                                <!-- logo -->
                                <div class="footer-logo">
                                    <a href="index.php"><img src="assets/img/logo/logo2_footer.png" alt=""></a>
                                </div>
                                <div class="footer-tittle">
                                    <div class="footer-pera">
                                        <p>Asorem ipsum adipolor sdit amet, consectetur adipisicing elitcf sed do eiusmod tem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-3 col-sm-5">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Quick Links</h4>
                                <ul>
                                    <li><a href="#">About</a></li>
                                    <li><a href="#"> Offers & Discounts</a></li>
                                    <li><a href="#"> Get Coupon</a></li>
                                    <li><a href="#"> Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-7">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>New Products</h4>
                                <ul>
                                    <li><a href="#">Woman Cloth</a></li>
                                    <li><a href="#">Fashion Accessories</a></li>
                                    <li><a href="#"> Man Accessories</a></li>
                                    <li><a href="#"> Rubber made Toys</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-7">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-tittle">
                                <h4>Support</h4>
                                <ul>
                                    <li><a href="#">Frequently Asked Questions</a></li>
                                    <li><a href="#">Terms & Conditions</a></li>
                                    <li><a href="#">Privacy Policy</a></li>
                                    <li><a href="#">Report a Payment Issue</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer bottom -->
                <div class="row align-items-center">
                    <div class="col-xl-7 col-lg-8 col-md-7">
                        <div class="footer-copy-right">
                            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                Copyright &copy;<script>
                                    document.write(new Date().getFullYear());
                                </script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-4 col-md-5">
                        <div class="footer-copy-right f-right">
                            <!-- social -->
                            <div class="footer-social">
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.facebook.com/sai4ull"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-behance"></i></a>
                                <a href="#"><i class="fas fa-globe"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End-->
    </footer>
    <!--? Search model Begin -->
    <div class="search-model-box">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-btn">+</div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Searching key.....">
            </form>
        </div>
    </div>
    <!-- Search model end -->

    <!-- JS here -->

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