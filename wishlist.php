<?php
session_start();
include "db.php";

/* -------------------------------------------------
   WISHLIST ACTIONS
------------------------------------------------- */

/* Add product to wishlist */
if (isset($_POST['add_to_wishlist'])) {

    $product_id = (int) $_POST['product_id'];

    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }

    if (!in_array($product_id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $product_id;
    }

    header("Location: wishlist.php");
    exit;
}


/* Remove product from wishlist */
if (isset($_GET['remove'])) {

    $product_id = (int) $_GET['remove'];

    if (isset($_SESSION['wishlist'])) {

        $_SESSION['wishlist'] = array_values(
            array_filter(
                $_SESSION['wishlist'],
                function ($id) use ($product_id) {
                    return (int)$id !== $product_id;
                }
            )
        );
    }

    header("Location: wishlist.php");
    exit;
}


/* -------------------------------------------------
   ADD WISHLIST PRODUCT TO CART
------------------------------------------------- */

if (isset($_POST['wishlist_add_cart'])) {

    $product_id = (int) $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    header("Location: wishlist.php");
    exit;
}


/* -------------------------------------------------
   LOGIN CHECK
------------------------------------------------- */

$is_logged_in = false;

/*
   Supports the common session formats used in your project.
*/

if (
    isset($_SESSION['user']) ||
    isset($_SESSION['user_id'])
) {
    $is_logged_in = true;
}


/* -------------------------------------------------
   HEADER
------------------------------------------------- */

include "includes/header.php";
?>

<style>

/* -------------------------------------------------
   WISHLIST PAGE
------------------------------------------------- */

.wishlist-page {
    min-height: calc(100vh - 150px);
    background: #ffffff;
    padding: 70px 5% 100px;
}


/* -------------------------------------------------
   LOGIN STATE
------------------------------------------------- */

.login-wishlist {
    min-height: 650px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.login-wishlist h2 {
    font-size: 24px;
    font-weight: 700;
    color: #17213c;
    margin-bottom: 18px;
    letter-spacing: .3px;
}

.login-wishlist p {
    color: #8a93a6;
    font-size: 18px;
    margin-bottom: 50px;
}

.wishlist-icon {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 35px;
}

.wishlist-icon svg {
    width: 120px;
    height: 120px;
}

.login-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 190px;
    height: 62px;
    border: 1px solid #3467f6;
    background: #fff;
    color: #3467f6;
    font-size: 20px;
    font-weight: 600;
    text-decoration: none;
    transition: .25s ease;
}

.login-btn:hover {
    background: #3467f6;
    color: #fff;
}


/* -------------------------------------------------
   WISHLIST PRODUCTS
------------------------------------------------- */

.wishlist-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
}

.wishlist-heading h2 {
    font-size: 28px;
    font-weight: 700;
    color: #17213c;
    margin: 0;
}

.wishlist-count {
    color: #777;
    font-size: 15px;
}


.wishlist-card {
    background: #fff;
    border: 1px solid #eeeeee;
    transition: .25s ease;
    height: 100%;
    position: relative;
}

.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
}


.wishlist-image {
    height: 280px;
    background: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.wishlist-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: .3s ease;
}

.wishlist-card:hover .wishlist-image img {
    transform: scale(1.04);
}


.wishlist-content {
    padding: 20px;
}

.wishlist-product-name {
    color: #17213c;
    font-size: 17px;
    font-weight: 600;
    margin-bottom: 8px;
}

.wishlist-price {
    font-size: 18px;
    font-weight: 700;
    color: #17213c;
    margin-bottom: 18px;
}


.wishlist-actions {
    display: flex;
    gap: 10px;
}

.add-cart-btn {
    flex: 1;
    border: none;
    background: #17213c;
    color: #fff;
    padding: 12px;
    font-weight: 600;
    transition: .2s ease;
}

.add-cart-btn:hover {
    background: #3467f6;
}


.remove-btn {
    width: 45px;
    border: 1px solid #ddd;
    background: #fff;
    color: #777;
    transition: .2s ease;
}

.remove-btn:hover {
    border-color: #ff3f6c;
    color: #ff3f6c;
}


/* -------------------------------------------------
   EMPTY WISHLIST
------------------------------------------------- */

.empty-wishlist {
    min-height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.empty-wishlist-icon {
    font-size: 70px;
    color: #d8dce5;
    margin-bottom: 25px;
}

.empty-wishlist h3 {
    color: #17213c;
    font-weight: 700;
}

.empty-wishlist p {
    color: #8a93a6;
    margin-bottom: 30px;
}

.shop-btn {
    background: #17213c;
    color: #fff;
    padding: 13px 30px;
    text-decoration: none;
    font-weight: 600;
}

.shop-btn:hover {
    background: #3467f6;
    color: #fff;
}


/* -------------------------------------------------
   RESPONSIVE
------------------------------------------------- */

@media (max-width: 768px) {

    .wishlist-page {
        padding: 40px 20px 80px;
    }

    .login-wishlist {
        min-height: 550px;
    }

    .login-wishlist h2 {
        font-size: 21px;
    }

    .login-wishlist p {
        font-size: 15px;
    }

    .wishlist-heading h2 {
        font-size: 22px;
    }

    .wishlist-image {
        height: 230px;
    }
}

</style>


<div class="wishlist-page">

<?php if (!$is_logged_in): ?>

    <!-- =========================================
         NOT LOGGED IN
    ========================================== -->

    <div class="login-wishlist">

        <h2>PLEASE LOG IN</h2>

        <p>
            Login to view items in your wishlist.
        </p>

        <div class="wishlist-icon">

            <svg viewBox="0 0 120 120"
                 fill="none"
                 xmlns="http://www.w3.org/2000/svg">

                <rect x="31"
                      y="15"
                      width="58"
                      height="82"
                      rx="7"
                      stroke="#5FE1CF"
                      stroke-width="3"/>

                <rect x="38"
                      y="10"
                      width="58"
                      height="82"
                      rx="7"
                      stroke="#5FE1CF"
                      stroke-width="3"/>

                <path d="M53 50H73"
                      stroke="#F7B955"
                      stroke-width="6"
                      stroke-linecap="round"/>

                <path d="M63 40V60"
                      stroke="#F7B955"
                      stroke-width="6"
                      stroke-linecap="round"/>

                <path d="M76 43H86"
                      stroke="#F7B955"
                      stroke-width="5"
                      stroke-linecap="round"/>

                <path d="M76 53H86"
                      stroke="#F7B955"
                      stroke-width="5"
                      stroke-linecap="round"/>

                <path d="M60 22L64 14L68 22"
                      stroke="#5FE1CF"
                      stroke-width="3"
                      stroke-linecap="round"/>

            </svg>

        </div>

        <a href="login.php" class="login-btn">
            LOGIN
        </a>

    </div>


<?php else: ?>

    <!-- =========================================
         LOGGED IN
    ========================================== -->

    <div class="wishlist-heading">

        <div>
            <h2>My Wishlist</h2>
            <span class="wishlist-count">
                <?php
                echo isset($_SESSION['wishlist'])
                    ? count($_SESSION['wishlist'])
                    : 0;
                ?>
                items
            </span>
        </div>

    </div>


    <?php

    $wishlist = $_SESSION['wishlist'] ?? [];

    if (!empty($wishlist)):

    ?>

        <div class="row g-4">

        <?php

        foreach ($wishlist as $product_id):

            $product_id = (int)$product_id;

            $stmt = $conn->prepare(
                "SELECT * FROM products WHERE id = ?"
            );

            $stmt->bind_param("i", $product_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if (!$product) {
                continue;
            }

        ?>

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">

                <div class="wishlist-card">

                    <!-- PRODUCT IMAGE -->

                    <div class="wishlist-image">

                        <?php if (!empty($product['image'])): ?>

                            <img
                                src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                            >

                        <?php else: ?>

                            <span class="text-muted">
                                No Image
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- PRODUCT INFORMATION -->

                    <div class="wishlist-content">

                        <div class="wishlist-product-name">

                            <?php
                            echo htmlspecialchars($product['name']);
                            ?>

                        </div>

                        <div class="wishlist-price">

                            ₹<?php
                            echo number_format(
                                (float)$product['price'],
                                2
                            );
                            ?>

                        </div>


                        <div class="wishlist-actions">

                            <!-- ADD TO CART -->

                            <form
                                method="POST"
                                style="flex:1;"
                            >

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?php echo $product['id']; ?>"
                                >

                                <button
                                    type="submit"
                                    name="wishlist_add_cart"
                                    class="add-cart-btn w-100"
                                >
                                    ADD TO BAG
                                </button>

                            </form>


                            <!-- REMOVE -->

                            <a
                                href="wishlist.php?remove=<?php echo $product['id']; ?>"
                                class="remove-btn d-flex align-items-center justify-content-center"
                                title="Remove from wishlist"
                            >
                                ×
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

        </div>


    <?php else: ?>

        <!-- EMPTY WISHLIST -->

        <div class="empty-wishlist">

            <div class="empty-wishlist-icon">
                ♡
            </div>

            <h3>Your wishlist is empty</h3>

            <p>
                Save your favourite products here and shop them later.
            </p>

            <a
                href="index.php"
                class="shop-btn"
            >
                CONTINUE SHOPPING
            </a>

        </div>

    <?php endif; ?>

<?php endif; ?>

</div>


<?php
include "includes/footer.php";
?>