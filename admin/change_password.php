<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "php_project");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy thông tin admin từ session
$admin_email = $_SESSION['admin_email'];
$admin_name = $_SESSION['admin_name'];

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['admin_name']);
    $new_email = trim($_POST['admin_email']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ Kiểm tra định dạng email hợp lệ
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger text-center'>Địa chỉ email không hợp lệ!</div>";
    }
    // ✅ Kiểm tra mật khẩu mới trùng khớp
    else if ($new_password !== $confirm_password) {
        echo "<div class='alert alert-danger text-center'>Mật khẩu mới và xác nhận mật khẩu không khớp!</div>";
    } else {
        // Lấy mật khẩu cũ từ CSDL
        $sql = "SELECT admin_password FROM admins WHERE admin_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Xác minh mật khẩu cũ
            if (password_verify($old_password, $row['admin_password'])) {
                // Hash mật khẩu mới
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Cập nhật tên, email và mật khẩu mới
                $update_sql = "UPDATE admins SET admin_name = ?, admin_email = ?, admin_password = ? WHERE admin_email = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ssss", $new_name, $new_email, $hashed_new_password, $admin_email);

                if ($update_stmt->execute()) {
                    // Cập nhật session
                    $_SESSION['admin_name'] = $new_name;
                    $_SESSION['admin_email'] = $new_email;

                    echo "<div class='alert alert-success text-center'>Cập nhật thông tin thành công! Đang chuyển hướng...</div>";

                    // Chuyển hướng sau 2 giây
                    header("refresh:2;url=index.php");
                    exit;
                } else {
                    echo "<div class='alert alert-danger text-center'>Đã xảy ra lỗi khi cập nhật thông tin!</div>";
                }
            } else {
                echo "<div class='alert alert-danger text-center'>Mật khẩu cũ không đúng!</div>";
            }
        } else {
            echo "<div class='alert alert-danger text-center'>Không tìm thấy tài khoản!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title text-center">Đổi Mật Khẩu</h1>
        <form action="change_password.php" method="POST">
            <div class="input-group">
                <label for="admin_name">Tên Quản Trị Viên</label>
                <input type="text" id="admin_name" name="admin_name" 
                       value="<?php echo htmlspecialchars($admin_name); ?>" required>
            </div>

            <div class="input-group">
                <label for="admin_email">Email Quản Trị Viên</label>
                <input type="email" id="admin_email" name="admin_email" 
                       value="<?php echo htmlspecialchars($admin_email); ?>" required>
            </div>

            <div class="input-group">
                <label for="old_password">Mật Khẩu Hiện Tại</label>
                <input type="password" id="old_password" name="old_password" 
                       placeholder="Nhập mật khẩu hiện tại" required>
            </div>

            <div class="input-group">
                <label for="new_password">Mật Khẩu Mới</label>
                <input type="password" id="new_password" name="new_password" 
                       placeholder="Nhập mật khẩu mới" required>
            </div>

            <div class="input-group">
                <label for="confirm_password">Xác Nhận Mật Khẩu Mới</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Nhập lại mật khẩu mới" required>
            </div>

            <button type="submit" class="btn">Cập Nhật Mật Khẩu</button>
        </form>
    </div>
</body>
</html>
