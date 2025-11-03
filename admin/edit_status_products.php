<?php
include('../server/connection.php');

// Lấy thông tin trạng thái sản phẩm nếu có status_products_id được truyền qua URL (GET)
if (isset($_GET['status_products_id'])) {
    $status_products_id = $_GET['status_products_id'];
    $stmt = $conn->prepare('SELECT * FROM status_products WHERE status_products_id = ?');
    $stmt->bind_param('i', $status_products_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $status_products = $result->fetch_assoc(); // Lấy 1 bản ghi duy nhất
}

// Xử lý khi người dùng nhấn nút cập nhật trạng thái sản phẩm
if (isset($_POST['update_status_products'])) {
    $status_products_name = $_POST['status_products_name'];
    $status_products_id = $_POST['status_products_id'];  // Lấy status_products_id từ input ẩn

    // Cập nhật trạng thái sản phẩm theo ID
    $stmt1 = $conn->prepare('UPDATE status_products SET status_products_name = ? WHERE status_products_id = ?');
    $stmt1->bind_param('si', $status_products_name, $status_products_id);

    if ($stmt1->execute()) {
        header("location:list_status_products.php?message=Cập nhật trạng thái sản phẩm thành công");
        exit();
    } else {
        header("location:list_status_products.php?error=Lỗi khi cập nhật trạng thái sản phẩm");
        exit();
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
                    <h1>Cập nhật trạng thái sản phẩm</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_status_products.php" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Nội dung chính -->
    <section class="content">
        <form action="edit_status_products.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php if (isset($status_products)) { ?>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="status_products_name">Tên trạng thái</label>
                                        <input type="text" name="status_products_name" id="status_products_name" 
                                               class="form-control"
                                               placeholder="Nhập tên trạng thái"
                                               value="<?php echo htmlspecialchars($status_products['status_products_name']); ?>" required>
                                        <!-- Truyền status_products_id ẩn -->
                                        <input type="hidden" name="status_products_id"
                                               value="<?php echo $status_products['status_products_id']; ?>">
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="col-md-12">
                                    <p class="text-danger">Không tìm thấy trạng thái sản phẩm</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_status_products">Cập nhật</button>
                    <a href="list_status_products.php" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>
