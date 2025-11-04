<?php
session_start();
include('server/connection.php');

if (isset($_POST['register'])) {
    $username = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ Kiểm tra định dạng email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location:register.php?error=Địa chỉ email không hợp lệ");
        exit();
    }

    // ✅ Kiểm tra mật khẩu trùng khớp
    if ($password !== $confirm_password) {
        header('location:register.php?error=Mật khẩu nhập lại không khớp');
        exit();
    }

    // ✅ Kiểm tra độ dài mật khẩu
    if (strlen($password) < 6) {
        header('location:register.php?error=Mật khẩu phải có ít nhất 6 ký tự');
        exit();
    }

    // ✅ Kiểm tra email đã tồn tại hay chưa
    $stmt1 = $conn->prepare('SELECT COUNT(*) FROM users WHERE user_email = ?');
    $stmt1->bind_param('s', $email);
    $stmt1->execute();
    $stmt1->bind_result($num_rows);
    $stmt1->fetch();
    $stmt1->close();

    if ($num_rows > 0) {
        header('location:register.php?error=Email đã được sử dụng');
        exit();
    }

    // ✅ Thêm tài khoản mới vào CSDL
    $stmt = $conn->prepare('INSERT INTO users(user_name, user_email, user_password) VALUES (?, ?, ?)');
    $hashed_password = md5($password);
    $stmt->bind_param('sss', $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $username;
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in'] = true;
        header('location:login.php?status=Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
        exit();
    } else {
        header('location:register.php?error=Đã xảy ra lỗi, vui lòng thử lại sau');
        exit();
    }
}
?>

<?php include('layouts/header.php') ?>

<!--Register-->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <p class="fw-bold p-0 m-0 fs-6">Chào mừng bạn đến với</p>
        <p class="login_name-shop fw-bold text-uppercase fs-2 ls-lg">Rakuten</p>
    </div>

    <div class="container">
        <form class="p-2 register-form-wrapper" id="register-form" action="register.php" method="POST">
            <div class="lgf-wrapper mx-auto container">
                <div class="container text-center text-uppercase mb-3">
                    <div class="lg-img d-flex text-center mx-auto" style="width: 100px; height: 100px;">
                        <img src="./assets/images/icon-114x114.png" alt="Logo đăng ký">
                    </div>
                    <h2 class="font-weight-bold">Đăng ký tài khoản</h2>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <p class="m-0"><?php echo $_GET['error']; ?></p>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <input type="text" placeholder="Nhập tên đăng nhập" id="user_name" name="user_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Nhập địa chỉ email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Nhập mật khẩu" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Nhập lại mật khẩu" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="submit" value="Đăng ký" name="register" class="btn bg-primary text-white" id="register"
                        onfocus="this.style.boxShadow='none'; this.style.outline='none'; this.style.borderColor='none';">
                </div>
                <div class="form-group">
                    <p class="register-txt">
                        Bạn đã có tài khoản?
                        <a id="register-url" class="btn-text d-inline-flex" href="login.php">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include('layouts/footer.php') ?>
