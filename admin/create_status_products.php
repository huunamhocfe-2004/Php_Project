<?php
// Kết nối cơ sở dữ liệu
include('../server/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_status_products'])) {
    // Lấy dữ liệu từ form
    $status_products_name = trim($_POST['status_products_name']);

    // Kiểm tra dữ liệu hợp lệ
    if (!empty($status_products_name)) {

        // Kiểm tra trùng tên trạng thái sản phẩm
        $check_stmt = $conn->prepare("SELECT * FROM status_products WHERE status_products_name = ?");
        $check_stmt->bind_param("s", $status_products_name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: create_status_products.php?error=Tên trạng thái sản phẩm đã tồn tại, vui lòng chọn tên khác");
            exit;
        }

        // Thêm dữ liệu vào bảng
        $stmt = $conn->prepare('INSERT INTO status_products (status_products_name) VALUES (?)');
        $stmt->bind_param('s', $status_products_name);

        if ($stmt->execute()) {
            // Thành công
            header('Location: list_status_products.php?message=Thêm trạng thái sản phẩm thành công');
        } else {
            // Lỗi SQL
            header('Location: create_status_products.php?error=Không thể thêm trạng thái sản phẩm, vui lòng thử lại');
        }

        $stmt->close();
        $check_stmt->close();
    } else {
        header('Location: create_status_products.php?error=Vui lòng nhập tên trạng thái sản phẩm hợp lệ');
    }
}
?>

<?php include('../admin/layouts/app.php'); ?>

<div class="content-wrapper">
    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thêm Trạng Thái Sản Phẩm</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_status_products.php" class="btn btn-primary">Trở lại</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Nội dung chính -->
    <section class="content">
        <form action="create_status_products.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <!-- Hiển thị thông báo -->
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
                        <?php elseif (isset($_GET['message'])): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($_GET['message']); ?></div>
                        <?php endif; ?>

                        <!-- Nhập tên trạng thái -->
                        <div class="mb-3">
                            <label for="status_products_name">Tên trạng thái sản phẩm</label>
                            <input type="text" 
                                   name="status_products_name" 
                                   id="status_products_name" 
                                   class="form-control" 
                                   placeholder="Nhập tên trạng thái sản phẩm" 
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="create_status_products">Thêm mới</button>
                    <a href="list_status_products.php" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>
