<?php
include('../server/connection.php');

// Kiểm tra xem có slider_id không
if (isset($_GET['slider_id'])) {
    $slider_id = $_GET['slider_id'];

    // Lấy thông tin slider từ cơ sở dữ liệu
    $sql = "SELECT slider_name, slider_image FROM slider WHERE slider_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $slider_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $slider_name = $row['slider_name'];
        $slider_image = $row['slider_image'];
    } else {
        header("Location: list_slider.php?error=Không tìm thấy slider");
        exit;
    }
}

// Xử lý POST khi cập nhật slider
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slider_name = $_POST['slider_name'];

    // Kiểm tra nếu có ảnh mới
    if (!empty($_FILES['slider_image']['name'])) {
        $slider_image = $_FILES['slider_image']['name'];
        $slider_image_tmp = $_FILES['slider_image']['tmp_name'];
        $target_dir = "../assets/images/";
        $target_file = $target_dir . basename($slider_image);

        // Kiểm tra định dạng ảnh
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg','jpeg','png','gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            header("Location: edit_slider.php?slider_id=$slider_id&error=Định dạng ảnh không hợp lệ");
            exit;
        }

        // Di chuyển ảnh tải lên
        if (move_uploaded_file($slider_image_tmp, $target_file)) {
            // Cập nhật thông tin slider vào cơ sở dữ liệu
            $sql = "UPDATE slider SET slider_name = ?, slider_image = ? WHERE slider_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $slider_name, $slider_image, $slider_id);
            if ($stmt->execute()) {
                header("Location: list_slider.php?message=Cập nhật slider thành công");
            } else {
                header("Location: edit_slider.php?slider_id=$slider_id&error=Cập nhật slider thất bại");
            }
        } else {
            header("Location: edit_slider.php?slider_id=$slider_id&error=Tải ảnh lên thất bại");
        }
    } else {
        // Nếu không thay đổi ảnh, chỉ cập nhật tên slider
        $sql = "UPDATE slider SET slider_name = ? WHERE slider_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $slider_name, $slider_id);
        if ($stmt->execute()) {
            header("Location: list_slider.php?message=Cập nhật slider thành công");
        } else {
            header("Location: edit_slider.php?slider_id=$slider_id&error=Cập nhật slider thất bại");
        }
    }
}
?>

<?php include('../admin/layouts/app.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Chỉnh sửa Slider</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_slider.php" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="edit_slider.php?slider_id=<?php echo $slider_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Tên Slider -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="slider_name">Tên Slider</label>
                                    <input type="text" name="slider_name" id="slider_name" class="form-control" value="<?php echo htmlspecialchars($slider_name); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Ảnh Slider (Hiển thị ảnh hiện tại) -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="mb-3">Tải lên ảnh mới</h5>
                                <img src="../assets/images/<?php echo htmlspecialchars($slider_image); ?>" class="img-fluid mb-3" alt="Ảnh Slider hiện tại" style="width: 100px;">
                                <input type="file" name="slider_image" id="slider_image" class="form-control">
                                <small>Nếu không muốn thay đổi ảnh, để trống.</small>
                            </div>
                        </div>

                        <!-- Nút Submit -->
                        <div class="pb-5 pt-3">
                            <button class="btn btn-primary" name="update_slider">Cập nhật</button>
                            <a href="list_slider.php" class="btn btn-danger">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>
