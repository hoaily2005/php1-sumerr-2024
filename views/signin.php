<?php
include '../models/user.php';
session_start();
$user = new User();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['username']) || empty(trim($_POST['username']))) {
        $errors['username'] = "Username cannot be empty!";
    }
    if (!isset($_POST['password']) || empty(trim($_POST['password']))) {
        $errors['password'] = "Password cannot be empty!";
    }

    if (empty($errors)) {
        $username = htmlspecialchars(trim($_POST['username']));
        $password = $_POST['password'];

        $dbHelper = new DBUntil();
        $result = $dbHelper->select("SELECT * FROM users WHERE username = ?", [$username]);

        if (!empty($result) && password_verify($password, $result[0]['password'])) {
            $_SESSION['username'] = $username;
            $role = $result[0]['role'];
            switch ($role) {
                case '1':
                    header("Location: ../views/index.php");
                    exit();
                case '0':
                    header("Location: ../views/timezone-master/index_user.php");
                    exit();
                default:
                    $errors['invalid'] = "Invalid role";
                    break;
            }
        } else {
            $errors['invalid'] = "Invalid username or password";
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


        <!-- Sign In Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="signin.php" class="">
                                <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                            </a>
                            <h3>Sign In</h3>
                        </div>
                        <form action="signin.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" placeholder="name@example.com">
                                <label for="floatingInput">Username </label>
                                <?php if (isset($errors['username'])) : ?>
                                    <p class="red"><?php echo $errors['username']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="floatingPassword" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>" placeholder="Password">
                                <label for="floatingPassword">Password</label>
                                <?php if (isset($errors['password'])) : ?>
                                    <p class="red"><?php echo $errors['password']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                </div>
                                <a href="forgot_password.php">Forgot Password</a>
                            </div>
                            <input class="btn btn-primary btn-user btn-block btn-lg w-100 " type="submit" name="submit" value="Sign In" />
                            <?php if (isset($errors['invalid'])) : ?>
                                <p class="red"><?php echo $errors['invalid']; ?></p>
                            <?php endif; ?>
                            <p class="text-center mb-0">Don't have an Account? <a href="../views/signup.php">Sign Up</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign In End -->
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