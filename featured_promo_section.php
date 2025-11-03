<?php
// featured_promo_section.php

include("server/connection.php");

// LẤY 1 SẢN PHẨM CÓ STATUS = 'Highlight' (ưu tiên giảm giá)
$stmt = $conn->prepare("
    SELECT products.* 
    FROM products
    INNER JOIN status_products 
    ON products.status_products_id = status_products.status_products_id
    WHERE status_products.status_products_name = 'Hightlight'
    LIMIT 1
");

$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Nếu không có → thông báo
if (!$product) {
    echo "<p style='text-align:center; color:#999; padding:50px; font-size:1.1rem;'>Chưa có sản phẩm nào được đánh dấu <strong>Highlight</strong>.</p>";
    return;
}
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', 'Segoe UI', sans-serif;
        background: #f8f9fa;
    }

    .featured-promo {
        max-width: 1200px;
        margin: 80px auto;
        padding: 0 20px;
    }

    .promo-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        padding: 20px;
        align-items: center;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        /* box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08); */
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px, rgb(51, 51, 51) 0px 0px 0px 3px;
    }

    .promo-image {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
    }

    .promo-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.6s ease;
    }

    .promo-image:hover img {
        transform: scale(1.08);
    }

    .badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #f39c12;
        color: white;
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        z-index: 2;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
    }

    .promo-content {
        padding: 40px;
    }

    .promo-content h2 {
        font-size: 2.6rem;
        color: #2c3e50;
        margin-bottom: 16px;
        line-height: 1.2;
    }

    .category {
        color: #e74c3c;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1.5px;
        display: block;
        margin-bottom: 8px;
    }

    .promo-content p {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 24px;
        line-height: 1.8;
    }

    .price-group {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .price del {
        color: #999;
        font-size: 1.2rem;
        text-decoration: line-through;
    }

    .price ins {
        color: #e74c3c;
        font-size: 2.2rem;
        font-weight: 700;
        text-decoration: none;
    }

    .stock-notice {
        background: #fff3cd;
        color: #856404;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.95rem;
        display: inline-block;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .btn-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 32px;
        background: linear-gradient(135deg, #ff6b9d, #ff8fab);
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(255, 107, 157, 0.3);
        transition: all 0.3s ease;
    }

    .btn-cta:hover {
        background: linear-gradient(135deg, #ff8fab, #ff6b9d);
        color: #000;
        box-shadow: 0 10px 25px rgba(255, 107, 157, 0.4);
    }

    @media (max-width: 992px) {
        .promo-grid {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .promo-content {
            padding: 30px 20px;
        }

        .promo-content h2 {
            font-size: 2.2rem;
        }
    }

    @media (max-width: 576px) {
        .promo-content h2 {
            font-size: 1.9rem;
        }

        .price ins {
            font-size: 1.8rem;
        }

        .btn-cta {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- SECTION QUẢNG CÁO -->
<section class="featured-promo">
    <div class="promo-grid">

        <!-- ẢNH -->
        <div class="promo-image">
            <img src="assets/images/<?php echo $product['product_image']; ?>"
                alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            <div class="badge">Hot sale</div>
        </div>

        <!-- NỘI DUNG -->
        <div class="promo-content">
            <h2><?php echo $product['product_name']; ?></h2>
            <p class="lh-1.25 m-0"><?php echo $product['product_description']; ?></p>
            <?php
            if ($product['product_price_discount'] > 0) {
                $percent = (($product['product_price'] - $product['product_price_discount']) / $product['product_price']) * 100;
                $percent = round($percent);
                echo "<p class='text-dark fw-bold'>Giảm <span class='fs-3' style='color: red'>{$percent}%</span> ngay hôm nay!</p>";
            }
            ?>

            <!-- GIÁ -->
            <div class="price-group d-flex align-items-center justify-content-start">
                <?php if ($product['product_price_discount'] > 0): ?>
                    <del class="text-muted me-2">
                        <?php echo number_format($product['product_price'], 0, '.', '.'); ?>đ
                    </del>
                    <ins class="fw-bold fs-2">
                        <?php echo number_format($product['product_price_discount'], 0, '.', '.'); ?>đ
                    </ins>
                <?php else: ?>
                    <ins>
                        <?php echo number_format($product['product_price'], 0, '.', '.'); ?>đ
                    </ins>
                <?php endif; ?>
            </div>

            <!-- TỒN KHO -->
            <?php if ($product['quantity'] < 10 && $product['quantity'] > 0): ?>
                <div class="stock-notice">Chỉ còn <?php echo $product['quantity']; ?> sản phẩm!</div>
            <?php endif; ?>

            <!-- NÚT CTA -->
            <a href="single_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn-cta">
                Mua ngay
            </a>
        </div>
    </div>
</section>