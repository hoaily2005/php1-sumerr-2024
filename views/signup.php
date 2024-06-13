<?php
include '../models/user.php';
session_start();
$user = new User();
$errors = [];
function isVietnamesePhoneNumber($number)
{
    return preg_match('/^(03|05|07|08|09|01[2689])[0-9]{8}$/', $number) === 1;
}
function ischeckmail($email)
{
    $dbHelper = new DBUntil();
    $result = $dbHelper->select("SELECT email FROM users WHERE email = ?", [$email]);
    return empty($result);
}

function ischeckusername($username)
{
    $dbHelper = new DBUntil();
    $result = $dbHelper->select("SELECT username FROM users WHERE username = ?", [$username]);
    return empty($result);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (!isset($_POST['username']) || empty(trim($_POST['username']))) {
        $errors['username'] = "Username không được bỏ trống!";
    } elseif (!ischeckusername($_POST['username'])) {
        $errors['username'] = "Username trùng lặp!";
    }
    if (!isset($_POST['password']) || empty(trim($_POST['password']))) {
        $errors['password'] = "Password không được bỏ trống!";
    }
    if (!isset($_POST['passwordconfirm']) || $_POST['password'] !== $_POST['passwordconfirm']) {
        $errors['passwordconfirm'] = "Passwords không trùng khớp!";
    }
    if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email là bắt buộc!";
    } elseif (!ischeckmail($_POST['email'])) {
        $errors['email'] = "Email trùng lặp!";
    }
    if (!isset($_POST['phone']) || empty($_POST['phone'])) {
        $errors['phone'] = "Phone là bắt buộc!";
    } else {
        if (!isVietnamesePhoneNumber($_POST['phone'])) {
            $errors['phone'] = "Phone number không hợp lệ!";
        } else {
            $phone = $_POST['phone'];
        }
    }
    if (!isset($_POST['address']) || empty(trim($_POST['address']))) {
        $errors['address'] = "Address không được bỏ trống!";
    }

    if (empty($errors)) {
        $status = isset($_POST['status']) ? 1 : 0;
        $data = [
            'username' => htmlspecialchars(trim($_POST['username'])),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'email' => htmlspecialchars(trim($_POST['email'])),
            'role' => 'user', // Thiết lập vai trò là 'user'
            'status' => $status,
            'address' => htmlspecialchars(trim($_POST['address'])),
            'phone' => htmlspecialchars(trim($_POST['phone']))
        ];

        try {
            $user->addUser($data);
            header("Location: ../views/signin.php");
            exit();
        } catch (Exception $e) {
            $errors = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
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
    <link href="../ib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .red {
            color: red;
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

        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="signin.php" class="">
                                <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                            </a>
                            <h3>Sign Up</h3>
                        </div>
                        <form action="signup.php" class="" method="post">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="username" id="floatingText" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" placeholder="jhondoe">
                                <label for="floatingText">Username</label>
                                <?php
                                if (isset($errors['username'])) {
                                    echo "<p class='red'>{$errors['username']}</p>";
                                }
                                ?>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="floatingInput" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="name@example.com">
                                <label for="floatingInput">Email address</label>
                                <?php
                                if (isset($errors['email'])) {
                                    echo "<p class='red'>{$errors['email']}</p>";
                                }
                                ?>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="phone" class="form-control" name="phone" id="floatingPhone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>" placeholder="Phone number">
                                <label for="floatingPhone">Phone number</label>
                                <?php
                                if (isset($errors['phone'])) {
                                    echo "<p class='red'>{$errors['phone']}</p>";
                                }
                                ?>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" name="password" id="floatingPassword" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>" placeholder="Password">
                                <label for="floatingPassword">Password</label>
                                <?php
                                if (isset($errors['password'])) {
                                    echo "<p class='red'>{$errors['password']}</p>";
                                }
                                ?>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" name="passwordconfirm" id="password_confirm" value="<?php echo isset($_POST['passwordconfirm']) ? htmlspecialchars($_POST['passwordconfirm']) : '' ?>" placeholder="Password confirm">
                                <label for="password_confirm">Password confirm</label>
                                <?php
                                if (isset($errors['passwordconfirm'])) {
                                    echo "<p class='red'>{$errors['passwordconfirm']}</p>";
                                }
                                ?>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" name="address" id="floatingAddress" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?>" placeholder="Address">
                                <label for="floatingAddress">Address</label>
                                <?php
                                if (isset($errors['address'])) {
                                    echo "<p class='red'>{$errors['address']}</p>";
                                }
                                ?>
                            </div>
                            <input type="submit" class="btn btn-primary btn-user btn-block btn-lg w-100" value=" Register Account" />
                            <p class="text-center mb-0">Already have an Account? <a href="signin.php">Sign In</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
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