<?php
session_start();
include_once('../controllers/DBUtil.php');
ini_set('display_errors', '1');

$dbHelper = new DBUntil();

// function isAdmin() {
//     return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
// }

// if (!isAdmin()) {
//     header("Location: signin.php");
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'updateStatus' && isset($_POST['order_id'], $_POST['status'])) {
            $order_id = $_POST['order_id'];
            $status = $_POST['status'];
            try {
                $dbHelper->update(
                    "orders",
                    array('status' => $status),
                    "id = :order_id",
                    array('order_id' => $order_id)
                );
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch orders for management
$orders = $dbHelper->select("SELECT * FROM orders");

// Fetch order statistics
$totalRevenue = $dbHelper->select("SELECT SUM(total_amount) as totalRevenue FROM orders")[0]['totalRevenue'];
$orderCount = $dbHelper->select("SELECT COUNT(*) as orderCount FROM orders")[0]['orderCount'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?> </h6>
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="index.php" class="nav-item nav-link "><i class="fa fa-tachometer-alt me-2"></i>Danh mục</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-laptop me-2"></i>Product</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="product.php" class="dropdown-item">Add Product</a>
                            <a href="list_product.php" class="dropdown-item">List produc</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-user me-2"></i>User</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="list_user.php" class="dropdown-item">List User</a>
                            <a href="add_user.php" class="dropdown-item">Add User</a>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="index_cupons.php" class="nav-link "><i class="far fa-newspaper"></i>Coupons</a>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="admin_order.php" class="nav-link active "><i class="far fa-file-alt me-2"></i>Order Status</a>
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
                                    <img class="rounded-circle" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                                    <div class="ms-2">
                                        <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                        <small>15 minutes ago</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
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
                            <span class="d-none d-lg-inline-flex">
                                Welcome, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="signin.php" class="dropdown-item">Sign in</a>
                            <a href="signup.php" class="dropdown-item">Sign up</a>
                            <a href="../controllers/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="mb-3">Order Statistics</h4>
                        <p>Tổng doanh thu: <?= number_format($totalRevenue, 0) ?> VND</p>
                        <p>Tổng đơn hàng: <?= $orderCount ?>
                        </p>
                        <canvas id="orderChart" width="400" height="200"></canvas>
                        <h4 class="mb-3">Manage Orders</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td><?= $order['id'] ?></td>
                                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                        <td><?= htmlspecialchars($order['customer_address']) ?></td>
                                        <td><?= number_format($order['total_amount']) ?> VND</td>
                                        <td><?= htmlspecialchars($order['status']) ?></td>
                                        <td>
                                            <form method="POST" action="">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <select name="status" class="form-select" required>
                                                    <option value="Chờ xác nhận" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                                                    <option value="Đang giao" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Đang giao</option>
                                                    <option value="Đã giao hàng" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Đã giao hàng</option>
                                                    <option value="Đã hủy" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                                </select>
                                                <button type="submit" name="action" value="updateStatus" class="btn btn-primary mt-2">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <script>
                            var orderData = <?php echo json_encode($orders); ?>;
                            var orderCountByStatus = {
                                'Chưa giải quyết': 0,
                                'Đang giao': 0,
                                'Đã giao hàng': 0,
                                'Đã hủy': 0
                            };

                            orderData.forEach(function(order) {
                                orderCountByStatus[order.status]++;
                            });

                            var labels = Object.keys(orderCountByStatus);
                            var data = Object.values(orderCountByStatus);

                            var ctx = document.getElementById('orderChart').getContext('2d');
                            var orderChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Trạng thái đơn hàng',
                                        data: data,
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.2)',
                                            'rgba(54, 162, 235, 0.2)',
                                            'rgba(255, 206, 86, 0.2)',
                                            'rgba(75, 192, 192, 0.2)',
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>

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