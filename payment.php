<?php
session_start();
if (!isset($_SESSION['order_id'])) {
    // Không có order_id: hiển thị thông báo thân thiện
    include('layouts/header.php');
    echo '<div class="container my-5 text-center"><h3>Có lỗi: Không tìm thấy mã đơn hàng. Vui lòng thực hiện đặt hàng rồi quay lại trang thanh toán.</h3><a href="index.php" class="btn btn-primary mt-3">Về trang chủ</a></div>';
    include('layouts/footer.php');
    exit;
}
?>

<?php include('layouts/header.php') ?>

<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class=" text-uppercase text-center"> Payment</h2>
        <hr class="mx-auto">
    </div>

    <div class="container text-center mx-auto">

        <h2 class="text-center">Total:
            <strong><?php echo isset($_SESSION['total']) ? number_format($_SESSION['total'], 3, '.', '.') . ' VND' : '0 VND'; ?></strong>
        </h2>

        <h2 class="text-center pt-4">
            <?php if (isset($_GET['order_status'])) echo htmlspecialchars($_GET['order_status']); ?>
        </h2>

        <!-- Các nút thanh toán -->
        <div class="payment-methods mt-4">
            <button id="codBtn" class="btn btn-dark mx-2">Thanh toán khi nhận hàng (COD)</button>
            <button id="onlineBtn" class="btn btn-primary mx-2">Thanh toán Online (QR)</button>
        </div>

        <!-- Khu vực QR -->
        <div id="qrSection" class="mt-4" style="display:none;">
            <h4>Quét mã QR để thanh toán</h4>
            <!-- Thay đường dẫn ảnh QR vào assets/images/qr_momo.png -->
            <img src="assets/images/qr_momo.png" alt="QR Momo" width="200" class="mt-3">
            <p class="mt-2">Sau khi quét và chuyển tiền, nhấn nút bên dưới để xác nhận.</p>
            <button id="confirmPayment" class="btn btn-success">Tôi đã chuyển tiền — Xác nhận</button>
        </div>

        <div id="resultMsg" class="mt-3"></div>

        <!-- Nút quay lại mua hàng -->
        <div class="return-btn-container mt-4">
            <a href="index.php" class="return-btn btn btn-danger">Continue shopping</a>
        </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>

<script>
document.getElementById('onlineBtn').addEventListener('click', function() {
    document.getElementById('qrSection').style.display = 'block';
    document.getElementById('resultMsg').innerText = '';
});

document.getElementById('codBtn').addEventListener('click', async function() {
    // Gửi request để mark order là COD (đang chờ giao hàng)
    document.getElementById('resultMsg').innerText = 'Đang xử lý...';
    try {
        const res = await fetch('update_payment_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'method=cod'
        });
        const text = await res.text();
        if (text.trim() === 'OK') {
            // Chuyển hướng hoặc hiển thị thông báo
            window.location.href = 'order_success.php?status=cod';
        } else {
            document.getElementById('resultMsg').innerText = 'Có lỗi xảy ra: ' + text;
        }
    } catch (err) {
        document.getElementById('resultMsg').innerText = 'Lỗi kết nối: ' + err;
    }
});

document.getElementById('confirmPayment').addEventListener('click', async function() {
    // Gọi server để cập nhật trạng thái thanh toán online (paid)
    document.getElementById('resultMsg').innerText = 'Đang xác nhận thanh toán...';
    try {
        const res = await fetch('update_payment_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'method=online'
        });
        const text = await res.text();
        if (text.trim() === 'OK') {
            // Hiển thị thông báo và chuyển hướng
            window.location.href = 'order_success.php?status=paid';
        } else {
            document.getElementById('resultMsg').innerText = 'Có lỗi: ' + text;
        }
    } catch (err) {
        document.getElementById('resultMsg').innerText = 'Lỗi kết nối: ' + err;
    }
});
</script>
