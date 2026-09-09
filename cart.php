<?php
session_start();
include "db.php";

/* =========================================================
   ADD TO CART
========================================================= */

if (isset($_POST['add_to_cart'])) {

    $product_id = (int)$_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    header("Location: cart.php");
    exit;
}


/* =========================================================
   REMOVE ITEM
========================================================= */

if (isset($_GET['remove'])) {

    $remove_id = (int)$_GET['remove'];

    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }

    header("Location: cart.php");
    exit;
}


/* =========================================================
   CALCULATE CART
========================================================= */

$total = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $product_id => $quantity) {

        $product_id = (int)$product_id;
        $quantity = (int)$quantity;

        if ($quantity < 1) {
            continue;
        }

        $result = $conn->query(
            "SELECT * FROM products WHERE id=$product_id LIMIT 1"
        );

        if ($result && $result->num_rows > 0) {

            $product = $result->fetch_assoc();

            $subtotal = $product['price'] * $quantity;

            $total += $subtotal;

            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}

$item_count = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $qty) {
        $item_count += (int)$qty;
    }
}

$delivery_charge = ($total > 0 && $total < 999) ? 99 : 0;

$grand_total = $total + $delivery_charge;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Shopping Bag - MyStore</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            color: #282c3f;
        }

        /* =====================================================
           HEADER
        ===================================================== */

        .checkout-header {
            height: 78px;
            border-bottom: 1px solid #eeeeee;
            background: #ffffff;
            display: flex;
            align-items: center;
            padding: 0 5%;
        }

        .checkout-logo {
            width: 55px;
            height: 45px;
            object-fit: contain;
        }

        .checkout-steps {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
        }

        .checkout-step {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 3px;
            color: #696b78;
        }

        .checkout-step.active {
            color: #20b894;
            border-bottom: 2px solid #20b894;
            padding-bottom: 7px;
        }

        .step-line {
            width: 60px;
            border-top: 1px dashed #999999;
        }

        .secure-area {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555555;
            font-size: 13px;
            letter-spacing: 3px;
            font-weight: 500;
        }

        .secure-icon {
            width: 30px;
            height: 34px;
            background: #20c997;
            clip-path: polygon(
                50% 0%,
                92% 17%,
                92% 55%,
                75% 80%,
                50% 100%,
                25% 80%,
                8% 55%,
                8% 17%
            );
            position: relative;
        }

        .secure-icon::after {
            content: "✓";
            color: white;
            font-weight: bold;
            position: absolute;
            left: 8px;
            top: 6px;
        }


        /* =====================================================
           MAIN CONTENT
        ===================================================== */

        .cart-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 45px 30px 80px;
        }

        .cart-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 30px;
        }


        /* =====================================================
           CART ITEM
        ===================================================== */

        .cart-item {
            border: 1px solid #e5e5e5;
            padding: 18px;
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            background: #ffffff;
            position: relative;
        }

        .product-image-wrapper {
            width: 130px;
            height: 160px;
            background: #f7f7f7;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .product-info {
            flex: 1;
            padding-top: 3px;
        }

        .product-name {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .product-description {
            font-size: 13px;
            color: #7e818c;
            margin-bottom: 12px;
        }

        .product-price {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .quantity-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #d4d5d9;
            background: #ffffff;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .quantity-btn:hover {
            border-color: #ff3f6c;
            color: #ff3f6c;
        }

        .quantity-value {
            min-width: 25px;
            text-align: center;
            font-weight: 600;
        }

        .subtotal {
            margin-top: 14px;
            font-weight: 600;
            font-size: 15px;
        }

        .remove-btn {
            position: absolute;
            right: 15px;
            top: 12px;
            color: #696b78;
            font-size: 18px;
            text-decoration: none;
        }

        .remove-btn:hover {
            color: #ff3f6c;
        }


        /* =====================================================
           SUMMARY
        ===================================================== */

        .summary-card {
            border: 1px solid #e5e5e5;
            padding: 25px;
            height: fit-content;
        }

        .summary-title {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 17px;
            font-size: 14px;
            color: #535766;
        }

        .summary-row.total {
            border-top: 1px solid #eeeeee;
            padding-top: 18px;
            margin-top: 8px;
            font-size: 17px;
            font-weight: 600;
            color: #282c3f;
        }

        .free-delivery {
            color: #03a685;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 18px;
        }

        .checkout-btn {
            width: 100%;
            border: none;
            background: #ff3f6c;
            color: #ffffff;
            padding: 15px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .checkout-btn:hover {
            background: #e7335f;
            color: #ffffff;
        }


        /* =====================================================
           EMPTY CART
        ===================================================== */

        .empty-cart {
            min-height: 650px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .empty-bag {
            width: 110px;
            height: 125px;
            background: #ff3f6c;
            position: relative;
            margin-bottom: 40px;
            transform: rotate(-4deg);
            border-radius: 5px;
        }

        .empty-bag::before {
            content: "";
            position: absolute;
            width: 45px;
            height: 35px;
            border: 4px solid #555;
            border-bottom: none;
            border-radius: 30px 30px 0 0;
            left: 29px;
            top: -27px;
        }

        .empty-bag::after {
            content: "M";
            position: absolute;
            color: white;
            font-size: 45px;
            font-weight: bold;
            left: 34px;
            top: 35px;
        }

        .empty-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-text {
            color: #7e818c;
            font-size: 15px;
            margin-bottom: 28px;
        }

        .wishlist-btn {
            display: inline-block;
            padding: 14px 22px;
            border: 1px solid #ff3f6c;
            color: #ff3f6c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .wishlist-btn:hover {
            background: #ff3f6c;
            color: white;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .cart-footer {
            border-top: 1px solid #eeeeee;
            padding: 18px 5%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #282c3f;
            font-size: 14px;
        }

        .payment-icons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .payment-icon {
            border: 1px solid #eeeeee;
            padding: 8px 12px;
            font-size: 11px;
            color: #555;
            background: #fafafa;
        }

        .help-text {
            margin-left: auto;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 900px) {

            .checkout-header {
                padding: 0 20px;
            }

            .checkout-steps {
                position: static;
                transform: none;
                margin: auto;
            }

            .secure-area {
                display: none;
            }

            .cart-layout {
                grid-template-columns: 1fr;
            }

            .summary-card {
                margin-top: 10px;
            }
        }


        @media (max-width: 600px) {

            .checkout-header {
                height: 70px;
            }

            .checkout-logo {
                width: 45px;
            }

            .checkout-steps {
                gap: 6px;
            }

            .checkout-step {
                font-size: 9px;
                letter-spacing: 1.5px;
            }

            .step-line {
                width: 20px;
            }

            .cart-page {
                padding: 25px 15px 50px;
            }

            .cart-item {
                padding: 12px;
                gap: 12px;
            }

            .product-image-wrapper {
                width: 95px;
                height: 125px;
            }

            .product-name {
                font-size: 14px;
            }

            .product-description {
                font-size: 11px;
            }

            .product-price {
                font-size: 14px;
            }

            .empty-cart {
                min-height: 550px;
            }

            .empty-title {
                font-size: 21px;
            }

            .cart-footer {
                flex-direction: column;
                gap: 15px;
            }

            .help-text {
                margin-left: 0;
            }
        }

    </style>

</head>

<body>


<!-- =========================================================
     CHECKOUT HEADER
========================================================= -->

<header class="checkout-header">

    <a href="index.php">

        <img
            src="uploads/logo.png"
            alt="MyStore"
            class="checkout-logo"
            onerror="this.style.display='none';"
        >

    </a>


    <div class="checkout-steps">

        <span class="checkout-step active">
            BAG
        </span>

        <span class="step-line"></span>

        <span class="checkout-step">
            ADDRESS
        </span>

        <span class="step-line"></span>

        <span class="checkout-step">
            PAYMENT
        </span>

    </div>


    <div class="secure-area">

        <div class="secure-icon"></div>

        <span>100% SECURE</span>

    </div>

</header>


<!-- =========================================================
     MAIN CART
========================================================= -->

<main class="cart-page">


<?php if (!empty($cart_items)) { ?>


    <h1 class="cart-title">
        Shopping Bag
        <span style="font-size:14px;color:#7e818c;">
            (<?php echo $item_count; ?> items)
        </span>
    </h1>


    <div class="cart-layout">


        <!-- =================================================
             CART ITEMS
        ================================================== -->

        <section>

            <?php foreach ($cart_items as $item) {

                $product = $item['product'];
                $quantity = $item['quantity'];
                $subtotal = $item['subtotal'];

            ?>

                <div class="cart-item">


                    <!-- PRODUCT IMAGE -->

                    <div class="product-image-wrapper">

                        <?php if (!empty($product['image'])) { ?>

                            <img
                                src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="product-image"
                                onerror="this.src='uploads/default.png';"
                            >

                        <?php } else { ?>

                            <span style="color:#999;">
                                No Image
                            </span>

                        <?php } ?>

                    </div>


                    <!-- PRODUCT INFORMATION -->

                    <div class="product-info">

                        <div class="product-name">

                            <?php
                            echo htmlspecialchars($product['name']);
                            ?>

                        </div>


                        <div class="product-description">

                            <?php
                            echo htmlspecialchars(
                                substr($product['description'] ?? '', 0, 100)
                            );
                            ?>

                        </div>


                        <div class="product-price">

                            ₹<?php echo number_format($product['price'], 2); ?>

                        </div>


                        <!-- QUANTITY -->

                        <div class="quantity-wrapper">

                            <button
                                type="button"
                                class="quantity-btn"
                                onclick="updateCart(
                                    <?php echo $product['id']; ?>,
                                    'decrease'
                                )"
                            >
                                −
                            </button>


                            <span
                                class="quantity-value"
                                id="qty-<?php echo $product['id']; ?>"
                            >
                                <?php echo $quantity; ?>
                            </span>


                            <button
                                type="button"
                                class="quantity-btn"
                                onclick="updateCart(
                                    <?php echo $product['id']; ?>,
                                    'increase'
                                )"
                            >
                                +
                            </button>

                        </div>


                        <div class="subtotal">

                            Subtotal:
                            ₹<?php echo number_format($subtotal, 2); ?>

                        </div>

                    </div>


                    <!-- REMOVE -->

                    <a
                        href="cart.php?remove=<?php echo $product['id']; ?>"
                        class="remove-btn"
                        title="Remove item"
                        onclick="return confirm('Remove this item from your bag?');"
                    >

                        <i class="fa-solid fa-xmark"></i>

                    </a>


                </div>

            <?php } ?>

        </section>



        <!-- =================================================
             SUMMARY
        ================================================== -->

        <aside>

            <div class="summary-card">

                <div class="summary-title">
                    PRICE DETAILS
                </div>


                <div class="summary-row">

                    <span>
                        Total MRP
                    </span>

                    <span>
                        ₹<?php echo number_format($total, 2); ?>
                    </span>

                </div>


                <div class="summary-row">

                    <span>
                        Delivery Charges
                    </span>

                    <span>

                        <?php if ($delivery_charge == 0) { ?>

                            <span style="color:#03a685;">
                                FREE
                            </span>

                        <?php } else { ?>

                            ₹<?php echo number_format($delivery_charge, 2); ?>

                        <?php } ?>

                    </span>

                </div>


                <?php if ($delivery_charge == 0) { ?>

                    <div class="free-delivery">
                        Free delivery on orders above ₹999
                    </div>

                <?php } else { ?>

                    <div class="free-delivery">
                        Add ₹<?php echo number_format(999 - $total, 2); ?>
                        more for free delivery
                    </div>

                <?php } ?>


                <div class="summary-row total">

                    <span>
                        Total Amount
                    </span>

                    <span>
                        ₹<?php echo number_format($grand_total, 2); ?>
                    </span>

                </div>


                <a
                    href="checkout.php"
                    class="checkout-btn"
                >
                    PROCEED TO CHECKOUT
                </a>

            </div>

        </aside>


    </div>


<?php } else { ?>


    <!-- =====================================================
         EMPTY CART
    ====================================================== -->

    <div class="empty-cart">

        <div class="empty-bag"></div>


        <div class="empty-title">
            Hey, it feels so light!
        </div>


        <div class="empty-text">
            There is nothing in your bag.
            Let's add some items.
        </div>


        <a
            href="index.php"
            class="wishlist-btn"
        >
            ADD ITEMS FROM WISHLIST
        </a>

    </div>


<?php } ?>


</main>


<!-- =========================================================
     FOOTER
========================================================= -->

<footer class="cart-footer">

    <div class="payment-icons">

        <div class="payment-icon">
            256-bit SSL
        </div>

        <div class="payment-icon">
            VISA
        </div>

        <div class="payment-icon">
            Mastercard
        </div>

        <div class="payment-icon">
            UPI
        </div>

        <div class="payment-icon">
            COD
        </div>

    </div>


    <div class="help-text">

        Need Help?
        <strong>Contact Us</strong>

    </div>

</footer>


<!-- =========================================================
     CART JAVASCRIPT
========================================================= -->

<script>

function updateCart(productId, action) {

    fetch('update_cart.php', {

        method: 'POST',

        headers: {
            'Content-Type':
            'application/x-www-form-urlencoded'
        },

        body:
            'product_id=' +
            encodeURIComponent(productId) +
            '&action=' +
            encodeURIComponent(action)

    })

    .then(response => {

        if (!response.ok) {
            throw new Error('Network response failed');
        }

        return response.text();

    })

    .then(data => {

        data = data.trim();

        /*
         * update_cart.php should return
         * the new cart total.
         */

        const totalElement =
            document.getElementById('cart-total');

        if (totalElement) {
            totalElement.innerText = data;
        }


        const qtyElement =
            document.getElementById(
                'qty-' + productId
            );


        if (!qtyElement) {
            location.reload();
            return;
        }


        let currentQuantity =
            parseInt(qtyElement.innerText);


        if (action === 'increase') {

            currentQuantity++;

            qtyElement.innerText =
                currentQuantity;

        }


        else if (action === 'decrease') {

            if (currentQuantity > 1) {

                currentQuantity--;

                qtyElement.innerText =
                    currentQuantity;

            } else {

                location.reload();

            }

        }

    })

    .catch(error => {

        console.error(
            'Cart update error:',
            error
        );

        alert(
            'Unable to update the cart. Please try again.'
        );

    });

}

</script>


</body>
</html>