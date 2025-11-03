<?php
include('../server/connection.php');

$message = '';
$error = '';

if (isset($_POST['create_category'])) {
    $category_name = trim($_POST['category_name']);

    if (empty($category_name)) {
        $error = "Vui lòng nhập tên danh mục!";
    } else {
        // Kiểm tra trùng tên danh mục
        $check_stmt = $conn->prepare('SELECT * FROM category WHERE category_name = ?');
        $check_stmt->bind_param('s', $category_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Tên danh mục đã tồn tại!";
        } else {
            // Thêm danh mục mới
            $stmt = $conn->prepare('INSERT INTO category(category_name) VALUES(?)');
            $stmt->bind_param('s', $category_name);

            if ($stmt->execute()) {
                $message = "Thêm danh mục thành công!";
            } else {
                $error = "Đã xảy ra lỗi khi thêm danh mục!";
            }
        }
    }
}
?>

<?php include('../admin/layouts/app.php') ?>
<div class="content-wrapper">
    <!-- Tiêu đề trang -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thêm Danh Mục</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_categorys.php" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Nội dung chính -->
    <section class="content">
        <form action="create_categorys.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">

                        <!-- Hiển thị thông báo -->
                        <?php if (!empty($message)) : ?>
                            <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php elseif (!empty($error)) : ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Tên danh mục</label>
                                    <input type="text" name="category_name" id="category_name" class="form-control"
                                        placeholder="Nhập tên danh mục" value="<?php echo isset($category_name) ? htmlspecialchars($category_name) : ''; ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button class="btn btn-success" name="create_category">Thêm mới</button>
                    <a href="list_categorys.php" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>
<?php include('../admin/layouts/sidebar.php') ?>
