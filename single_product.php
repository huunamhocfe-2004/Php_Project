<?php

include("server/connection.php");

// if (isset($_GET['product_id'])) {
//     $product_id = $_GET['product_id'];

//     $stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ?');

//     $stmt->bind_param('i', $product_id);

//     $stmt->execute();

//     $sg_product = $stmt->get_result();



//     $stmt1 = $conn->prepare("SELECT * FROM products  LIMIT 4 OFFSET 8");
//     $stmt1->execute();
//     $related_products = $stmt1->get_result();
// } else {
//     header("location: index.php");
// }


include("server/connection.php");

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Lấy sản phẩm và danh mục
    $stmt = $conn->prepare('
        SELECT p.*, c.category_name 
        FROM products p 
        JOIN category c ON p.category_id = c.category_id 
        WHERE product_id = ?
    ');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $sg_product = $stmt->get_result();

    // Lấy 4 sản phẩm ngẫu nhiên, loại trừ sản phẩm hiện tại
    $stmt1 = $conn->prepare("
   SELECT * 
   FROM products 
   WHERE product_id != ? 
   ORDER BY RAND() 
   LIMIT 4
");
    $stmt1->bind_param('i', $product_id); // Đảm bảo sản phẩm hiện tại không được chọn
    $stmt1->execute();
    $related_products = $stmt1->get_result();
} else {
    header("location: index.php");
}

?>

<!-- css for size -->
<style>
    .size-options {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .size-label {
        padding: 10px 20px;
        border: 2px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, border 0.3s ease;
    }

    .size-label:hover {
        background-color: #f0f0f0;
    }

    input[type="radio"]:checked+.size-label {
        background-color: #000;
        color: #fff;
        border-color: #fff;
    }

    input[type="radio"] {
        display: none;
        /* Ẩn nút radio gốc */
    }

    .add-to-cart-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 24px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 15px;
        color: white;
        background: linear-gradient(135deg, #ff6b9d, #ff8fab);
        border: none;
        border-radius: 50px;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        color: #000;
    }

    .add-to-cart-btn:hover {
        background: linear-gradient(135deg, #ff8fab, #ff6b9d);
        box-shadow: 0 8px 20px rgba(255, 107, 157, 0.4);
    }

    .add-to-cart-btn:active {
        transform: translateY(-1px);
    }

    .add-to-cart-btn .icon {
        font-size: 18px;
    }

    .add-to-cart-btn .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .quantity-input {
        display: flex;
        align-items: center;
        gap: 0;
        width: fit-content;
        margin-top: 12px;
        font-family: 'Poppins', sans-serif;
    }

    /* Nút + / - */
    .quantity-btn {
        width: 38px;
        height: 38px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 50%;
        font-size: 18px;
        font-weight: 600;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
        /* box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); */
    }

    .quantity-btn:hover {
        background: #ff6b9d;
        color: white;
        border-color: #ff6b9d;
        box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
    }

    .quantity-btn:active {
        transform: scale(0.95);
    }

    /* Ô input số */
    .quantity-input input {
        width: 56px;
        height: 38px;
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        border: 2px solid #ddd;
        border-radius: 12px;
        outline: none;
        transition: all 0.3s ease;
        -moz-appearance: textfield;
        /* Ẩn mũi tên mặc định */
    }

    /* Ẩn mũi tên lên/xuống mặc định */
    .quantity-input input::-webkit-outer-spin-button,
    .quantity-input input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .quantity-input input:focus {
        border-color: #ff6b9d;
        box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.2);
    }
</style>


<?php include('layouts/header.php') ?>

<!--Single Product-->

<section class=" container single_product my-5 pt-5">
    <div class="row mt-5">

        <?php while ($row = $sg_product->fetch_assoc()) { ?>



            <div class="col-lg-5 col-md-6">
                <img src="./assets/images/<?php echo $row['product_image'] ?>" class="img-fluid w-100 pb-2 main-img"
                    id="mainImg">

                <div class="small-img-group">

                    <div class="small-img-col">
                        <img src="./assets/images/<?php echo $row['product_image2'] ?>" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="./assets/images/<?php echo $row['product_image3'] ?>" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="./assets/images/<?php echo $row['product_image4'] ?>" class="small-img">
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-6">

                <h2 class="py-4"><?php echo $row['product_name']; ?></h2>
                <h4><?php echo number_format($row['product_price'], 0, '.', '.') . ' VND'; ?></h4>

                <h3 class="p-price-discount">
                    <?php
                    if ($row['product_price_discount'] != 0) {
                        // Định dạng giá với dấu chấm cách 3 chữ số và thêm "VND"
                        echo number_format($row['product_price_discount'], 0, '.', '.') . ' VND';
                    } else {
                        echo ''; // Hiển thị khoảng trống nếu giá giảm bằng 0
                    }
                    ?>
                </h3>
                <!-- Hiển thị số lượng nếu quantity < 10 -->
                <?php if ($row['quantity'] < 10) { ?>
                    <p class="stock-status">Chỉ còn <?php echo $row['quantity']; ?> chiếc trong kho</p>
                <?php } ?>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo number_format($row['product_price'], 0, '.', '.') . ' VND'; ?>">

                    <label>Kích cỡ:</label>
                    <?php
                    $categories_with_presize = ['BAGS', 'BAGS/MINI BAGS', 'BAGS/BIG BAGS', 'ACCESSORIES'];
                    if (in_array($row['category_name'], $categories_with_presize)) {
                    ?>
                        <!-- Hiển thị pre-size -->
                        <div class="size-options">
                            <input type="radio" name="product_size" id="5" value="5">
                            <label for="5" class="size-label">Free Size</label>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="size-options">
                            <input type="radio" name="product_size" id="1" value="1" required>
                            <label for="1" class="size-label">S</label>
                            <input type="radio" name="product_size" id="2" value="2">
                            <label for="2" class="size-label">M</label>
                            <input type="radio" name="product_size" id="3" value="3">
                            <label for="3" class="size-label">L</label>
                            <input type="radio" name="product_size" id="4" value="4">
                            <label for="4" class="size-label">XL</label>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="quantity-input my-3">
                        <div class="quantity-btn mx-2" onclick="changeQuantity(-1)">-</div>
                        <input type="number" name="product_quantity" value="1" min="1" id="quantity" readonly>
                        <div class="quantity-btn mx-2" onclick="changeQuantity(1)">+</div>
                    </div>
                    <!-- <input type="number" name="product_quantity" value="1" min="1" class="mt-3"> -->
                    <button class="add-to-cart-btn" type="submit" name="add_to_cart"> <i class="fa-solid fa-cart-shopping"></i>Thêm vào giỏ hàng</button>
                </form>
                <h3 class="py-5 text-uppercase">Mô tả</h3>
                <p><?php echo $row['product_description']; ?></p>







            </div>
        <?php } ?>
</section>

<!-- <section id="featured" class="my-5 p-5">
    <div class="container text-center mt-5 py-5">
        <h3 class="text-uppercase fs-1">Sản phẩm liên quan</h3>
    </div>

    <div class="row mx-auto container-fluid gap-2"> -->
<div class="container p-5 list_product-container">
    <div class="list_product-txt">Sản phẩm liên quan</div>
    <div class="list_product-wrapper px-2 py-2">
        <div class="row gap-2 justify-content-between">
            <?php
            // Kết nối tới cơ sở dữ liệu
            include('server/connection.php');

            // Giả sử bạn đã có product_id từ trang single_product.php
            if (isset($_GET['product_id'])) {
                $product_id = $_GET['product_id'];

                // Truy vấn lấy thông tin sản phẩm hiện tại
                $stmt = $conn->prepare("
                SELECT p.*, sp.status_products_name
                FROM products p
                LEFT JOIN status_products sp ON p.status_products_id = sp.status_products_id
                WHERE p.product_id != ? 
                ORDER BY RAND() 
                LIMIT 4
            ");
                $stmt->bind_param('i', $product_id); // Loại trừ sản phẩm hiện tại
                $stmt->execute();
                $related_products = $stmt->get_result();
            } else {
                echo "No related products available.";
            }

            // Lặp qua các sản phẩm liên quan và hiển thị
            while ($related_product = $related_products->fetch_assoc()) {
                // Kiểm tra trạng thái sản phẩm
                if ($related_product['status_products_name'] == 'Sold Out') {
                    // Nếu sản phẩm đã "Sold Out", chuyển hướng đến trang sold_out.php
                    $link = "sold_out.php?product_id=" . $related_product['product_id'];
                } elseif ($related_product['status_products_name'] == 'Pre Order') {
                    // Nếu sản phẩm là "Pre Order", chuyển hướng đến trang pre_order.php
                    $link = "pre_order.php?product_id=" . $related_product['product_id'];
                } else {
                    // Nếu sản phẩm còn hàng, chuyển hướng đến trang single_product.php
                    $link = "single_product.php?product_id=" . $related_product['product_id'];
                }
            ?>

                <div class="product text-center col-lg-3 col-md-6 col-sm-12">
                    <a href="<?php echo $link; ?>" class="product-link">

                        <!-- Hiển thị trạng thái sản phẩm -->
                        <div class="product-status <?php echo strtolower(str_replace(' ', '-', $related_product['status_products_name'])); ?>">
                            <?php echo $related_product['status_products_name']; ?>
                        </div>

                        <div class="img-container">
                            <!-- Ảnh sản phẩm chính -->
                            <img class="img-fluid mb-3" src="./assets/images/<?php echo $related_product['product_image']; ?>">

                            <!-- Ảnh sản phẩm thứ hai sẽ xuất hiện khi hover -->
                            <img class="img-fluid img-second" src="./assets/images/<?php echo $related_product['product_image2']; ?>" alt="Second Image">
                        </div>

                        <div class="star">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>

                        <h3 class="p-product fs-2" style="font-size: 18px !important;"><?php echo $related_product['product_name']; ?></h3>
                        <p class="p-price"><?php echo number_format($related_product['product_price'], 0, '.', '.') . ' VND'; ?></p>

                        <!-- Hiển thị giá giảm nếu có -->
                        <p class="p-price-discount">
                            <?php
                            if ($related_product['product_price_discount'] != 0) {
                                echo number_format($related_product['product_price_discount'], 0, '.', '.') . ' VND';
                            } else {
                                echo ''; // Nếu không có giá giảm
                            }
                            ?>
                        </p>
                    </a>
                </div>

            <?php } // Kết thúc vòng lặp 
            ?>
        </div>
    </div>
</div>
</section>






<?php include('layouts/footer.php') ?>


<script>
    var mainImg = document.getElementById('mainImg');
    var small_Img = document.getElementsByClassName('small-img');

    for (let i = 0; i <= 4; i++) {
        small_Img[i].addEventListener('click', function() {
            mainImg.src = small_Img[i].src;
        });
    }
    function changeQuantity(change) {
            const input = document.getElementById('quantity');
            let value = parseInt(input.value);
            value = value + change;
            if (value < 1) value = 1;
            input.value = value;
        }
</script>