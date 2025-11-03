<?php
session_start();
include('server/connection.php');

// 1️⃣ Kiểm tra đăng nhập
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('location: checkout.php?message=Please login/register to place an order');
    exit();
}

if (isset($_POST['place_order'])) {

    // 2️⃣ Lấy thông tin khách hàng
    $name = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (!isset($_SESSION['user_id'])) {
        die("Error: user_id is missing from the session.");
    }

    $user_id = $_SESSION['user_id'];
    $order_status = "Pending";
    $order_cost = $_SESSION['total'];
    $order_date = date('Y-m-d H:i:s');

    // 3️⃣ Thêm đơn hàng vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn SQL: " . $conn->error);
    }

    $stmt->bind_param('dsisss', $order_cost, $order_status, $user_id, $phone, $address, $order_date);

    if (!$stmt->execute()) {
        die("Lỗi khi thêm đơn hàng: " . $stmt->error);
    }

    $order_id = $stmt->insert_id;
    $_SESSION['order_id'] = $order_id; // thêm dòng này để payment.php và update có thể biết order


    // 4️⃣ Duyệt giỏ hàng và thêm từng sản phẩm vào order_items
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $product_name = $item['product_name'];
        $product_price = $item['product_price'];
        $product_quantity = $item['product_quantity'];
        $product_size = isset($item['product_size']) ? $item['product_size'] : 'No size';
        $product_image = $item['product_image'];

        // 4.1️⃣ Kiểm tra số lượng sản phẩm trong kho
        $stmt2 = $conn->prepare("SELECT quantity FROM products WHERE product_id = ?");
        if (!$stmt2) {
            die("Lỗi chuẩn bị truy vấn SQL kiểm tra sản phẩm: " . $conn->error);
        }

        $stmt2->bind_param('i', $product_id);
        $stmt2->execute();
        $stmt2->bind_result($quantity);
        $stmt2->fetch();
        $stmt2->close();

        if ($quantity < $product_quantity) {
            die("Lỗi: Số lượng sản phẩm $product_name không đủ trong kho.");
        }

        // 4.2️⃣ Cập nhật lại số lượng trong kho
        $new_quantity = $quantity - $product_quantity;
        $stmt3 = $conn->prepare("UPDATE products SET quantity = ? WHERE product_id = ?");
        if (!$stmt3) {
            die("Lỗi chuẩn bị truy vấn SQL cập nhật kho: " . $conn->error);
        }

        $stmt3->bind_param('ii', $new_quantity, $product_id);
        if (!$stmt3->execute()) {
            die("Lỗi khi cập nhật kho: " . $stmt3->error);
        }

        // 4.3️⃣ Thêm sản phẩm vào bảng order_items
        $stmt1 = $conn->prepare("INSERT INTO order_items 
            (order_id, product_id, product_name, product_quantity, product_size, product_image, user_id, order_date, product_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt1) {
            die("Lỗi chuẩn bị truy vấn SQL cho order_items: " . $conn->error);
        }

        if (empty($product_size)) {
            $product_size = 'No size';
        }

        $stmt1->bind_param('iisiisisd', $order_id, $product_id, $product_name, $product_quantity, $product_size, $product_image, $user_id, $order_date, $product_price);

        if (!$stmt1->execute()) {
            die("Lỗi khi thêm sản phẩm vào order_items: " . $stmt1->error);
        }
    }

    // 5️⃣ Xóa giỏ hàng sau khi đặt hàng
    unset($_SESSION['cart']);

    // 6️⃣ Chuyển hướng đến trang payment
    header("location:payment.php?order_status=Order successfully");
    exit();
}
?>
