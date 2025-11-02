<?php
session_start();
include('server/connection.php');

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('location: checkout.php?message=Please login/register to place an order');
    exit();
} else {
    if (isset($_POST['place_order'])) {

        // 1. Lấy thông tin người dùng và lưu vào database
        $name = $_POST['customer_name'];  // Thay 'name' thành 'customer_name'
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        // Kiểm tra user_id trong session
        if (!isset($_SESSION['user_id'])) {
            die("Error: user_id is missing from the session.");
        }

        $user_id = $_SESSION['user_id'];
        $order_status = "Pending";
        $order_cost = $_SESSION['total'];
        $order_date = date('Y-m-d H:i:s');

        // Chuẩn bị câu truy vấn và kiểm tra xem prepare có thành công không
        $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Lỗi chuẩn bị truy vấn SQL: " . $conn->error);
        }

        // Thực hiện bind_param với đúng kiểu dữ liệu
        $stmt->bind_param('dsisss', $order_cost, $order_status, $user_id, $phone, $address, $order_date);

        // Thực thi câu lệnh
        if (!$stmt->execute()) {
            die("Lỗi khi thêm đơn hàng: " . $stmt->error);
        }

        // Lấy ID của đơn hàng vừa được thêm vào
        $order_id = $stmt->insert_id;

        // 3. Lặp qua giỏ hàng và lưu từng sản phẩm vào bảng `order_items`
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['product_id'];
            $product_name = $item['product_name'];
            $product_price = $item['product_price'];
            $product_quantity = $item['product_quantity'];
            $product_size = $item['product_size'];
            $product_image = $item['product_image'];

            // 3.1 Kiểm tra số lượng sản phẩm trong kho (bảng products)
            $stmt2 = $conn->prepare("SELECT quantity FROM products WHERE product_id = ?");
            if (!$stmt2) {
                die("Lỗi chuẩn bị truy vấn SQL kiểm tra sản phẩm: " . $conn->error);
            }

            $stmt2->bind_param('i', $product_id);
            $stmt2->execute();
            $stmt2->bind_result($quantity);
            $stmt2->fetch();
            $stmt2->close();

            // Nếu số lượng trong kho không đủ, hiển thị thông báo lỗi
            if ($quantity < $product_quantity) {
                die("Lỗi: Số lượng sản phẩm $product_name không đủ trong kho.");
            }

            // 3.2 Cập nhật số lượng sản phẩm trong kho (bảng products)
            $new_quantity = $quantity - $product_quantity;
            $stmt3 = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
            if (!$stmt3) {
                die("Lỗi chuẩn bị truy vấn SQL cập nhật số lượng sản phẩm trong kho: " . $conn->error);
            }

            $stmt3->bind_param('ii', $new_quantity, $product_id);
            if (!$stmt3->execute()) {
                die("Lỗi khi cập nhật số lượng sản phẩm trong kho: " . $stmt3->error);
            }

            // 3.3 Lưu thông tin sản phẩm vào bảng `order_items`
            $stmt1 = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_quantity, product_size, product_image, user_id, order_date, product_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt1) {
                die("Lỗi chuẩn bị truy vấn SQL cho order_items: " . $conn->error);
            }

            // Bind các biến vào câu truy vấn
            $stmt1->bind_param('iisiisisd', $order_id, $product_id, $product_name, $product_quantity, $product_size, $product_image, $user_id, $order_date, $product_price);

            // Thực thi câu lệnh
            if (!$stmt1->execute()) {
                die("Lỗi khi thêm sản phẩm vào order_items: " . $stmt1->error);
            }
        }

        // 4. Xóa giỏ hàng sau khi hoàn tất đặt hàng
        unset($_SESSION['cart']);

        // 5. Chuyển hướng tới trang thanh toán hoặc thông báo thành công
        header("location:payment.php?order_status=Order successfully");
        exit();
    }
}
?>
