<?php
include('server/connection.php');
session_start();

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
        exit();
    }
}

// Xử lý cập nhật tài khoản
if (isset($_POST['update_account'])) {
    $user_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Kiểm tra định dạng email
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        header('location:account.php?error=Địa chỉ email không hợp lệ. Vui lòng nhập đúng định dạng.');
        exit();
    }

    // 🔍 Kiểm tra email trùng (trừ chính tài khoản hiện tại)
    $check_email = $conn->prepare('SELECT user_id FROM users WHERE user_email = ? AND user_id != ?');
    $check_email->bind_param('si', $user_email, $user_id);
    $check_email->execute();
    $check_email->store_result();
    if ($check_email->num_rows > 0) {
        header('location:account.php?error=Email này đã được sử dụng bởi tài khoản khác.');
        exit();
    }
    $check_email->close();

    // ✅ Cập nhật tên và email
    $stmt = $conn->prepare('UPDATE users SET user_name = ?, user_email = ? WHERE user_id = ?');
    $stmt->bind_param('ssi', $user_name, $user_email, $user_id);
    if (!$stmt->execute()) {
        header('location:account.php?error=Cập nhật thông tin tài khoản thất bại. Vui lòng thử lại.');
        exit();
    }

    // ✅ Nếu người dùng đổi mật khẩu
    if (!empty($old_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            header('location:account.php?error=Mật khẩu xác nhận không khớp.');
            exit();
        } elseif (strlen($new_password) < 6) {
            header('location:account.php?error=Mật khẩu mới phải có ít nhất 6 ký tự.');
            exit();
        } else {
            // Kiểm tra mật khẩu cũ
            $stmt = $conn->prepare('SELECT user_password FROM users WHERE user_id = ?');
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($hashed_old_password);
            $stmt->fetch();
            $stmt->close();

            if (md5($old_password) !== $hashed_old_password) {
                header('location:account.php?error=Mật khẩu cũ không chính xác.');
                exit();
            }

            // Cập nhật mật khẩu mới
            $hashed_new_password = md5($new_password);
            $stmt = $conn->prepare('UPDATE users SET user_password = ? WHERE user_id = ?');
            $stmt->bind_param('si', $hashed_new_password, $user_id);

            if ($stmt->execute()) {
                header('location:account.php?message=Cập nhật tài khoản thành công.');
            } else {
                header('location:account.php?error=Cập nhật mật khẩu thất bại. Vui lòng thử lại.');
            }
            $stmt->close();
        }
    } else {
        header('location:account.php?message=Cập nhật tài khoản thành công.');
    }
}
?>

<?php include('layouts/header.php') ?>

<!-- Trang tài khoản -->
<section class="my-5 py-5">
    <div class="container">
        <div class="row">
            <div class="account-update col-lg-8 col-md-10 col-sm-12 mx-auto">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">TRANG CHỦ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">TÀI KHOẢN CỦA TÔI</li>
                    </ol>
                </nav>

                <!-- Menu tài khoản -->
                <ul id="account-panel" class="nav nav-pills justify-content-center mb-4">
                    <li class="nav-item">
                        <a href="my_profile.php" class="nav-link font-weight-bold active" role="tab">
                            <i class="fa-solid fa-user"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="my_orders.php" class="nav-link font-weight-bold" role="tab">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="account.php?logout=1" class="nav-link font-weight-bold" role="tab">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </li>
                </ul>

                <h3 class="font-weight-bold text-center text-uppercase">Tài khoản của tôi</h3>

                <!-- Form cập nhật -->
                <div class="account-update-form mt-4">
                    <form id="account-update" action="account.php" method="POST">
                        <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <p><?php echo $_GET['message']; ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <p><?php echo $_GET['error']; ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="user_name">Tên tài khoản</label>
                            <input type="text" id="user_name" name="user_name" class="form-control"
                                value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Địa chỉ Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="old_password">Mật khẩu cũ</label>
                            <input type="password" id="old_password" name="old_password" class="form-control"
                                placeholder="Nhập mật khẩu hiện tại">
                        </div>

                        <div class="form-group">
                            <label for="new_password">Mật khẩu mới</label>
                            <input type="password" id="new_password" name="new_password" class="form-control"
                                placeholder="Ít nhất 6 ký tự">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <div class="form-group">
                            <button type="submit" name="update_account" class="btn btn-primary w-100">
                                Cập nhật tài khoản
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('layouts/footer.php') ?>
