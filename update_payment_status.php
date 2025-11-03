<?php
session_start();
include('server/connection.php'); // $conn

// Kiểm tra order_id
if (!isset($_SESSION['order_id'])) {
    echo 'ERROR: order_id missing';
    exit;
}
$order_id = intval($_SESSION['order_id']);

// Lấy method
$method = isset($_POST['method']) ? $_POST['method'] : '';

if ($method === 'online') {
    $payment_status = 'paid';
    $order_status = 'Paid';
    $payment_method = 'Online';
} elseif ($method === 'cod') {
    $payment_status = 'pending';
    $order_status = 'Pending';
    $payment_method = 'COD';
} else {
    echo 'ERROR: invalid method';
    exit;
}

// Hàm kiểm tra cột có tồn tại không
function column_exists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return ($res && $res->num_rows > 0);
}

// Xác định các cột có trong bảng `orders`
$columnsToUpdate = [];
$values = [];

// Nếu database có payment_status thì cập nhật
if (column_exists($conn, 'orders', 'payment_status')) {
    $columnsToUpdate[] = 'payment_status = ?';
    $values[] = $payment_status;
}

// Nếu database có payment_method thì cập nhật
if (column_exists($conn, 'orders', 'payment_method')) {
    $columnsToUpdate[] = 'payment_method = ?';
    $values[] = $payment_method;
}

// Nếu database có order_status thì cập nhật
if (column_exists($conn, 'orders', 'order_status')) {
    $columnsToUpdate[] = 'order_status = ?';
    $values[] = $order_status;
}

if (count($columnsToUpdate) === 0) {
    echo 'ERROR: no columns to update in orders table (payment_status/payment_method/order_status missing)';
    exit;
}

// Build SQL động
$sql = "UPDATE orders SET " . implode(', ', $columnsToUpdate) . " WHERE order_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo 'ERROR: prepare failed - ' . $conn->error;
    exit;
}

// Chuẩn bị bind_param
// types: tất cả là string 's' trừ order_id là integer 'i'
$types = str_repeat('s', count($values)) . 'i';

// bind params array (references required)
$params = $values;
$params[] = $order_id;

// tạo mảng tham chiếu cho call_user_func_array
$refs = [];
$refs[] = & $types;
for ($i = 0; $i < count($params); $i++) {
    $refs[] = & $params[$i];
}

call_user_func_array([$stmt, 'bind_param'], $refs);

if ($stmt->execute()) {
    echo 'OK';
    if ($method === 'online') {
        if (isset($_SESSION['cart'])) unset($_SESSION['cart']);
        unset($_SESSION['order_id']);
    }
    exit;
} else {
    echo 'ERROR: ' . $stmt->error;
    exit;
}
?>
