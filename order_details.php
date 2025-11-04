<?php
session_start();
include('server/connection.php');

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
        exit;
    }
}

// Lấy chi tiết đơn hàng nếu có order_id
$order_details = null;
if (isset($_POST['order_details']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    $stmt = $conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();

    // Kiểm tra lỗi truy vấn
    if ($order_details === false) {
        echo "Lỗi truy vấn dữ liệu.";
        exit;
    }
} else {
    echo "Không có mã đơn hàng được cung cấp.";
    exit;
}
?>

<?php include('layouts/header.php') ?>

<!--Account page-->
<section class="my-5 py-5">
    <div class="row container mx-auto">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="account.php">Tài khoản</a></li>
                <li class="breadcrumb-item"><a href="my_orders.php">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
            </ol>
        </nav>

        <div class="info text-center col-md-6 col-lg-12 col-sm-12">
            <div class="account-profile">
                <div class="account-update col-lg-12 col-md-12">
                    <h3 class="text-uppercase mb-4">Chi tiết đơn hàng</h3>

                    <div class="table-container">
                        <table class="orders mt-5">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Kích thước</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Ngày đặt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($order_details && $order_details->num_rows > 0) {
                                    while ($row = $order_details->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['order_id']; ?></td>
                                            <td>
                                                <div class="product-info d-flex align-items-center justify-content-center gap-2">
                                                    <img class="img-fluid" src="./assets/images/<?php echo $row['product_image']; ?>" alt="Ảnh sản phẩm" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <span><?php echo $row['product_name']; ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                // Hiển thị size sản phẩm
                                                switch ($row['product_size']) {
                                                    case 1:
                                                        echo 'S';
                                                        break;
                                                    case 2:
                                                        echo 'M';
                                                        break;
                                                    case 3:
                                                        echo 'L';
                                                        break;
                                                    case 4:
                                                        echo 'XL';
                                                        break;
                                                    default:
                                                        echo 'Free size';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo number_format($row['product_price'], 0, '.', '.'); ?> VND</td>
                                            <td><?php echo $row['product_quantity']; ?></td>
                                            <td><?php echo $row['order_date']; ?></td>
                                        </tr>
                                <?php }
                                } else {
                                    echo "<tr><td colspan='6'>Không tìm thấy đơn hàng nào.</td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CSS để tắt hiệu ứng hover -->
<style>
.orders tr:hover {
    background-color: transparent !important;
    cursor: default !important;
}
.orders td,
.orders th {
    vertical-align: middle;
    text-align: center;
}
</style>

<?php include('layouts/footer.php') ?>
