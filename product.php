<?php
session_start();
include "db.php";

/* =========================
   CHECK PRODUCT ID
========================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    include "includes/header.php";
    ?>
    <div class="product-error">
        <div class="error-icon">!</div>
        <h2>Product Not Found</h2>
        <p>The product you are looking for does not exist.</p>
        <a href="index.php" class="primary-btn">Continue Shopping</a>
    </div>
    <?php
    include "includes/footer.php";
    exit;
}

$id = intval($_GET['id']);

/* =========================
   FETCH PRODUCT
========================= */
$product_result = $conn->query(
    "SELECT * FROM products WHERE id = $id LIMIT 1"
);

if (!$product_result || $product_result->num_rows == 0) {
    include "includes/header.php";
    ?>
    <div class="product-error">
        <div class="error-icon">!</div>
        <h2>Product Not Found</h2>
        <p>Sorry, this product is no longer available.</p>
        <a href="index.php" class="primary-btn">Continue Shopping</a>
    </div>
    <?php
    include "includes/footer.php";
    exit;
}

$product = $product_result->fetch_assoc();

/* =========================
   PRODUCT DATA
========================= */
$product_name = htmlspecialchars($product['name'] ?? 'Product');
$product_description = htmlspecialchars($product['description'] ?? '');
$product_price = floatval($product['price'] ?? 0);

$product_image = !empty($product['image'])
    ? "uploads/" . htmlspecialchars($product['image'])
    : "uploads/banner.jpg";

/* =========================
   FETCH GALLERY IMAGES
========================= */
$gallery_images = [];

$gallery_result = $conn->query(
    "SELECT * FROM product_images WHERE product_id = $id"
);

if ($gallery_result && $gallery_result->num_rows > 0) {
    while ($g = $gallery_result->fetch_assoc()) {
        $gallery_images[] = "uploads/" . htmlspecialchars($g['image']);
    }
}

/*
   Build the final gallery list:
   main image first, then any extra gallery images.
   If nothing else exists, just repeat the main image
   so the layout doesn't look broken.
*/
$all_images = array_merge([$product_image], $gallery_images);

if (count($all_images) < 2) {
    $all_images = array_fill(0, 6, $product_image);
}

/*
   Change this discount percentage if
   you have a discount field in your DB.
*/
$discount_percent = 40;

$original_price = $product_price > 0
    ? round($product_price / (1 - ($discount_percent / 100)))
    : 0;

$saved_amount = $original_price - $product_price;

/* =========================
   FETCH VARIATIONS
========================= */
$sizes = [];
$colors = [];

$variations_result = $conn->query(
    "SELECT * FROM product_variations WHERE product_id = $id"
);

if ($variations_result) {
    while ($variation = $variations_result->fetch_assoc()) {

        if (
            isset($variation['attribute']) &&
            strtolower($variation['attribute']) == 'size'
        ) {
            $sizes[] = $variation['value'];
        }

        if (
            isset($variation['attribute']) &&
            strtolower($variation['attribute']) == 'color'
        ) {
            $colors[] = $variation['value'];
        }
    }
}

/* =========================
   ADD REVIEW
========================= */
$review_message = "";
$review_type = "";

if (
    isset($_POST['submit_review']) &&
    isset($_SESSION['user'])
) {

    $user_name = $_SESSION['user'];

    $user_name_safe = $conn->real_escape_string($user_name);

    $user_result = $conn->query(
        "SELECT * FROM users WHERE name='$user_name_safe' LIMIT 1"
    );

    if ($user_result && $user_result->num_rows > 0) {

        $user = $user_result->fetch_assoc();

        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating >= 1 && $rating <= 5 && !empty($comment)) {

            $comment_safe = $conn->real_escape_string($comment);

            $insert_review = $conn->query(
                "INSERT INTO reviews
                (product_id, user_id, rating, comment)
                VALUES
                ($id, {$user['id']}, $rating, '$comment_safe')"
            );

            if ($insert_review) {
                $review_message = "Your review has been added successfully.";
                $review_type = "success";
            } else {
                $review_message = "Unable to add your review.";
                $review_type = "danger";
            }

        } else {
            $review_message = "Please provide a rating and review.";
            $review_type = "warning";
        }

    } else {
        $review_message = "User account could not be found.";
        $review_type = "danger";
    }
}

/* =========================
   FETCH REVIEWS
========================= */
$reviews = $conn->query(
    "SELECT r.*, u.name
     FROM reviews r
     JOIN users u ON r.user_id = u.id
     WHERE r.product_id = $id
     ORDER BY r.id DESC"
);

/* =========================
   CALCULATE RATING
========================= */
$average_rating = 4.1;
$total_reviews = 0;

if ($reviews && $reviews->num_rows > 0) {

    $rating_total = 0;
    $rating_count = 0;

    while ($review_rating = $reviews->fetch_assoc()) {
        $rating_total += intval($review_rating['rating']);
        $rating_count++;
    }

    if ($rating_count > 0) {
        $average_rating = round($rating_total / $rating_count, 1);
        $total_reviews = $rating_count;
    }

    /* Re-run query for displaying reviews */
    $reviews = $conn->query(
        "SELECT r.*, u.name
         FROM reviews r
         JOIN users u ON r.user_id = u.id
         WHERE r.product_id = $id
         ORDER BY r.id DESC"
    );
}

/* =========================
   FETCH SIMILAR PRODUCTS
========================= */
$similar_products = $conn->query(
    "SELECT *
     FROM products
     WHERE id != $id
     ORDER BY id DESC
     LIMIT 8"
);

include "includes/header.php";
?>

<style>

/* =========================================
   PRODUCT PAGE
========================================= */

.product-page {
    max-width: 1450px;
    margin: 0 auto;
    padding: 30px 35px 80px;
    background: #fff;
}

/* BREADCRUMB */

.breadcrumb-custom {
    font-size: 14px;
    color: #777;
    margin-bottom: 25px;
}

.breadcrumb-custom a {
    color: #222;
    text-decoration: none;
}

.breadcrumb-custom a:hover {
    color: #ff3f6c;
}

/* MAIN PRODUCT AREA */

.product-main {
    display: grid;
    grid-template-columns: minmax(0, 62%) minmax(350px, 38%);
    gap: 35px;
    align-items: start;
}

/* =========================================
   IMAGE GALLERY
========================================= */

.product-gallery {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5px;
}

.product-image-box {
    background: #f7f7f7;
    overflow: hidden;
    position: relative;
    min-height: 480px;
}

.product-image-box img {
    width: 100%;
    height: 100%;
    min-height: 480px;
    object-fit: contain;
    display: block;
    transition: transform .4s ease;
}

.product-image-box:hover img {
    transform: scale(1.03);
}

/* Image badge */

.image-badge {
    position: absolute;
    left: 12px;
    bottom: 12px;
    background: rgba(255,255,255,.95);
    padding: 5px 10px;
    font-size: 11px;
    border-radius: 3px;
    color: #333;
}

/* =========================================
   PRODUCT INFORMATION
========================================= */

.product-info {
    position: sticky;
    top: 20px;
}

.product-brand {
    font-size: 13px;
    color: #777;
    margin-bottom: 5px;
}

.product-title {
    font-size: 24px;
    font-weight: 600;
    color: #282c3f;
    margin-bottom: 8px;
}

.product-subtitle {
    color: #777;
    font-size: 15px;
    margin-bottom: 18px;
}

/* RATING */

.rating-box {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #ddd;
    padding: 7px 10px;
    font-size: 13px;
    margin-bottom: 15px;
}

.rating-number {
    font-weight: 600;
}

.stars {
    color: #149447;
}

.rating-divider {
    color: #ddd;
}

/* PRICE */

.price-section {
    border-top: 1px solid #eee;
    padding-top: 18px;
    margin-top: 5px;
}

.current-price {
    font-size: 25px;
    font-weight: 700;
    color: #282c3f;
}

.original-price {
    color: #999;
    text-decoration: line-through;
    margin-left: 8px;
}

.discount {
    color: #ff905a;
    margin-left: 8px;
    font-size: 14px;
    font-weight: 600;
}

.tax-note {
    font-size: 12px;
    color: #03a685;
    margin-top: 5px;
}

/* =========================================
   OPTIONS
========================================= */

.option-title {
    font-size: 14px;
    font-weight: 700;
    margin-top: 25px;
    margin-bottom: 12px;
    text-transform: uppercase;
}

.size-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.size-option {
    border: 1px solid #ddd;
    min-width: 55px;
    padding: 10px 15px;
    text-align: center;
    cursor: pointer;
    border-radius: 3px;
    font-size: 13px;
    background: #fff;
}

.size-option:hover {
    border-color: #ff3f6c;
    color: #ff3f6c;
}

.size-option input {
    display: none;
}

.size-option input:checked + span {
    color: #ff3f6c;
    font-weight: 700;
}

/* =========================================
   COLOR SWATCHES
========================================= */

.color-options {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.color-swatch {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}

.color-swatch input {
    display: none;
}

.color-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 2px solid #ddd;
    box-shadow: inset 0 0 0 2px #fff;
    transition: border-color .2s ease, transform .2s ease;
}

.color-swatch:hover .color-circle {
    transform: scale(1.08);
}

.color-swatch input:checked + .color-circle {
    border-color: #ff3f6c;
}

.color-label {
    font-size: 11px;
    color: #555;
    text-transform: capitalize;
}

.color-swatch input:checked ~ .color-label {
    color: #ff3f6c;
    font-weight: 700;
}

/* =========================================
   BUTTONS
========================================= */

.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.add-bag-btn {
    flex: 1;
    border: none;
    background: #ff3f6c;
    color: #fff;
    height: 50px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
}

.add-bag-btn:hover {
    background: #e93661;
}

.wishlist-btn {
    width: 150px;
    height: 50px;
    background: #fff;
    border: 1px solid #ddd;
    font-weight: 700;
    color: #282c3f;
    cursor: pointer;
}

.wishlist-btn:hover {
    border-color: #ff3f6c;
    color: #ff3f6c;
}

/* =========================================
   DELIVERY
========================================= */

.delivery-section {
    border-top: 1px solid #eee;
    margin-top: 25px;
    padding-top: 20px;
}

.delivery-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 12px;
}

.delivery-box {
    display: flex;
    border: 1px solid #ddd;
    height: 45px;
}

.delivery-box input {
    flex: 1;
    border: none;
    padding: 0 12px;
    outline: none;
}

.delivery-box button {
    border: none;
    background: transparent;
    color: #ff3f6c;
    font-weight: 700;
    padding: 0 15px;
}

/* =========================================
   INFO
========================================= */

.info-section {
    margin-top: 25px;
    border-top: 1px solid #eee;
}

.info-item {
    padding: 18px 0;
    border-bottom: 1px solid #eee;
}

.info-item h5 {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 8px;
}

.info-item p {
    color: #666;
    font-size: 13px;
    line-height: 1.7;
    margin: 0;
}

/* =========================================
   FULL WIDTH SECTIONS
========================================= */

.product-description-section {
    margin-top: 60px;
    border-top: 1px solid #eee;
    padding-top: 35px;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #282c3f;
    margin-bottom: 20px;
}

.description-text {
    color: #555;
    line-height: 1.8;
    font-size: 14px;
    max-width: 900px;
}

/* =========================================
   REVIEWS
========================================= */

.reviews-section {
    margin-top: 50px;
    border-top: 1px solid #eee;
    padding-top: 35px;
}

.review-layout {
    display: grid;
    grid-template-columns: 35% 65%;
    gap: 30px;
}

.review-form {
    border: 1px solid #eee;
    padding: 25px;
}

.review-form input,
.review-form textarea {
    width: 100%;
    border: 1px solid #ddd;
    padding: 12px;
    margin-bottom: 12px;
    outline: none;
}

.review-form input:focus,
.review-form textarea:focus {
    border-color: #ff3f6c;
}

.review-submit {
    width: 100%;
    height: 45px;
    background: #ff3f6c;
    border: none;
    color: white;
    font-weight: 700;
}

.review-card {
    border-bottom: 1px solid #eee;
    padding: 18px 0;
}

.review-user {
    font-weight: 700;
    font-size: 14px;
}

.review-stars {
    color: #149447;
    font-size: 13px;
    margin: 5px 0;
}

.review-comment {
    color: #555;
    font-size: 14px;
}

/* =========================================
   SIMILAR PRODUCTS
========================================= */

.similar-section {
    margin-top: 60px;
    border-top: 1px solid #eee;
    padding-top: 35px;
}

.similar-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 18px;
}

.similar-card {
    cursor: pointer;
    transition: .25s;
}

.similar-card:hover {
    transform: translateY(-4px);
}

.similar-image {
    height: 300px;
    background: #f7f7f7;
    overflow: hidden;
}

.similar-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.similar-info {
    padding: 10px 2px;
}

.similar-name {
    font-size: 13px;
    font-weight: 700;
    color: #282c3f;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.similar-desc {
    color: #777;
    font-size: 12px;
    margin: 3px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.similar-price {
    font-size: 13px;
    font-weight: 700;
}

.similar-old-price {
    color: #999;
    text-decoration: line-through;
    margin-left: 5px;
    font-size: 11px;
}

.similar-discount {
    color: #ff905a;
    font-size: 11px;
    margin-left: 5px;
}

/* =========================================
   ERROR
========================================= */

.product-error {
    max-width: 600px;
    margin: 100px auto;
    text-align: center;
    padding: 50px 25px;
}

.error-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #ff3f6c;
    color: white;
    font-size: 35px;
    line-height: 55px;
    margin: 0 auto 20px;
}

.primary-btn {
    display: inline-block;
    margin-top: 20px;
    background: #ff3f6c;
    color: white;
    text-decoration: none;
    padding: 13px 25px;
    font-weight: 600;
}

/* =========================================
   RESPONSIVE
========================================= */

@media(max-width: 1000px) {

    .product-main {
        grid-template-columns: 1fr;
    }

    .product-info {
        position: static;
    }

    .similar-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media(max-width: 700px) {

    .product-page {
        padding: 20px 15px 50px;
    }

    .product-gallery {
        grid-template-columns: 1fr 1fr;
    }

    .product-image-box,
    .product-image-box img {
        min-height: 250px;
    }

    .review-layout {
        grid-template-columns: 1fr;
    }

    .similar-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .similar-image {
        height: 230px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .wishlist-btn {
        width: 100%;
    }
}

</style>

<div class="product-page">

    <!-- =========================
         BREADCRUMB
    ========================== -->

    <div class="breadcrumb-custom">
        <a href="index.php">Home</a>
        &nbsp; / &nbsp;
        <a href="search.php">Products</a>
        &nbsp; / &nbsp;
        <?php echo $product_name; ?>
    </div>


    <!-- =========================
         MAIN PRODUCT
    ========================== -->

    <div class="product-main">

        <!-- IMAGE GALLERY -->

        <div class="product-gallery">

            <?php foreach ($all_images as $index => $img_src) { ?>

                <div class="product-image-box">

                    <img src="<?php echo htmlspecialchars($img_src); ?>"
                         alt="<?php echo $product_name; ?>">

                    <?php if ($index === 0) { ?>
                        <span class="image-badge">BESTSELLER</span>
                    <?php } ?>

                </div>

            <?php } ?>

        </div>


        <!-- =========================
             PRODUCT INFORMATION
        ========================== -->

        <div class="product-info">

            <div class="product-brand">
                MyStore
            </div>

            <h1 class="product-title">
                <?php echo $product_name; ?>
            </h1>

            <div class="product-subtitle">
                <?php echo $product_description; ?>
            </div>


            <!-- RATING -->

            <div class="rating-box">

                <span class="rating-number">
                    <?php echo $average_rating; ?>
                </span>

                <span class="stars">
                    ★
                </span>

                <span class="rating-divider">
                    |
                </span>

                <span>
                    <?php echo $total_reviews; ?> Reviews
                </span>

            </div>


            <!-- PRICE -->

            <div class="price-section">

                <span class="current-price">
                    ₹<?php echo number_format($product_price, 0); ?>
                </span>

                <?php if ($original_price > $product_price) { ?>

                    <span class="original-price">
                        ₹<?php echo number_format($original_price, 0); ?>
                    </span>

                    <span class="discount">
                        <?php echo $discount_percent; ?>% OFF
                    </span>

                <?php } ?>

                <div class="tax-note">
                    inclusive of all taxes
                </div>

            </div>


            <!-- ADD TO CART -->

            <form method="POST" action="cart.php">

                <input
                    type="hidden"
                    name="product_id"
                    value="<?php echo $id; ?>"
                >


                <!-- SIZE -->

                <?php if (!empty($sizes)) { ?>

                    <div class="option-title">
                        Select Size
                    </div>

                    <div class="size-options">

                        <?php foreach ($sizes as $index => $size) { ?>

                            <label class="size-option">

                                <input
                                    type="radio"
                                    name="size"
                                    value="<?php echo htmlspecialchars($size); ?>"
                                    <?php echo $index === 0 ? 'checked' : ''; ?>
                                >

                                <span>
                                    <?php echo htmlspecialchars($size); ?>
                                </span>

                            </label>

                        <?php } ?>

                    </div>

                <?php } ?>


                <!-- COLOR -->

                <?php if (!empty($colors)) { ?>

                    <div class="option-title">
                        Select Color
                    </div>

                    <div class="color-options">

                        <?php foreach ($colors as $index => $color) { ?>

                            <label class="color-swatch">

                                <input
                                    type="radio"
                                    name="color"
                                    value="<?php echo htmlspecialchars($color); ?>"
                                    <?php echo $index === 0 ? 'checked' : ''; ?>
                                >

                                <span class="color-circle"
                                      style="background-color: <?php echo htmlspecialchars(strtolower($color)); ?>;">
                                </span>

                                <span class="color-label">
                                    <?php echo htmlspecialchars($color); ?>
                                </span>

                            </label>

                        <?php } ?>

                    </div>

                <?php } ?>


                <!-- BUTTONS -->

                <div class="action-buttons">

                    <button
                        type="submit"
                        name="add_to_cart"
                        class="add-bag-btn"
                    >
                        ADD TO BAG
                    </button>

                    <button
                        type="button"
                        class="wishlist-btn"
                        onclick="addToWishlist(<?php echo $id; ?>)"
                    >
                        ♡ WISHLIST
                    </button>

                </div>

            </form>


            <!-- DELIVERY -->

            <div class="delivery-section">

                <div class="delivery-title">
                    DELIVERY OPTIONS
                </div>

                <div class="delivery-box">

                    <input
                        type="text"
                        placeholder="Enter pincode"
                        maxlength="6"
                    >

                    <button type="button">
                        CHECK
                    </button>

                </div>

                <p style="
                    font-size:12px;
                    color:#777;
                    margin-top:8px;
                ">
                    Enter your pincode to check delivery availability.
                </p>

            </div>


            <!-- PRODUCT INFORMATION -->

            <div class="info-section">

                <div class="info-item">

                    <h5>
                        100% ORIGINAL
                    </h5>

                    <p>
                        All products listed on MyStore are sourced and sold
                        through our trusted sellers.
                    </p>

                </div>

                <div class="info-item">

                    <h5>
                        EASY RETURNS
                    </h5>

                    <p>
                        Easy returns and exchanges are available according
                        to the product return policy.
                    </p>

                </div>

                <div class="info-item">

                    <h5>
                        BEST PRICE
                    </h5>

                    <p>
                        Enjoy competitive pricing and regular offers on
                        selected products.
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================
         DESCRIPTION
    ========================== -->

    <div class="product-description-section">

        <h2 class="section-title">
            Product Details
        </h2>

        <div class="description-text">

            <?php
            echo nl2br($product_description);
            ?>

        </div>

    </div>


    <!-- =========================
         REVIEWS
    ========================== -->

    <div class="reviews-section">

        <h2 class="section-title">
            Ratings & Reviews
        </h2>

        <?php if (!empty($review_message)) { ?>

            <div class="alert alert-<?php echo $review_type; ?>">
                <?php echo $review_message; ?>
            </div>

        <?php } ?>


        <div class="review-layout">

            <!-- REVIEW FORM -->

            <div>

                <?php if (isset($_SESSION['user'])) { ?>

                    <div class="review-form">

                        <h4 style="
                            font-size:17px;
                            margin-bottom:20px;
                        ">
                            Write a Review
                        </h4>

                        <form method="POST">

                            <input
                                type="number"
                                name="rating"
                                min="1"
                                max="5"
                                placeholder="Rating (1-5)"
                                required
                            >

                            <textarea
                                name="comment"
                                rows="5"
                                placeholder="Share your experience with this product..."
                                required
                            ></textarea>

                            <button
                                type="submit"
                                name="submit_review"
                                class="review-submit"
                            >
                                SUBMIT REVIEW
                            </button>

                        </form>

                    </div>

                <?php } else { ?>

                    <div class="review-form text-center">

                        <h4>
                            Want to review this product?
                        </h4>

                        <p style="
                            color:#777;
                            font-size:14px;
                            margin:15px 0;
                        ">
                            Please login to write a review.
                        </p>

                        
                            href="login.php"
                            class="primary-btn"
                        >
                            LOGIN
                        </a>

                    </div>

                <?php } ?>

            </div>


            <!-- REVIEWS -->

            <div>

                <?php

                if (
                    $reviews &&
                    $reviews->num_rows > 0
                ) {

                    while ($review = $reviews->fetch_assoc()) {

                ?>

                    <div class="review-card">

                        <div class="review-user">
                            <?php
                            echo htmlspecialchars(
                                $review['name']
                            );
                            ?>
                        </div>

                        <div class="review-stars">

                            <?php
                            echo str_repeat(
                                "★",
                                intval($review['rating'])
                            );

                            echo str_repeat(
                                "☆",
                                5 - intval($review['rating'])
                            );
                            ?>

                        </div>

                        <div class="review-comment">
                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $review['comment']
                                )
                            );
                            ?>
                        </div>

                    </div>

                <?php

                    }

                } else {

                ?>

                    <div style="
                        padding:30px;
                        text-align:center;
                        border:1px solid #eee;
                        color:#777;
                    ">
                        No reviews yet. Be the first to review this product.
                    </div>

                <?php } ?>

            </div>

        </div>

    </div>


    <!-- =========================
         SIMILAR PRODUCTS
    ========================== -->

    <div class="similar-section">

        <h2 class="section-title">
            You May Also Like
        </h2>

        <div class="similar-grid">

            <?php

            if (
                $similar_products &&
                $similar_products->num_rows > 0
            ) {

                while (
                    $similar = $similar_products->fetch_assoc()
                ) {

                    $similar_image =
                        !empty($similar['image'])
                        ? "uploads/" . htmlspecialchars($similar['image'])
                        : "uploads/banner.jpg";

            ?>

                <div
                    class="similar-card"
                    onclick="window.location='product.php?id=<?php echo $similar['id']; ?>'"
                >

                    <div class="similar-image">

                        <img
                            src="<?php echo $similar_image; ?>"
                            alt="<?php echo htmlspecialchars($similar['name']); ?>"
                        >

                    </div>

                    <div class="similar-info">

                        <div class="similar-name">

                            <?php
                            echo htmlspecialchars(
                                $similar['name']
                            );
                            ?>

                        </div>

                        <div class="similar-desc">

                            <?php
                            echo htmlspecialchars(
                                substr(
                                    $similar['description'] ?? '',
                                    0,
                                    45
                                )
                            );
                            ?>

                        </div>

                        <div class="similar-price">

                            ₹<?php
                            echo number_format(
                                $similar['price'],
                                0
                            );
                            ?>

                            <span class="similar-old-price">
                                ₹<?php
                                echo number_format(
                                    $similar['price'] * 1.4,
                                    0
                                );
                                ?>
                            </span>

                            <span class="similar-discount">
                                40% OFF
                            </span>

                        </div>

                    </div>

                </div>

            <?php

                }

            }

            ?>

        </div>

    </div>

</div>


<script>

/* =========================================
   WISHLIST
========================================= */

function addToWishlist(productId) {

    fetch("wishlist.php", {
        method: "POST",
        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded"
        },
        body:
            "product_id=" +
            encodeURIComponent(productId)
    })
    .then(response => response.text())
    .then(data => {

        alert("Product added to wishlist.");

    })
    .catch(error => {

        console.error(error);

        alert(
            "Unable to add product to wishlist."
        );

    });

}

</script>


<?php
include "includes/footer.php";
?>