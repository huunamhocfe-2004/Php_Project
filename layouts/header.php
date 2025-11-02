<?php
if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        session_destroy();
        header('location:login.php');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Website</title>
    <link href='./assets/images/favicon-16x16.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" type="text/css" href="./assets/css/header.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">

    <link rel="stylesheet" type="text/css" href="./assets/css/styles.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/index.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/note.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>


<style>
    /* General styles for the modal background */
    .cart-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        animation: fadeIn 0.5s ease-in-out;
    }

    /* Animations for fade-in effect */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Modal content styles */
    .cart-content {
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        padding: 30px;
        width: 90%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: zoomIn 0.4s ease-in-out;
    }

    /* Close button styles */
    .cart-content .close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        transition: transform 0.3s, color 0.3s;
        z-index: 999;
        /* Đảm bảo nút đóng luôn hiển thị trên các phần tử khác */
    }

    .cart-content .close:hover {
        transform: rotate(90deg) scale(1.2);
        color: #e74c3c;
    }

    /* Heading styles */
    .cart-content h2 {
        text-align: center;
        font-size: 2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: fadeIn 0.8s ease-in-out;
    }

    /* Product list styles */
    .cart-content .product-list {
        margin: 20px 0;
    }

    .cart-content .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 12px;
        background: linear-gradient(145deg, #ffffff, #eeeeee);
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1), -3px -3px 10px rgba(255, 255, 255, 0.6);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .cart-content .product-item:hover {
        transform: scale(1.02);
        box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.2), -5px -5px 20px rgba(255, 255, 255, 0.7);
    }

    /* Product details */
    .cart-content .product-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cart-content .product-info img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .cart-content .product-info p {
        font-size: 16px;
        font-weight: 500;
        color: #333;
        margin: 0;
    }

    /* Price and quantity */
    .cart-content .product-price,
    .cart-content .product-quantity {
        font-size: 15px;
        font-weight: bold;
        color: #555;
        text-align: center;
    }

    /* Total price styles */
    .cart-content .total-price {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: right;
        color: #333;
        margin-top: 20px;
        border-top: 2px solid #ddd;
        padding-top: 10px;
    }

    /* Buttons */
    .cart-content .btn {
        display: inline-block;
        padding: 12px 25px;
        font-size: 15px;
        font-weight: bold;
        text-transform: uppercase;
        color: #fff;
        /* Màu chữ trắng */
        background-color: #000;
        /* Nền nút màu đen */
        border-radius: 12px;
        margin-top: 20px;
        margin-right: 10px;
        text-align: center;
        text-decoration: none;
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s, background-color 0.3s, color 0.3s, box-shadow 0.3s;
    }

    .cart-content .btn:hover {
        transform: translateY(-3px);
        background-color: #fff;
        /* Nền nút chuyển sang trắng khi hover */
        color: #000;
        /* Màu chữ chuyển sang đen khi hover */
        box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.3);
    }


    /* Scrollbar styles */
    .cart-content::-webkit-scrollbar {
        width: 10px;
    }

    .cart-content::-webkit-scrollbar-thumb {
        background: linear-gradient(145deg, #000, #fff);
        border-radius: 10px;
    }

    .cart-content::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(145deg, #fff, #000);
    }
</style>

<body>
    <!-- Navbar -->
    <nav id="navbar" class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid nav-logo">
            <a class="logo_container" href="index.php">
                <!-- <img src="./assets/images/logo.png" width="200px" height="60px" alt="Logo"> -->

                <svg width="120" height="52" viewBox="0 0 256 77" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M206.08 63.85H51.2l12.34 12.33 142.54-12.33ZM65.55 13.95v1.82a15.69 15.69 0 0 0-9-2.9c-10.85 0-19.09 9.9-19.09 22.05s8.24 22.05 19.09 22.05a15.69 15.69 0 0 0 9-2.9v1.86h9.54v-42l-9.54.02Zm-9 32.93c-5.35 0-9.27-5.26-9.27-11.92s3.92-11.92 9.27-11.92 9.13 5.27 9.13 11.92-3.76 11.92-9.12 11.92h-.01ZM141.96 13.95v24.68c0 4.64-3.19 8.55-7.82 8.55-4.63 0-7.81-3.91-7.81-8.55V13.95h-9.55v24.68c0 10.13 6.93 18.38 17.06 18.38a14.641 14.641 0 0 0 8.11-2.6v1.56h9.53v-42l-9.52-.02ZM230.83 55.97V31.28c0-4.63 3.18-8.54 7.82-8.54 4.64 0 7.81 3.91 7.81 8.54v24.69H256V31.28c0-10.13-6.93-18.37-17.06-18.37a14.641 14.641 0 0 0-8.11 2.6v-1.56h-9.54v42l9.54.02Z" fill="#8529CD" />
                    <path d="M9.98 55.97V39.69h7l12.27 16.28h12.53L26.97 36.33A18.1 18.1 0 0 0 16.49 3.47H0v52.5h9.98Zm0-42.53h6.51a8.14 8.14 0 0 1 0 16.28H9.98V13.44ZM177.2 46.03a5.69 5.69 0 0 1-3.19 1c-1.56 0-4.49-1.19-4.49-5.15v-18h8.15v-10h-8.15V3.47h-9.54v10.48h-5v10h5v18.13c0 9.39 7.06 15 14.18 15a19.722 19.722 0 0 0 9.33-2.64l-6.29-8.41ZM98.8 33.64l16.36-19.7h-13.37L90.35 28.59V0h-9.83v55.97h9.83V38.71l14.06 17.26h13.37L98.8 33.64Z" fill="#8529CD" />
                    <path d="M199.23 12.88c-11 0-19 9.7-19 22.08 0 13 9.94 22.08 19.91 22.08 5 0 11.48-1.72 16.89-9.41l-8.38-4.86c-6.52 9.6-17.38 4.73-18.63-4.87h27.48c2.35-15.13-7.41-25.02-18.27-25.02Zm8.33 16.65h-17.18c2.01-9.84 15.32-10.4 17.18 0Z" fill="#8529CD" />
                </svg>

            </a>
            <!-- Menu chính -->
            <div class="navbar-nav ms-auto">
                <a class="nav-link menu-link nav-hover" href="index.php">Trang chủ</a>
                <div class="nav-item dropdown menu-list-item">
                    <a class="nav-link menu-link dropdown-toggle" href="all_product.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Sản phẩm<i class="fa-solid fa-caret-down"></i></a>
                    <div class="megamenu-sub">
                        <div class="megamenu-sub-wrap megamenu-sub-level1 row">
                            <!-- ÁO -->
                            <div class="item col-2">
                                <a href="TOPS.php">ÁO</a>
                                <div class="megamenu-sub-level2">
                                    <ul class="item">
                                        <li>
                                            <a href="T-SHIRTS.php">T-Shirts</a>
                                        </li>
                                        <li>
                                            <a href="SHIRTS.php">Shirts</a>
                                        </li>
                                        <li>
                                            <a href="OUTERWEARS.php">Outwears</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="item col-2">
                                <a href="TOPS.php">SWEETSHIRTS</a>
                                <div class="megamenu-sub-level2">
                                    <ul class="item">
                                        <li>
                                            <a href="SWEATSHIRTS & HOODIES.php">Hoddies</a>
                                        </li>
                                        <li>
                                            <a href="SWEATERS & CARDIGANS.php">Cardigans</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                            <!-- QUẦN -->
                            <div class="item col-2">
                                <a href="BOTTOMS.php">QUẦN</a>
                                <div class="megamenu-sub-level2">
                                    <ul class="item">
                                        <li>
                                            <a href="SHORTS.php">
                                                Shorts</a>
                                        </li>
                                        <li>
                                            <a href="PANTS.php">
                                                Pants</a>
                                        </li>
                                    </ul>
                                </div>

                            </div>

                            <!-- TÚI -->
                            <div class="item col-2">
                                <a href="BAGS.php">TÚI XÁCH</a>
                                <div class="megamenu-sub-level2">
                                    <ul class="item">
                                        <li>
                                            <a href="MINI BAGS.php">
                                                Mini Bags</a>
                                        </li>
                                        <li>
                                            <a href="BIG BAGS.php">
                                                Big Bags</a>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                            <!-- PHỤ KIỆN -->
                            <div class="item col-2">
                                <a href="ACCESSORIES.php">PHỤ KIỆN</a>
                            </div>
                        </div>
                    </div>
                </div>



                <a class="nav-link menu-link nav-hover" href="abouts.php">Về chúng tôi</a>

                <form class="form-search align-items-center" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="query" placeholder="Search..." aria-label="Search" required>
                    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                    <label class="lbl-search"></label>

                </form>

                <div class="nav-icons ms-3">
                    <a href="javascript:void(0);" onclick="toggleCartPopup()">
                        <i class="fas fa-shopping-cart"></i></a>
                </div>

                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                    $username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Người dùng';
                ?>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle nav-link menu-link" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class=""></i> <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li>
                                <a class="dropdown-item" href="account.php">
                                    <i class="fas fa-user-circle"></i> My Account
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="my_orders.php">
                                    <i class="fas fa-shopping-bag"></i> Your Orders
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="account.php?logout=1">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>


                                <!-- <a href="account.php?logout=1" class="nav-link font-weight-bold" role="tab">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a> -->
                            </li>
                        </ul>

                    </div>
                <?php } else { ?>
                    <div class="nav-icons">
                        <a href="login.php" class="nav-link menu-link"><i class="fas fa-user"></i> </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </nav>







</body>

</html>
<!-- Cart Pop-up Modal -->
<div id="cartModal" class="cart-modal">
    <div class="cart-content">
        <span class="close" onclick="toggleCartPopup()">&times;</span>
        <h2>Your Cart</h2>

        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>

                <?php foreach ($_SESSION['cart'] as $key => $value) { ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="./assets/images/<?php echo $value['product_image']; ?>"
                                    alt="<?php echo $value['product_name']; ?>">
                                <div>
                                    <p class="pt-4"><?php echo $value['product_name']; ?></p>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div>
                                <p class="pt-4">
                                    <?php
                                    if ($value['product_size'] == 1) {
                                        echo "S";
                                    } elseif ($value['product_size'] == 2) {
                                        echo "M";
                                    } elseif ($value['product_size'] == 3) {
                                        echo "L";
                                    } elseif ($value['product_size'] == 4) {
                                        echo "XL";
                                    } else {
                                        echo "Pre Size"; // Giá trị mặc định nếu không khớp
                                    }
                                    ?>
                                </p>
                            </div>
                        </td>
                        <td><?php echo $value['product_quantity']; ?></td>
                        <td><?php echo $value['product_price']; ?></td>
                        <td><?php echo number_format($_SESSION['total'], 3, '.', '.') . ' VND'; ?> </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td colspan="4">Total</td>
                    <td><?php echo number_format($_SESSION['total'], 3, '.', '.') . ' VND'; ?></td>
                </tr>
            </table>
            <a href="checkout.php" class="btn btn-dark">Proceed to Checkout</a>
            <a href="cart.php" class="btn btn-dark">Show Full</a>
        <?php } else { ?>
            <div class="empty-cart">
                <!-- Hình ảnh giỏ hàng trống -->
                <img src="./assets/images/empty-cart.png" alt="Giỏ hàng trống" style="max-width: 300px; display: block; margin: 0 auto;">
                <p>Your cart is empty.</p>
                <a href="index.php" class="btn btn-dark">Continue Shopping</a>
            </div>
        <?php } ?>
    </div>
</div>



<!-- cart -->
<script>
    function toggleCartPopup() {
        const cartModal = document.getElementById('cartModal');
        if (cartModal.style.display === 'flex') {
            cartModal.style.display = 'none';
        } else {
            cartModal.style.display = 'flex';
        }
    }
    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById("cartModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    window.addEventListener('scroll', function() {
        let scrollPosition = window.scrollY; // Vị trí cuộn của trang
        let banner = document.querySelector('.home-slider');
        const navbar = document.getElementById("navbar");

        // Nếu người dùng cuộn xuống, thu nhỏ banner
        if (scrollPosition > 100) { // Bạn có thể thay đổi giá trị 100 để tùy chỉnh
            banner.classList.add('banner-shrunk');
            banner.classList.remove('banner-expanded');
            navbar.style.background = '#ffffffa6'; // Thêm class khi cuộn xuống

        }
        // Nếu người dùng cuộn lên, phóng to banner
        else {
            banner.classList.add('banner-expanded');
            banner.classList.remove('banner-shrunk');
            navbar.style.background = '#fff'; // Gỡ class khi ở đầu trang

        }

    });
</script>