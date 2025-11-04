<?php
include('../server/connection.php');

$message = '';
$error = '';

if (isset($_POST['create_product'])) {
    // Kiểm tra và xác thực dữ liệu đầu vào
    $product_name = trim($_POST['product_name']);
    $product_category = $_POST['product_category'];
    $product_status = $_POST['product_status'];
    $product_description = $_POST['product_description'];
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $product_price_discount = filter_var($_POST['product_price_discount'], FILTER_VALIDATE_FLOAT);
    $product_color = $_POST['product_color'];
    $quantity = $_POST['quantity'];

    // Kiểm tra giá trị nhập hợp lệ
    if (empty($product_name)) {
        $error = "Vui lòng nhập tên sản phẩm!";
    } elseif ($product_price === false || $product_price < 0) {
        $error = "Giá sản phẩm không hợp lệ!";
    } elseif ($product_price_discount !== null && $product_price_discount < 0) {
        $error = "Giá khuyến mãi không hợp lệ!";
    } else {
        // Kiểm tra trùng tên sản phẩm
        $check_stmt = $conn->prepare('SELECT * FROM products WHERE product_name = ?');
        $check_stmt->bind_param('s', $product_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Tên sản phẩm đã tồn tại!";
        } else {
            // Xử lý upload hình ảnh
            $image_files = ['image', 'image2', 'image3', 'image4'];
            $image_names = [];
            $max_size = 7 * 1024 * 1024; // 7MB

            foreach ($image_files as $key => $image) {
                if (isset($_FILES[$image]) && $_FILES[$image]['error'] === UPLOAD_ERR_OK) {
                    $file_tmp_name = $_FILES[$image]['tmp_name'];
                    $file_name = $_FILES[$image]['name'];
                    $file_size = $_FILES[$image]['size'];
                    $extension = pathinfo($file_name, PATHINFO_EXTENSION);

                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array(strtolower($extension), $allowed_extensions)) {
                        $error = "Chỉ cho phép tải lên các tệp hình ảnh (jpg, jpeg, png, gif).";
                        break;
                    }

                    if ($file_size > $max_size) {
                        $error = "Tệp quá lớn! Vui lòng chọn ảnh nhỏ hơn 7MB.";
                        break;
                    }

                    $image_name = $product_name . ($key + 1) . '.' . $extension;
                    $image_path = "../assets/images/" . $image_name;

                    if (move_uploaded_file($file_tmp_name, $image_path)) {
                        $image_names[] = $image_name;
                    } else {
                        $error = "Không thể tải ảnh lên: $file_name.";
                        break;
                    }
                } else {
                    $image_names[] = null;
                }
            }

            if (empty($error)) {
                // Chuẩn bị câu lệnh SQL
                $stmt = $conn->prepare('
                    INSERT INTO products 
                    (product_name, category_id, status_products_id, product_description, product_image, product_image2, product_image3, product_image4, product_price, product_price_discount, product_color, quantity)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ');

                if ($stmt) {
                    $stmt->bind_param(
                        "siisssssddsi",
                        $product_name,
                        $product_category,
                        $product_status,
                        $product_description,
                        $image_names[0],
                        $image_names[1],
                        $image_names[2],
                        $image_names[3],
                        $product_price,
                        $product_price_discount,
                        $product_color,
                        $quantity
                    );

                    if ($stmt->execute()) {
                        $message = "Thêm sản phẩm thành công!";
                    } else {
                        $error = "Lỗi khi thêm sản phẩm: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $error = "Lỗi chuẩn bị truy vấn: " . $conn->error;
                }
            }
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
                    <h1>Thêm Sản Phẩm</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_products.php" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="create_products.php" method="POST" enctype="multipart/form-data">
            <div class="container-fluid">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php elseif (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="product_name">Tên sản phẩm</label>
                                    <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Nhập tên sản phẩm" required>
                                </div>

                                <div class="mb-3">
                                    <label for="product_category">Danh mục sản phẩm</label>
                                    <select name="product_category" id="product_category" class="form-control" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php
                                        $result = $conn->query('SELECT category_id, category_name FROM category');
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="product_status">Trạng thái sản phẩm</label>
                                    <select name="product_status" id="product_status" class="form-control" required>
                                        <option value="">-- Chọn trạng thái --</option>
                                        <?php
                                        $result = $conn->query('SELECT status_products_id, status_products_name FROM status_products');
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($row['status_products_id']) . '">' . htmlspecialchars($row['status_products_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="product_description">Mô tả sản phẩm</label>
                                    <textarea name="product_description" id="product_description" cols="98" rows="10" class="summernote" placeholder="Nhập mô tả chi tiết"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Upload ảnh -->
                        <?php foreach (['image', 'image2', 'image3', 'image4'] as $imageField): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <label for="<?php echo $imageField; ?>">Tải ảnh <?php echo ucfirst($imageField); ?></label>
                                    <input type="file" name="<?php echo $imageField; ?>" id="<?php echo $imageField; ?>" class="form-control" required>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="card mb-3">
                            <div class="card-body">
                                <label for="product_price">Giá sản phẩm</label>
                                <input type="number" name="product_price" id="product_price" class="form-control" placeholder="Nhập giá" step="0.01" required>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <label for="product_price_discount">Giá khuyến mãi (nếu có)</label>
                                <input type="number" name="product_price_discount" id="product_price_discount" class="form-control" placeholder="Nhập giá khuyến mãi" step="0.01">
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <label for="product_color">Màu sắc sản phẩm</label>
                                <input type="text" name="product_color" id="product_color" class="form-control" placeholder="Nhập màu sắc">
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <label for="quantity">Số lượng</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Nhập số lượng">
                            </div>
                        </div>

                        <div class="pb-5 pt-3">
                            <button class="btn btn-success" name="create_product">Thêm sản phẩm</button>
                            <a href="list_products.php" class="btn btn-secondary">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php') ?>
