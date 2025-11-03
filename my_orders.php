<?php

session_start();
include('server/connection.php');

if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
    }
}



if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];


    $stmt = $conn->prepare('SELECT * FROM orders JOIN order_items ON orders.order_id = order_items.order_id WHERE orders.user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();

    // Check if there are any results
    if (!$orders) {
        echo "No orders found.";
        exit();
    }
} else {
    echo "You are not logged in.";
    exit();
}

?>

<?php include('layouts/header.php') ?>

<!--Account page-->
<section class="my-5 py-5">
    <div class="container mx-auto">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                <li class="breadcrumb-item"><a href="account.php">My Account</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Orders</li>
            </ol>
        </nav>
        
  
        <!-- Thanh Menu -->
        <div class="d-flex justify-content-center mb-5">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="account.php" class="nav-link font-weight-bold">
                        <i class="fa-solid fa-user mx-2"></i> Tài khoản
                    </a>
                </li>
                <li class="nav-item">
                    <a href="my_orders.php" class="nav-link font-weight-bold active">
                        <i class="fa-solid fa-cart-shopping mx-2"></i> Đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="account.php?logout=1" class="nav-link font-weight-bold">
                                                    <i class="fa-solid fa-right-from-bracket mx-2"></i> Đăng xuất

                    </a>
                </li>
            </ul>
        </div>

        <!-- Bảng đơn hàng -->
        <div class="account-update text-center">
            <h3 class="text-uppercase mb-4">Đơn hàng của bạn</h3>
            <table class="orders mx-auto">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Giá đơn hàng</th>
                        <th>Số lượng</th>
                        <th>Trạng thái đơn</th>
                        <th>Ngày đặt</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display orders -->
                    <?php while ($row = $orders->fetch_assoc()) { ?>
                        <tr class="text-uppercase font-weight">
                            <td><?php echo $row['order_id']; ?></td>
                            <td><?php echo $row['order_cost']; ?> VND</td>
                            <td><?php echo $row['product_quantity']; ?></td>
                            <td>
                                <?php
                                    $status = $row['order_status'];
                                    $statusClass = 'bg-danger'; // Mặc định là màu đỏ cho "pending"
                                    if ($status === 'shipped') {
                                        $statusClass = 'bg-warning'; // Màu cam cho "shipped"
                                    } elseif ($status === 'delivered') {
                                        $statusClass = 'bg-success'; // Màu xanh cho "delivered"
                                    } elseif ($status === 'cancelled') {
                                        $statusClass = 'bg-primary'; // Màu xanh dương cho "cancelled"
                                    }
                                ?>
                                <span class="badge <?php echo $statusClass; ?> p-2 text-uppercase">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td><?php echo $row['order_date']; ?></td>
                            <td>
                                <form action="order_details.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <input type="submit" name="order_details" class="btn custom-badge m-0" value="Chi tiết">
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>



