<?php
session_start();
include_once('../controllers/DBUtil.php');

$dbHelper = new DBUntil();

$products = $dbHelper->select("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id");

$product_errors = [];

// Display success message
$success_message = "";
if (isset($_SESSION['success_messages'])) {
    $success_message = $_SESSION['success_messages'];
    unset($_SESSION['success_messages']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['add_product'])) {
        if (!isset($_POST['product_name']) || empty($_POST['product_name'])) {
            $product_errors['name'] = "Tên sản phẩm không được để trống!";
        }
        if (!isset($_POST['product_price']) || empty($_POST['product_price'])) {
            $product_errors['price'] = "Giá sản phẩm không được để trống!";
        }
        if (!isset($_POST['product_category']) || empty($_POST['product_category'])) {
            $product_errors['category'] = "Danh mục sản phẩm không được để trống!";
        }

        if (empty($product_errors)) {
            $image = null;
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $image = $upload_dir . basename($_FILES["product_image"]["name"]);
                move_uploaded_file($_FILES["product_image"]["tmp_name"], $image);
            }

            $data = [
                'name' => $_POST['product_name'],
                'description' => $_POST['product_description'],
                'price' => $_POST['product_price'],
                'image' => $image,
                'category_id' => $_POST['product_category']
            ];

            $isCreated = $dbHelper->insert('products', $data);
            if ($isCreated) {
                $_SESSION['success_messages'] = "Sản phẩm đã được thêm thành công.";
                header("Location: ../views/product.php");
                exit();
            } else {
                $product_errors['general'] = "Lỗi khi tạo sản phẩm.";
            }
        }
    }
    if (isset($_GET['categories'])) {
        $categories = unserialize(urldecode($_GET['categories']));
    } else {
        $categories = $dbHelper->select("SELECT * FROM categories");
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


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></h6>
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link "><i class="fa fa-tachometer-alt me-2"></i>Danh mục</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown"><i class="fa fa-laptop me-2"></i>Product</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="product.php" class="dropdown-item">Add Product</a>
                            <a href="list_product.php" class="dropdown-item">List produc</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>User</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="list_user.php" class="dropdown-item">List User</a>
                            <a href="add_user.php" class="dropdown-item">Add User</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="index_cupons.php" class="nav-link"><i class="far fa-newspaper"></i>Coupons</a>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="admin_order.php" class="nav-link"><i class="far fa-file-alt me-2"></i>Order Status</a>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Search">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-envelope me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Message</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item text-center">See all message</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notificatin</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0">Profile updated</h6>
                                <small>15 minutes ago</small>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0">New user added</h6>
                                <small>15 minutes ago</small>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <h6 class="fw-normal mb-0">Password changed</h6>
                                <small>15 minutes ago</small>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item text-center">See all notifications</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>
                            <a href="../controllers/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <?php if ($success_message) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <h2>Add product</h2>
            <form action="product.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name">
                    <?php if (isset($product_errors['name'])) echo "<span class='text-danger'>{$product_errors['name']}</span>"; ?>
                </div>
                <div class="mb-3">
                    <label for="product_description" class="form-label">Description</label>
                    <textarea class="form-control" id="product_description" name="product_description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="product_price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="product_price" name="product_price">
                    <?php if (isset($product_errors['price'])) echo "<span class='text-danger'>{$product_errors['price']}</span>"; ?>
                </div>
                <div class="mb-3">
                    <label for="product_category" class="form-label">Categories</label>
                    <select class="form-control" id="product_category" name="product_category">
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($product_errors['category'])) echo "<span class='text-danger'>{$product_errors['category']}</span>"; ?>
                </div>


                <div class="mb-3">
                    <label for="product_image" class="form-label">Image</label>
                    <input type="file" class="form-control" id="product_image" name="product_image">
                </div>
                <button type="submit" class="btn btn-primary" name="add_product">Update</button>
                <?php if (isset($product_errors['general'])) echo "<span class='text-danger'>{$product_errors['general']}</span>"; ?>
            </form>

            <!-- Back to Top -->
            <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
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