<?php
session_start();
include('server/connection.php');

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
        exit();
    }
}

// Kiểm tra đăng nhập
if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];

    // Lấy danh sách đơn hàng của người dùng
    $stmt = $conn->prepare('
        SELECT * 
        FROM orders 
        JOIN order_items ON orders.order_id = order_items.order_id 
        WHERE orders.user_id = ?
    ');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();

    // Nếu không có đơn hàng nào
    if (!$orders || $orders->num_rows === 0) {
        $no_orders = true;
    }
} else {
    echo "<div class='alert alert-danger text-center my-5'>Vui lòng đăng nhập để xem đơn hàng của bạn.</div>";
    exit();
}
?>

<?php include('layouts/header.php') ?>

<!-- Trang đơn hàng -->
<section class="my-5 py-5">
    <div class="container mx-auto">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">TRANG CHỦ</a></li>
                <li class="breadcrumb-item"><a href="account.php">TÀI KHOẢN</a></li>
                <li class="breadcrumb-item active" aria-current="page">ĐƠN HÀNG CỦA TÔI</li>
            </ol>
        </nav>

        <!-- Thanh Menu -->
        <div class="d-flex justify-content-center mb-5">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="account.php" class="nav-link font-weight-bold">
                        <i class="fa-solid fa-user mx-2"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="my_orders.php" class="nav-link font-weight-bold active">
                        <i class="fa-solid fa-cart-shopping mx-2"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="account.php?logout=1" class="nav-link font-weight-bold">
                        <i class="fa-solid fa-right-from-bracket mx-2"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Bảng đơn hàng -->
        <div class="account-update text-center">
            <h3 class="text-uppercase mb-4 font-weight-bold">ĐƠN HÀNG CỦA TÔI</h3>

            <?php if (isset($no_orders) && $no_orders): ?>
                <div class="alert alert-info text-center">
                    <p>Bạn chưa có đơn hàng nào.</p>
                </div>
            <?php else: ?>
                <table class="orders mx-auto table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Tổng giá trị</th>
                            <th>Số lượng sản phẩm</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $orders->fetch_assoc()) { ?>
                            <tr class="text-uppercase">
                                <td><?php echo $row['order_id']; ?></td>
                                <td><?php echo number_format($row['order_cost'], 0, ',', '.'); ?> VND</td>
                                <td><?php echo $row['product_quantity']; ?></td>
                                <td>
                                    <?php
                                    $status = $row['order_status'];
                                    $statusClass = 'bg-secondary';

                                    // Gán màu theo trạng thái
                                    if ($status === 'pending') {
                                        $statusClass = 'bg-danger';
                                        $statusText = 'Đang xử lý';
                                    } elseif ($status === 'shipped') {
                                        $statusClass = 'bg-warning';
                                        $statusText = 'Đang giao hàng';
                                    } elseif ($status === 'delivered') {
                                        $statusClass = 'bg-success';
                                        $statusText = 'Đã giao hàng';
                                    } elseif ($status === 'cancelled') {
                                        $statusClass = 'bg-dark';
                                        $statusText = 'Đã hủy';
                                    } else {
                                        $statusText = ucfirst($status);
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?> p-2 text-uppercase">
                                        <?php echo htmlspecialchars($statusText); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($row['order_date'])); ?></td>
                                <td>
                                    <form action="order_details.php" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                        <input type="submit" name="order_details" class="btn btn-primary m-0"
                                            value="Xem chi tiết">
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>
