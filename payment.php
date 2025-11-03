<?php
session_start();
?>

<?php include('layouts/header.php') ?>

<!--Checkout page-->

<section class="my-5 py-5">
        
    <div class="container text-center mt-3 pt-5">
        <h2 class=" text-uppercase text-center"> Payment</h2>
        <hr class="mx-auto">
    </div>

    <div class="container text-center mx-auto">

    <h2 class="text-center">Total:
        <strong><?php echo number_format($_SESSION['total'], 3, '.', '.') . ' VND'; ?></strong>
    </h2>

    <h2 class="text-center pt-4">
        <?php if (isset($_GET['order_status'])) echo $_GET['order_status']; ?>
    </h2>

    <!-- Các nút thanh toán -->
    <div class="payment-methods mt-4">
        <button id="codBtn" class="btn btn-dark mx-2">Thanh toán khi nhận hàng</button>
        <button id="onlineBtn" class="btn btn-primary mx-2">Thanh toán Online (QR)</button>
    </div>

    <!-- Khu vực QR -->
    <div id="qrSection" class="mt-4" style="display:none;">
        <h4>Quét mã QR để thanh toán</h4>
        <img src="assets/images/qr_momo.png" alt="QR Momo" width="200" class="mt-3">
        <p class="mt-2">Sau khi quét mã, vui lòng nhấn "Xác nhận đã thanh toán".</p>
        <button id="confirmPayment" class="btn btn-success">Xác nhận đã thanh toán</button>
    </div>

    <!-- Nút quay lại mua hàng -->
    <div class="return-btn-container mt-4">
        <a href="index.php" class="return-btn btn btn-danger">Continue shopping</a>
    </div>
</div>

</section>

<script>
document.getElementById('onlineBtn').addEventListener('click', function() {
    document.getElementById('qrSection').style.display = 'block';
});

document.getElementById('codBtn').addEventListener('click', function() {
    document.getElementById('qrSection').style.display = 'none';
    alert('Bạn đã chọn thanh toán khi nhận hàng!');
});

document.getElementById('confirmPayment').addEventListener('click', function() {
    alert('Cảm ơn bạn! Hệ thống sẽ xác nhận thanh toán trong giây lát.');
});
</script>

<style>
.payment-methods button {
    font-size: 16px;
    padding: 10px 20px;
    border-radius: 10px;
}
#qrSection {
    background-color: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    display: inline-block;
}
</style>


<?php include('layouts/footer.php') ?>