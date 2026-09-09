<?php
include "db.php";
include "includes/header.php";
?>

<!-- =========================
     HERO BANNER
========================= -->

<?php
$banner = $conn->query("
    SELECT * FROM banners
    WHERE status = 1
    ORDER BY id DESC
    LIMIT 1
")->fetch_assoc();
?>

<section class="hero-wrapper">

    <div class="hero-banner"
         <?php if (!empty($banner['image'])) { ?>
             style="background-image:url('uploads/<?php echo htmlspecialchars($banner['image']); ?>');"
         <?php } ?>
         onclick="window.location='<?php echo htmlspecialchars($banner['link'] ?? '#'); ?>'">

    </div>

</section>


<!-- =========================
     MAIN CONTENT
========================= -->

<main class="home-content">


    <!-- =========================
         CATEGORY SECTION
    ========================= -->

<section class="category-section">
        <div class="section-header">
            <h2>SHOP BY CATEGORY</h2>
            <a href="search.php" class="view-all">
                View All
            </a>
        </div>
        <div class="horizontal-scroll category-scroll">
            <?php
            $cats = $conn->query("
                SELECT *
                FROM categories
                ORDER BY id ASC
            ");
            if ($cats && $cats->num_rows > 0) {
                while ($c = $cats->fetch_assoc()) {
                    /*
                     * If your categories table has an image column,
                     * it will automatically use it.
                     *
                     * Otherwise a default image will be used.
                     */
                    $categoryImage = (!empty($c['image']) && file_exists(__DIR__ . "/uploads/categories/" . $c['image']))
                        ? "uploads/categories/" . $c['image']
                        : "uploads/category-default.jpg";
            ?>
                    <div class="category-card"
                         onclick="window.location='search.php?category=<?php echo $c['id']; ?>'">
                        <div class="category-image">
                            <img src="<?php echo htmlspecialchars($categoryImage); ?>"
                                 alt="<?php echo htmlspecialchars($c['name']); ?>"
                                 onerror="this.src='uploads/category-default.jpg';">
                        </div>
                        <div class="category-name">
                            <?php echo htmlspecialchars($c['name']); ?>
                        </div>
                        <div class="category-link">
                            SHOP NOW
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "
                <div class='empty-message'>
                    No categories available.
                </div>
                ";
            }
            ?>
        </div>
    </section>


    <!-- =========================
         FEATURED PRODUCTS
    ========================= -->

    <section class="products-section">

        <div class="section-header">

            <h2>FEATURED PRODUCTS</h2>

            <a href="search.php" class="view-all">
                View All
            </a>

        </div>


        <div class="horizontal-scroll product-scroll">

            <?php

            $sql = "
                SELECT *
                FROM products
                WHERE is_featured = 1
                ORDER BY id DESC
            ";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

            ?>

                    <div class="product-card"
                         onclick="window.location='product.php?id=<?php echo $row['id']; ?>'">


                        <!-- PRODUCT IMAGE -->

                        <div class="product-image">

                            <?php if (!empty($row['image'])) { ?>

                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                                     onerror="this.style.display='none';">

                            <?php } else { ?>

                                <div class="no-image">
                                    No Image
                                </div>

                            <?php } ?>

                        </div>


                        <!-- PRODUCT DETAILS -->

                        <div class="product-details">

                            <h3>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </h3>


                            <p class="product-description">

                                <?php

                                $description = $row['description'] ?? '';

                                echo htmlspecialchars(
                                    strlen($description) > 70
                                        ? substr($description, 0, 70) . "..."
                                        : $description
                                );

                                ?>

                            </p>


                            <div class="product-bottom">

                                <span class="product-price">
                                    ₹<?php echo number_format($row['price'], 2); ?>
                                </span>


                                <form method="POST"
                                      action="cart.php"
                                      onclick="event.stopPropagation();">

                                    <input type="hidden"
                                           name="product_id"
                                           value="<?php echo $row['id']; ?>">

                                    <button type="submit"
                                            name="add_to_cart"
                                            class="add-cart-btn">

                                        Add to Cart

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

            <?php

                }

            } else {

                echo "
                <div class='empty-message'>
                    No featured products available.
                </div>
                ";

            }

            ?>

        </div>

    </section>



    <!-- =========================
         ALL PRODUCTS
    ========================= -->

    <section class="products-section">

        <div class="section-header">

            <h2>EXPLORE OUR COLLECTION</h2>

            <a href="search.php" class="view-all">
                View All
            </a>

        </div>


        <div class="horizontal-scroll product-scroll">

            <?php

            $allProducts = $conn->query("
                SELECT *
                FROM products
                ORDER BY id DESC
                LIMIT 12
            ");

            if ($allProducts && $allProducts->num_rows > 0) {

                while ($row = $allProducts->fetch_assoc()) {

            ?>

                    <div class="product-card"
                         onclick="window.location='product.php?id=<?php echo $row['id']; ?>'">


                        <div class="product-image">

                            <?php if (!empty($row['image'])) { ?>

                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                     alt="<?php echo htmlspecialchars($row['name']); ?>">

                            <?php } else { ?>

                                <div class="no-image">
                                    No Image
                                </div>

                            <?php } ?>

                        </div>


                        <div class="product-details">

                            <h3>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </h3>


                            <p class="product-description">

                                <?php

                                $description = $row['description'] ?? '';

                                echo htmlspecialchars(
                                    strlen($description) > 70
                                        ? substr($description, 0, 70) . "..."
                                        : $description
                                );

                                ?>

                            </p>


                            <div class="product-bottom">

                                <span class="product-price">
                                    ₹<?php echo number_format($row['price'], 2); ?>
                                </span>


                                <form method="POST"
                                      action="cart.php"
                                      onclick="event.stopPropagation();">

                                    <input type="hidden"
                                           name="product_id"
                                           value="<?php echo $row['id']; ?>">

                                    <button type="submit"
                                            name="add_to_cart"
                                            class="add-cart-btn">

                                        Add to Cart

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

            <?php

                }

            } else {

                echo "
                <div class='empty-message'>
                    No products available.
                </div>
                ";

            }

            ?>

        </div>

    </section>



    <!-- =========================
         WHY SHOP WITH US
    ========================= -->

    <section class="benefits-section">

        <div class="section-header">
            <h2>WHY SHOP WITH US</h2>
        </div>


        <div class="benefits-grid">

            <div class="benefit-card">

                <div class="benefit-icon">
                    ✓
                </div>

                <h3>Quality Products</h3>

                <p>
                    Carefully selected products from trusted sellers.
                </p>

            </div>


            <div class="benefit-card">

                <div class="benefit-icon">
                    ₹
                </div>

                <h3>Best Prices</h3>

                <p>
                    Great products at competitive prices.
                </p>

            </div>


            <div class="benefit-card">

                <div class="benefit-icon">
                    ↻
                </div>

                <h3>Easy Shopping</h3>

                <p>
                    Simple browsing, cart and checkout experience.
                </p>

            </div>


            <div class="benefit-card">

                <div class="benefit-icon">
                    ♡
                </div>

                <h3>Customer Support</h3>

                <p>
                    We're here to help whenever you need us.
                </p>

            </div>

        </div>

    </section>


</main>



<style>

/* =========================================
   GLOBAL
========================================= */

* {
    box-sizing: border-box;
}

body {
    background: #ffffff;
    margin: 0;
}


/* =========================================
   HERO BANNER
========================================= */

.hero-wrapper {
    width: 100%;
    padding: 0;
    margin: 0 0 35px 0;
}

.hero-banner {
    width: 100%;
    height: 350px;

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    cursor: pointer;

    transition: transform 0.3s ease;
}

.hero-banner:hover {
    transform: scale(1.005);
}


/* =========================================
   MAIN PAGE PADDING
========================================= */

.home-content {

    padding-left: 36px;
    padding-right: 36px;

    max-width: 1900px;
    margin: 0 auto;

}


/* =========================================
   SECTIONS
========================================= */

.category-section,
.products-section,
.benefits-section {

    margin-bottom: 55px;

}


.section-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 25px;

}


.section-header h2 {

    margin: 0;

    font-size: 28px;

    font-weight: 700;

    letter-spacing: 3px;

    color: #26354f;

}


.view-all {

    text-decoration: none;

    color: #26354f;

    font-size: 14px;

    font-weight: 600;

    border-bottom: 2px solid #26354f;

    padding-bottom: 3px;

}

.view-all:hover {

    color: #6366f1;

    border-color: #6366f1;

}


/* =========================================
   HORIZONTAL SCROLL
========================================= */

.horizontal-scroll {

    display: flex;

    gap: 24px;

    overflow-x: auto;

    overflow-y: hidden;

    padding: 5px 4px 20px 4px;

    scroll-behavior: smooth;

}


/* Hide scrollbar but keep scrolling */

.horizontal-scroll::-webkit-scrollbar {

    height: 6px;
    display: none;

}

.horizontal-scroll::-webkit-scrollbar-track {

    background: #f1f1f1;

    border-radius: 10px;

}

.horizontal-scroll::-webkit-scrollbar-thumb {

    background: #c4c4c4;

    border-radius: 10px;

}

.horizontal-scroll::-webkit-scrollbar-thumb:hover {

    background: #999;

}


/* =========================================
   CATEGORY CARDS
========================================= */

.category-card {

    min-width: 220px;

    max-width: 220px;

    background: #ffffff;

    border: 1px solid #eeeeee;

    cursor: pointer;

    transition: all 0.3s ease;

    flex-shrink: 0;

}


.category-card:hover {

    transform: translateY(-7px);

    box-shadow: 0 10px 25px rgba(0,0,0,0.12);

}


/* Category image */

.category-image {

    width: 100%;

    height: 230px;

    overflow: hidden;

    background: #f5f5f5;

}


.category-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    transition: transform 0.4s ease;

}


.category-card:hover .category-image img {

    transform: scale(1.05);

}


.category-name {

    text-align: center;

    font-size: 17px;

    font-weight: 600;

    padding: 15px 10px 5px;

    color: #222;

}


.category-link {

    text-align: center;

    font-size: 12px;

    font-weight: 700;

    letter-spacing: 1px;

    padding-bottom: 16px;

    color: #6366f1;

}


/* =========================================
   PRODUCT CARDS
========================================= */

.product-card {

    min-width: 260px;

    max-width: 260px;

    background: #ffffff;

    border: 1px solid #eeeeee;

    border-radius: 3px;

    overflow: hidden;

    cursor: pointer;

    flex-shrink: 0;

    transition: all 0.3s ease;

    display: flex;
    flex-direction: column;

}


.product-card:hover {

    transform: translateY(-7px);

    box-shadow: 0 12px 28px rgba(0,0,0,0.13);

}


/* Product image */

.product-image {

    width: 100%;

    height: 280px;

    background: #f7f7f7;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    flex-shrink: 0;

}


.product-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    transition: transform 0.4s ease;

}


.product-card:hover .product-image img {

    transform: scale(1.05);

}


/* No image */

.no-image {

    color: #999;

    font-size: 14px;

}


/* Product details */

.product-details {

    padding: 16px;

    display: flex;
    flex-direction: column;
    flex: 1;

}


.product-details h3 {

    font-size: 17px;

    font-weight: 600;

    margin: 0 0 8px;

    color: #222;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 44px;

}


.product-description {

    color: #777;

    font-size: 13px;

    margin-bottom: 15px;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 36px;

}


.product-bottom {

    display: flex;

    flex-direction: column;

    gap: 10px;

    margin-top: auto;

}


.product-price {

    font-size: 19px;

    font-weight: 700;

    color: #222;

}


/* Add cart */

.add-cart-btn {

    width: 100%;

    border: none;

    background: #26354f;

    color: white;

    padding: 10px;

    font-size: 13px;

    font-weight: 600;

    cursor: pointer;

    transition: 0.3s;

}


.add-cart-btn:hover {

    background: #6366f1;

}


/* =========================================
   BENEFITS
========================================= */

.benefits-section {

    padding-bottom: 40px;

}


.benefits-grid {

    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;

}


.benefit-card {

    text-align: center;

    padding: 30px 20px;

    background: #f8f8f8;

    border-radius: 5px;

    transition: 0.3s;

}


.benefit-card:hover {

    transform: translateY(-5px);

    box-shadow: 0 8px 20px rgba(0,0,0,0.08);

}


.benefit-icon {

    width: 50px;

    height: 50px;

    border-radius: 50%;

    background: #26354f;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    margin: 0 auto 15px;

    font-size: 22px;

}


.benefit-card h3 {

    font-size: 17px;

    margin-bottom: 8px;

}


.benefit-card p {

    color: #777;

    font-size: 13px;

    margin: 0;

}


/* =========================================
   EMPTY MESSAGE
========================================= */

.empty-message {

    padding: 30px;

    color: #777;

    background: #f8f8f8;

    width: 100%;

    text-align: center;

}


/* =========================================
   TABLET
========================================= */

@media (max-width: 992px) {

    .home-content {

        padding-left: 25px;

        padding-right: 25px;

    }


    .hero-banner {

        height: 280px;

    }


    .benefits-grid {

        grid-template-columns: repeat(2, 1fr);

    }

}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 576px) {

    .home-content {

        padding-left: 15px;

        padding-right: 15px;

    }


    .hero-wrapper {

        margin-bottom: 25px;

    }


    .hero-banner {

        height: 190px;

    }


    .section-header h2 {

        font-size: 20px;

        letter-spacing: 2px;

    }


    .category-card {

        min-width: 180px;

        max-width: 180px;

    }


    .category-image {

        height: 190px;

    }


    .product-card {

        min-width: 220px;

        max-width: 220px;

    }


    .product-image {

        height: 240px;

    }


    .benefits-grid {

        grid-template-columns: 1fr;

    }

}

</style>


<?php include "includes/footer.php"; ?>