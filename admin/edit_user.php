<?php
include('../server/connection.php');

// Lấy thông tin user nếu có user_id trong URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Lấy 1 bản ghi duy nhất
}

// Xử lý khi form được submit
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];

    // Validate email
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ');window.history.back();</script>";
        exit();
    }

    $stmt1 = $conn->prepare('UPDATE users SET user_name = ?, user_email = ?, user_password = ? WHERE user_id = ?');
    $stmt1->bind_param('sssi', $user_name, $user_email, $user_password, $user_id);
    if ($stmt1->execute()) {
        header('location:list_users.php?message=Cập nhật người dùng thành công');
        exit();
    } else {
        header('location:list_users.php?error=Lỗi khi cập nhật người dùng');
        exit();
    }
}
?>

<?php include('../admin/layouts/app.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cập nhật người dùng</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_users.php" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="edit_user.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <?php if (isset($user)) { ?>
                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_name">Họ tên</label>
                                        <input type="text" name="user_name" id="user_name" class="form-control"
                                               placeholder="Nhập họ tên" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_email">Email</label>
                                        <input type="email" name="user_email" id="user_email" class="form-control"
                                               placeholder="Nhập email" value="<?php echo htmlspecialchars($user['user_email']); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user_password">Mật khẩu</label>
                                        <input type="password" name="user_password" id="user_password" class="form-control"
                                               placeholder="Nhập mật khẩu" value="<?php echo $user['user_password']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="card-body">
                            <p class="text-danger">Người dùng không tồn tại.</p>
                        </div>
                    <?php } ?>
                </div>

                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_user">Cập nhật</button>
                    <a href="list_users.php" class="btn btn-outline-dark ml-3">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>
