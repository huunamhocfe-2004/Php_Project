<?php
include('../server/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $slider_name = trim($_POST['slider_name']);
    $slider_image = $_FILES['slider_image']['name'];
    $slider_image_tmp = $_FILES['slider_image']['tmp_name'];
    $target_dir = "../assets/images/";
    $target_file = $target_dir . basename($slider_image);

    // Kiểm tra tên slider trống
    if (empty($slider_name)) {
        header("Location: create_slider.php?error=Vui lòng nhập tên slider");
        exit;
    }

    // Kiểm tra trùng tên slider
    $check_stmt = $conn->prepare("SELECT * FROM slider WHERE slider_name = ?");
    $check_stmt->bind_param("s", $slider_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: create_slider.php?error=Tên slider đã tồn tại, vui lòng chọn tên khác");
        exit;
    }

    // Kiểm tra file ảnh được tải lên
    if (!empty($slider_image)) {
        // Di chuyển file ảnh vào thư mục đích
        if (move_uploaded_file($slider_image_tmp, $target_file)) {
            // Thêm slider vào CSDL
            $sql = "INSERT INTO slider (slider_name, slider_image) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $slider_name, $slider_image);

            if ($stmt->execute()) {
                header("Location: list_slider.php?message=Thêm slider thành công");
            } else {
                header("Location: create_slider.php?error=Không thể thêm slider, vui lòng thử lại");
            }
        } else {
            header("Location: create_slider.php?error=Tải ảnh thất bại, vui lòng thử lại");
        }
    } else {
        header("Location: create_slider.php?error=Vui lòng chọn ảnh slider");
    }
}
?>

<?php include('../admin/layouts/app.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Thêm Slider Mới</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_slider.php" class="btn btn-primary">Trở lại</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="create_slider.php" method="POST" enctype="multipart/form-data">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger"><?php echo $_GET['error']; ?></div>
                        <?php } elseif (isset($_GET['message'])) { ?>
                            <div class="alert alert-success"><?php echo $_GET['message']; ?></div>
                        <?php } ?>

                        <!-- Tên Slider -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="slider_name">Tên Slider</label>
                                    <input type="text" name="slider_name" id="slider_name" class="form-control" placeholder="Nhập tên slider" required>
                                </div>
                            </div>
                        </div>

                        <!-- Ảnh Slider -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Tải ảnh Slider</h2>
                                <input type="file" name="slider_image" id="slider_image" class="form-control" required>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="pb-5 pt-3">
                            <button class="btn btn-primary" name="create_slider">Thêm mới</button>
                            <a href="list_slider.php" class="btn btn-secondary">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>
