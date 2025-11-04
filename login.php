<?php
session_start();
include('server/connection.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // ✅ Kiểm tra định dạng email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location:login.php?error=Địa chỉ email không hợp lệ");
        exit();
    }

    // ✅ Nếu muốn chỉ cho phép @gmail.com thì bỏ comment đoạn sau
    // if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    //     header("location:login.php?error=Vui lòng sử dụng địa chỉ email có đuôi @gmail.com");
    //     exit();
    // }

    $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password FROM users WHERE user_email = ? AND user_password = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $password);
    if ($stmt->execute()) {
        $stmt->bind_result($user_id, $user_name, $user_email, $user_password);
        $stmt->store_result();
        if ($stmt->num_rows() == 1) {
            $stmt->fetch();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['user_password'] = $user_password;
            $_SESSION['logged_in'] = true;
            header("location:account.php?status=Đăng nhập thành công");
        } else {
            header("location:login.php?error=Email hoặc mật khẩu không đúng");
        }
    }
}
?>

<?php include('layouts/header.php') ?>

<!--Login-->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <p class="fw-bold p-0 m-0 fs-6">Chào mừng bạn đến với</p>
        <p class="login_name-shop fw-bold text-uppercase fs-2 ls-lg">Rakuten</p>
    </div>

    <form class="p-2 login-form-wrapper" id="login-form" action="login.php" method="POST">
        <div class="lgf-wrapper mx-auto container">
            <div class="container text-center text-uppercase mb-3">
                <div class="lg-img d-flex text-center mx-auto" style="width: 100px; height: 100px;">
                    <img src="./assets/images/icon-114x114.png" alt="Logo đăng nhập">
                </div>
                <h2 class="font-weight-bold">Đăng nhập tài khoản</h2>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <div class="alert alert-success" role="alert">
                    <p class="m-0"><?php echo $_GET['status']; ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <p class="m-0 w-100"><?php echo $_GET['error']; ?></p>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <input type="text" placeholder="Nhập email của bạn" id="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Nhập mật khẩu" id="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <input type="submit" value="Đăng nhập" name="login" class="btn-dark bg-success" id="login"
                    onfocus="this.style.boxShadow='none'; this.style.outline='none'; this.style.borderColor='none';">
            </div>
            <div class="form-group">
                <p class="register-txt">
                    Bạn chưa có tài khoản?
                    <a id="register-url" class="btn-text d-inline-flex" href="register.php">Đăng ký ngay</a>
                </p>
            </div>
        </div>
    </form>
</section>

<?php include('layouts/footer.php') ?>
