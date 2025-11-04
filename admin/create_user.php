<?php
include('../server/connection.php');

if (isset($_POST['create_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ Kiểm tra dữ liệu đầu vào
    if (empty($name) || empty($email) || empty($password)) {
        header('Location: create_user.php?error=Vui lòng nhập đầy đủ thông tin');
        exit;
    }

    // ✅ Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: create_user.php?error=Email không hợp lệ');
        exit;
    }

    // ✅ Kiểm tra email đã tồn tại
    $check_stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        header('Location: create_user.php?error=Email này đã được sử dụng, vui lòng chọn email khác');
        exit;
    }

    // ✅ Mã hóa mật khẩu bằng MD5 (giống register.php)
    $hashed_password = md5(trim($password));

    // ✅ Thêm user mới vào DB
    $stmt = $conn->prepare('INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $hashed_password);

    if ($stmt->execute()) {
        header('Location: list_users.php?message=Thêm người dùng thành công');
        exit;
    } else {
        header('Location: create_user.php?error=Thêm người dùng thất bại, vui lòng thử lại');
        exit;
    }

    $stmt->close();
    $check_stmt->close();
}
?>

<?php include('../admin/layouts/app.php'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thêm người dùng mới</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_users.php" class="btn btn-primary">Trở lại</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="create_user.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
                        <?php elseif (isset($_GET['message'])): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($_GET['message']); ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Họ và tên</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           placeholder="Nhập họ và tên" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">Địa chỉ Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                           placeholder="Nhập địa chỉ email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password">Mật khẩu</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="Nhập mật khẩu" required>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="create_user">Tạo mới</button>
                    <a href="list_users.php" class="btn btn-secondary ml-3">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>
