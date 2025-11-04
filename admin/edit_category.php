<?php

include('../server/connection.php');

// Lấy thông tin danh mục nếu có category_id được truyền qua URL (GET)
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $stmt = $conn->prepare('SELECT * FROM category WHERE category_id = ?');
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $category = $stmt->get_result();
}

// Xử lý khi người dùng nhấn nút cập nhật danh mục
else if (isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];
    $category_id = $_POST['category_id'];  // Lấy category_id từ input ẩn

    // Cập nhật danh mục theo category_id
    $stmt1 = $conn->prepare('UPDATE category SET category_name = ? WHERE category_id = ?');
    $stmt1->bind_param('si', $category_name, $category_id);

    if ($stmt1->execute()) {
        header("location:list_categorys.php?message=Cập nhật danh mục thành công");
    } else {
        header("location:list_categorys.php?error=Lỗi khi cập nhật danh mục");
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
                    <h1>Cập nhật danh mục</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_categorys.php" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Nội dung chính -->
    <section class="content">
        <form action="edit_category.php" method="POST">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php if (isset($category)) {
                                foreach ($category as $categories) { ?>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="category_name">Tên danh mục</label>
                                    <input type="text" name="category_name" id="category_name" class="form-control"
                                        placeholder="Nhập tên danh mục"
                                        value="<?php echo $categories['category_name'] ?>">
                                    <!-- Truyền category_id ẩn -->
                                    <input type="hidden" name="category_id"
                                        value="<?php echo $categories['category_id']; ?>">
                                </div>
                            </div>
                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_category">Cập nhật</button>
                    <a href="list_categorys.php" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>
<?php include('../admin/layouts/sidebar.php') ?>
