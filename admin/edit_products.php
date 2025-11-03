<?php
include('../server/connection.php');

// Xử lý khi có yêu cầu GET với product_id hoặc POST để cập nhật sản phẩm
if (isset($_GET['product_id']) || isset($_POST['update_product'])) {
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];

        // Lấy thông tin sản phẩm khi có product_id từ 2 bảng status_products và category
        $stmt = $conn->prepare('
            SELECT products.*, category.category_name, status_products.status_products_name
            FROM products
            INNER JOIN category ON products.category_id = category.category_id
            INNER JOIN status_products ON products.status_products_id = status_products.status_products_id
            WHERE product_id = ?'
        );
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        // Lấy danh sách các danh mục cho dropdown
        $stmt2 = $conn->prepare('SELECT * FROM category');
        $stmt2->execute();
        $categories = $stmt2->get_result();

        $stmt3 = $conn->prepare('SELECT * FROM status_products');
        $stmt3->execute();
        $status_product = $stmt3->get_result();
    }

    // Xử lý khi form được submit để cập nhật sản phẩm
    elseif (isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_price_discount = $_POST['product_price_discount'];
        $product_description = $_POST['product_description'];
        $product_color = $_POST['product_color'];
        $product_category = $_POST['product_category'];
        $product_status = $_POST['product_status'];
        $quantity = $_POST['quantity'];  // Thêm trường quantity

        // Kiểm tra nếu product_status không tồn tại hoặc không hợp lệ
        if (empty($product_status)) {
            echo "Vui lòng chọn trạng thái sản phẩm.";
            exit;
        }

        // Kiểm tra số lượng sản phẩm, nếu bằng 0 thì đặt status là "Hết hàng" (status_products_id = 6)
        if ($quantity == 0) {
            $product_status = 6; // Set status to Sold Out
        } 

        // Mảng chứa các trường hình ảnh
        $imageFields = ['product_image', 'product_image2', 'product_image3', 'product_image4'];
        $uploadedImages = [];

        // Duyệt từng trường ảnh để xử lý tải lên và cập nhật
        foreach ($imageFields as $imageField) {
            if (!empty($_FILES[$imageField]['name'])) {
                $imageName = $_FILES[$imageField]['name'];
                $imageTmpName = $_FILES[$imageField]['tmp_name'];
                $imagePath = '../assets/images/' . $imageName;

                // Kiểm tra xem tệp có phải là hình ảnh hợp lệ không
                $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

                // Kiểm tra định dạng ảnh hợp lệ
                if (in_array($imageFileType, $allowedTypes)) {
                    // Kiểm tra xem thư mục có quyền ghi không
                    if (is_writable('../assets/images/')) {
                        // Di chuyển tệp hình ảnh lên thư mục đích
                        if (move_uploaded_file($imageTmpName, $imagePath)) {
                            // Lưu tên hình ảnh vào mảng
                            $uploadedImages[$imageField] = $imageName;
                        } else {
                            echo "Lỗi khi tải lên tệp: " . $imageName . "<br>";
                        }
                    } else {
                        echo "Thư mục không có quyền ghi: ../assets/images/<br>";
                    }
                } else {
                    echo "Định dạng tệp không hợp lệ: " . $imageName . "<br>";
                }
            } else {
                // Giữ nguyên hình ảnh cũ nếu không có hình mới
                $uploadedImages[$imageField] = isset($_POST['existing_' . $imageField]) ? $_POST['existing_' . $imageField] : '';
            }
        }

        // Cập nhật sản phẩm với thông tin và hình ảnh
        $stmt1 = $conn->prepare('UPDATE products 
                                 SET product_name = ?, product_price = ?,product_price_discount = ?, product_description = ?, product_color = ?, category_id = ?, status_products_id = ?, product_image = ?, product_image2 = ?, product_image3 = ?, product_image4 = ?, quantity = ?
                                 WHERE product_id = ?');

        if ($stmt1) {
            $stmt1->bind_param(
                'sddssiissssii',
                $product_name,
                $product_price,
                $product_price_discount,
                $product_description,
                $product_color,
                $product_category,
                $product_status,
                $uploadedImages['product_image'],
                $uploadedImages['product_image2'],
                $uploadedImages['product_image3'],
                $uploadedImages['product_image4'],
                $quantity,
                $product_id
            );
        
            if ($stmt1->execute()) {
                header('Location: list_products.php?message=Sản phẩm đã được cập nhật thành công');
                exit();
            } else {
                echo "Lỗi khi thực thi câu lệnh: " . $stmt1->error;
            }
        } else {
            echo "Chuẩn bị câu lệnh thất bại.";
        }
    }
}
?>

<?php include('../admin/layouts/app.php'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cập nhật sản phẩm</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="list_products.php" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="edit_products.php" method="POST" enctype="multipart/form-data">
            <div class="container-fluid">
                <?php if (isset($product)): ?>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <!-- Tên sản phẩm -->
                                    <div class="mb-3">
                                        <label for="product_name">Tên sản phẩm</label>
                                        <input type="text" name="product_name" id="product_name" class="form-control"
                                               placeholder="Tên sản phẩm"
                                               value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                    </div>

                                    <!-- Danh mục sản phẩm -->
                                    <div class="mb-3">
                                        <label for="product_category">Danh mục sản phẩm</label>
                                        <select name="product_category" id="product_category" class="form-control" required>
                                            <?php while ($category = $categories->fetch_assoc()): ?>
                                                <option value="<?php echo htmlspecialchars($category['category_id']); ?>"
                                                    <?php echo ($category['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <!-- Trạng thái sản phẩm -->
                                    <div class="mb-3">
                                        <label for="product_status">Trạng thái sản phẩm</label>
                                        <select name="product_status" id="product_status" class="form-control" required>
                                            <?php if ($product['quantity'] == 0): ?>
                                                <option value="6" selected>Hết hàng</option>
                                            <?php else: ?>
                                                <?php while ($status_products = $status_product->fetch_assoc()): ?>
                                                    <option value="<?php echo htmlspecialchars($status_products['status_products_id']); ?>"
                                                        <?php echo ($status_products['status_products_id'] == $product['status_products_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($status_products['status_products_name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <!-- Mô tả sản phẩm -->
                                    <div class="mb-3">
                                        <label for="product_description">Mô tả sản phẩm</label>
                                        <textarea name="product_description" id="product_description" class="form-control" rows="10"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hình ảnh sản phẩm -->
                            <?php foreach (['product_image', 'product_image2', 'product_image3', 'product_image4'] as $imageField): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="mb-3">Tải lên <?php echo ucfirst(str_replace('_', ' ', $imageField)); ?></h5>
                                        <?php
                                        $imageSrc = !empty($product[$imageField]) ? '../assets/images/' . htmlspecialchars($product[$imageField]) : '../assets/images/logo.png';
                                        ?>
                                        <img class="img-fluid mb-3" src="<?php echo $imageSrc; ?>"
                                             alt="<?php echo ucfirst(str_replace('_', ' ', $imageField)); ?>" style="max-width: 80px; height: auto;">

                                        <input type="hidden" name="existing_<?php echo $imageField; ?>"
                                               value="<?php echo htmlspecialchars($product[$imageField]); ?>">
                                        <input type="file" name="<?php echo $imageField; ?>" class="form-control">
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Giá sản phẩm -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="product_price">Giá sản phẩm</label>
                                        <input type="number" name="product_price" id="product_price" class="form-control"
                                               placeholder="Giá sản phẩm"
                                               value="<?php echo htmlspecialchars($product['product_price']); ?>" step="0.01" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Giá khuyến mãi -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="product_price_discount">Giá khuyến mãi</label>
                                        <input type="number" name="product_price_discount" id="product_price_discount" class="form-control"
                                               placeholder="Giá khuyến mãi (tùy chọn)"
                                               value="<?php echo htmlspecialchars($product['product_price_discount']); ?>" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <!-- Màu sản phẩm -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="product_color">Màu sắc</label>
                                        <input type="text" name="product_color" id="product_color" class="form-control"
                                               placeholder="Màu sản phẩm"
                                               value="<?php echo htmlspecialchars($product['product_color']); ?>" >
                                    </div>
                                </div>
                            </div>

                            <!-- Số lượng -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="quantity">Số lượng</label>
                                        <input type="number" name="quantity" id="quantity" class="form-control"
                                               placeholder="Số lượng sản phẩm"
                                               value="<?php echo htmlspecialchars($product['quantity']); ?>" >
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

                <div class="pb-5 pt-3">
                    <button class="btn btn-primary" name="update_product">Cập nhật</button>
                    <a href="list_products.php" class="btn btn-danger">Hủy</a>
                </div>
            </div>
        </form>
    </section>
</div>

<?php include('../admin/layouts/sidebar.php'); ?>
