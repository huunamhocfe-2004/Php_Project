<?php
session_start();
include('layouts/header.php');
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>
<div class="container my-5 text-center">
    <?php if ($status === 'paid') : ?>
        <h2>Thanh toán thành công 🎉</h2>
        <p>Cảm ơn bạn! Đơn hàng của bạn đã được thanh toán.</p>
    <?php elseif ($status === 'cod') : ?>
        <h2>Đặt hàng thành công</h2>
        <p>Bạn đã chọn thanh toán khi nhận hàng. Chúng tôi sẽ liên hệ để giao hàng sớm nhất.</p>
    <?php else : ?>
        <h2>Order success</h2>
        <p>Đơn hàng đã được tạo.</p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-primary mt-3">Về trang chủ</a>
</div>

<?php include('layouts/footer.php'); ?>
