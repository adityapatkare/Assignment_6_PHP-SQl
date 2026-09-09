<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

include "db.php";
include "includes/header.php";

// USER CHECK
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ENSURE CART EXISTS
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$user_name = $_SESSION['user'];

// GET USER
$user_name_safe = $conn->real_escape_string($user_name);

$user_result = $conn->query(
    "SELECT * FROM users WHERE name='$user_name_safe' LIMIT 1"
);

if (!$user_result || $user_result->num_rows == 0) {
    die("User account not found.");
}

$user = $user_result->fetch_assoc();
$user_id = $user['id'];

// --------------------------------------------------
// CALCULATE CART TOTAL
// --------------------------------------------------

$total = 0;
$cart_items = [];

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $product_id => $quantity) {

        $product_id = intval($product_id);
        $quantity = intval($quantity);

        if ($quantity < 1) {
            $quantity = 1;
        }

        $result = $conn->query(
            "SELECT * FROM products WHERE id=$product_id LIMIT 1"
        );

        if ($result && $result->num_rows > 0) {

            $product = $result->fetch_assoc();

            $subtotal = $product['price'] * $quantity;

            $total += $subtotal;

            $cart_items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}

// --------------------------------------------------
// COUPON
// --------------------------------------------------

$discount = 0;
$final_total = $total;
$coupon_message = "";
$coupon_type = "";

if (isset($_POST['apply_coupon'])) {

    $coupon = trim($_POST['coupon'] ?? '');
    $coupon = $conn->real_escape_string($coupon);

    if (!empty($coupon)) {

        $coupon_result = $conn->query(
            "SELECT * FROM coupons 
             WHERE code='$coupon' 
             AND expiry > NOW()
             LIMIT 1"
        );

        if ($coupon_result && $coupon_result->num_rows > 0) {

            $coupon_data = $coupon_result->fetch_assoc();

            if ($coupon_data['discount_type'] == 'percent') {

                $discount = ($total * $coupon_data['value']) / 100;
                $coupon_type = $coupon_data['value'] . "% OFF";

            } else {

                $discount = $coupon_data['value'];
                $coupon_type = "₹" . $coupon_data['value'] . " OFF";
            }

            if ($discount > $total) {
                $discount = $total;
            }

            $_SESSION['discount'] = $discount;
            $_SESSION['coupon_code'] = $coupon;

            $final_total = $total - $discount;

            $coupon_message = "Coupon applied successfully.";

        } else {

            $coupon_message = "Invalid or expired coupon.";
        }

    } else {

        $coupon_message = "Please enter a coupon code.";
    }
}

// LOAD SAVED DISCOUNT
if (isset($_SESSION['discount']) && !isset($_POST['apply_coupon'])) {

    $discount = floatval($_SESSION['discount']);

    if ($discount > $total) {
        $discount = $total;
    }

    $final_total = $total - $discount;
}

// --------------------------------------------------
// PLACE ORDER
// --------------------------------------------------

$order_success = false;
$order_id = null;

if (isset($_POST['place_order'])) {

    if (!empty($_SESSION['cart'])) {

        $final_total = $total - $discount;

        if ($final_total < 0) {
            $final_total = 0;
        }

        // CREATE ORDER
        $insert_order = $conn->query(
            "INSERT INTO orders 
            (user_id, total_amount) 
            VALUES 
            ($user_id, $final_total)"
        );

        if ($insert_order) {

            $order_id = $conn->insert_id;

            // INSERT ORDER ITEMS
            foreach ($cart_items as $item) {

                $product_id = intval($item['id']);
                $quantity = intval($item['quantity']);
                $price = floatval($item['price']);

                $conn->query(
                    "INSERT INTO order_items 
                    (order_id, product_id, quantity, price)
                    VALUES
                    ($order_id, $product_id, $quantity, $price)"
                );
            }

            // CLEAR CART
            unset($_SESSION['cart']);
            unset($_SESSION['discount']);
            unset($_SESSION['coupon_code']);

            $order_success = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout | MyStore</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #fff;
            color: #282c3f;
            font-family: 'Poppins', sans-serif;
        }

        /* ========================================
           CHECKOUT HEADER
        ======================================== */

        .checkout-header {
            height: 78px;
            border-bottom: 1px solid #eeeeee;
            display: flex;
            align-items: center;
            background: #ffffff;
        }

        .checkout-header-inner {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 0 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .checkout-logo {
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .checkout-logo img {
            height: 42px;
            width: auto;
            object-fit: contain;
        }

        .checkout-steps {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 80px;
        }

        .checkout-step {
            font-size: 13px;
            letter-spacing: 3px;
            font-weight: 600;
            color: #777;
        }

        .checkout-step.active {
            color: #14b8a6;
            border-bottom: 2px solid #14b8a6;
            padding-bottom: 5px;
        }

        .step-line {
            width: 60px;
            border-top: 1px dashed #999;
        }

        .secure-checkout {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            letter-spacing: 2px;
            color: #555;
            font-weight: 500;
        }

        .secure-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #14c9a3;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }

        /* ========================================
           MAIN CHECKOUT
        ======================================== */

        .checkout-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 35px 25px 80px;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        /* ========================================
           LEFT SECTION
        ======================================== */

        .section-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 21px;
            font-weight: 700;
            margin: 0;
            color: #282c3f;
        }

        .address-heading {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 15px;
        }

        .address-card {
            border: 1px solid #eeeeee;
            border-radius: 5px;
            padding: 25px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            background: #fff;
        }

        .address-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .radio-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #ff3f6c;
            position: relative;
        }

        .radio-circle::after {
            content: "";
            position: absolute;
            width: 8px;
            height: 8px;
            background: #ff3f6c;
            border-radius: 50%;
            top: 4px;
            left: 4px;
        }

        .address-name {
            font-size: 16px;
            font-weight: 700;
        }

        .home-label {
            font-size: 11px;
            color: #00a884;
            border: 1px solid #00a884;
            padding: 4px 10px;
            border-radius: 15px;
            font-weight: 600;
        }

        .user-address {
            font-size: 14px;
            line-height: 1.7;
            color: #424553;
            margin-left: 32px;
            margin-bottom: 15px;
        }

        .user-email {
            margin-left: 32px;
            color: #666;
            font-size: 13px;
        }

        .address-actions {
            margin-left: 32px;
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }

        .outline-button {
            border: 1px solid #282c3f;
            background: #fff;
            padding: 9px 20px;
            font-size: 12px;
            font-weight: 600;
            color: #282c3f;
            border-radius: 4px;
        }

        .outline-button:hover {
            background: #282c3f;
            color: #fff;
        }

        .add-address {
            border: 1px dashed #d4d5d9;
            min-height: 85px;
            display: flex;
            align-items: center;
            padding: 25px;
            color: #ff3f6c;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .add-address:hover {
            background: #fff8fa;
        }

        /* ========================================
           CART ITEMS
        ======================================== */

        .items-section {
            margin-top: 35px;
        }

        .cart-item {
            border: 1px solid #eeeeee;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .cart-item-image {
            width: 90px;
            height: 110px;
            object-fit: contain;
            background: #f8f8f8;
            border-radius: 4px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .cart-item-price {
            font-size: 14px;
            font-weight: 700;
        }

        .cart-item-quantity {
            font-size: 13px;
            color: #777;
        }

        /* ========================================
           RIGHT SIDEBAR
        ======================================== */

        .checkout-sidebar {
            border-left: 1px solid #eeeeee;
            padding-left: 20px;
        }

        .sidebar-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .delivery-box {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .delivery-image {
            width: 55px;
            height: 65px;
            object-fit: contain;
            background: #f5f5f5;
        }

        .delivery-text {
            font-size: 14px;
            line-height: 1.6;
            padding-top: 10px;
        }

        .price-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
            color: #424553;
        }

        .discount-row {
            color: #03a685;
        }

        .price-divider {
            border-top: 1px dashed #ddd;
            margin: 20px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .continue-button {
            width: 100%;
            border: none;
            background: #ff3f6c;
            color: white;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .5px;
            border-radius: 2px;
            transition: .2s;
        }

        .continue-button:hover {
            background: #e7335e;
        }

        .continue-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* ========================================
           COUPON
        ======================================== */

        .coupon-box {
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .coupon-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .coupon-form {
            display: flex;
            gap: 8px;
        }

        .coupon-input {
            flex: 1;
            border: 1px solid #d4d5d9;
            padding: 10px 12px;
            font-size: 13px;
            outline: none;
        }

        .coupon-button {
            border: 1px solid #ff3f6c;
            background: white;
            color: #ff3f6c;
            font-weight: 700;
            padding: 10px 15px;
            font-size: 12px;
        }

        .coupon-success {
            color: #03a685;
            font-size: 12px;
            margin-top: 10px;
        }

        .coupon-error {
            color: #ff3f6c;
            font-size: 12px;
            margin-top: 10px;
        }

        /* ========================================
           SUCCESS
        ======================================== */

        .success-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            padding: 50px 30px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            background: #03a685;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 25px;
            font-size: 32px;
        }

        .success-container h2 {
            font-weight: 700;
            margin-bottom: 12px;
        }

        .success-container p {
            color: #777;
            margin-bottom: 25px;
        }

        .success-button {
            display: inline-block;
            background: #ff3f6c;
            color: white;
            text-decoration: none;
            padding: 13px 30px;
            font-weight: 600;
            border-radius: 3px;
        }

        /* ========================================
           FOOTER
        ======================================== */

        .checkout-footer {
            border-top: 1px solid #eee;
            padding: 25px;
            background: #fff;
        }

        .footer-inner {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payment-methods {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .payment-box {
            border: 1px solid #eee;
            padding: 8px 15px;
            font-size: 11px;
            color: #555;
            background: #fafafa;
        }

        .help-text {
            font-size: 14px;
            font-weight: 600;
        }

        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 900px) {

            .checkout-layout {
                grid-template-columns: 1fr;
            }

            .checkout-sidebar {
                border-left: none;
                border-top: 1px solid #eee;
                padding-left: 0;
                padding-top: 30px;
            }

            .checkout-steps {
                margin-left: 0;
            }

            .secure-checkout {
                display: none;
            }

        }

        @media (max-width: 600px) {

            .checkout-header {
                height: 65px;
            }

            .checkout-header-inner {
                padding: 0 15px;
            }

            .checkout-logo img {
                height: 35px;
            }

            .checkout-steps {
                gap: 7px;
            }

            .checkout-step {
                font-size: 9px;
                letter-spacing: 1px;
            }

            .step-line {
                width: 20px;
            }

            .checkout-wrapper {
                padding: 25px 15px 50px;
            }

            .section-title-row {
                display: block;
            }

            .address-card {
                padding: 18px;
            }

            .address-actions {
                margin-left: 0;
            }

            .user-address,
            .user-email {
                margin-left: 0;
            }

            .cart-item-image {
                width: 70px;
                height: 85px;
            }

            .footer-inner {
                display: block;
            }

            .help-text {
                margin-top: 20px;
            }

        }

    </style>

</head>

<body>

<!-- ========================================
     CHECKOUT HEADER
======================================== -->

<header class="checkout-header">

    <div class="checkout-header-inner">

        <a href="index.php" class="checkout-logo">

            <?php if (file_exists("uploads/logo.png")) { ?>

                <img src="uploads/logo.png" alt="MyStore">

            <?php } else { ?>

                <strong style="font-size:22px;">MyStore</strong>

            <?php } ?>

        </a>

        <div class="checkout-steps">

            <span class="checkout-step active">
                BAG
            </span>

            <span class="step-line"></span>

            <span class="checkout-step active">
                ADDRESS
            </span>

            <span class="step-line"></span>

            <span class="checkout-step">
                PAYMENT
            </span>

        </div>

        <div class="secure-checkout">

            <div class="secure-icon">
                ✓
            </div>

            100% SECURE

        </div>

    </div>

</header>


<?php if ($order_success) { ?>

<!-- ========================================
     ORDER SUCCESS
======================================== -->

<div class="checkout-wrapper">

    <div class="success-container">

        <div class="success-icon">
            ✓
        </div>

        <h2>Order Placed Successfully</h2>

        <p>
            Thank you for your purchase.
            Your order #<?php echo $order_id; ?> has been placed successfully.
        </p>

        <a href="index.php" class="success-button">
            CONTINUE SHOPPING
        </a>

    </div>

</div>

<?php } else { ?>


<!-- ========================================
     CHECKOUT CONTENT
======================================== -->

<div class="checkout-wrapper">

    <div class="checkout-layout">

        <!-- =================================
             LEFT
        ================================== -->

        <div class="checkout-left">

            <div class="section-title-row">

                <h2 class="section-title">
                    Select Delivery Address
                </h2>

                <button
                    type="button"
                    class="outline-button">
                    ADD NEW ADDRESS
                </button>

            </div>


            <div class="address-heading">
                DEFAULT ADDRESS
            </div>


            <!-- ADDRESS CARD -->

            <div class="address-card">

                <div class="address-top">

                    <div class="radio-circle"></div>

                    <span class="address-name">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </span>

                    <span class="home-label">
                        HOME
                    </span>

                </div>


                <div class="user-address">

                    <?php
                    if (!empty($user['address'])) {
                        echo nl2br(htmlspecialchars($user['address']));
                    } else {
                        echo "Your saved delivery address";
                    }
                    ?>

                </div>


                <div class="user-email">

                    Email:
                    <?php echo htmlspecialchars($user['email']); ?>

                </div>


                <div class="address-actions">

                    <button
                        type="button"
                        class="outline-button">
                        REMOVE
                    </button>

                    <button
                        type="button"
                        class="outline-button">
                        EDIT
                    </button>

                </div>

            </div>


            <!-- ADD ADDRESS -->

            <div class="add-address">

                + Add New Address

            </div>


            <!-- =================================
                 CART ITEMS
            ================================== -->

            <?php if (!empty($cart_items)) { ?>

            <div class="items-section">

                <h3 class="section-title mb-3">
                    Your Items
                </h3>


                <?php foreach ($cart_items as $item) { ?>

                <div class="cart-item">

                    <?php if (!empty($item['image'])) { ?>

                        <img
                            src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                            class="cart-item-image"
                            alt="<?php echo htmlspecialchars($item['name']); ?>">

                    <?php } else { ?>

                        <div class="cart-item-image
                                    d-flex
                                    align-items-center
                                    justify-content-center">

                            No Image

                        </div>

                    <?php } ?>


                    <div class="cart-item-info">

                        <div class="cart-item-name">

                            <?php echo htmlspecialchars($item['name']); ?>

                        </div>

                        <div class="cart-item-quantity">

                            Quantity:
                            <?php echo $item['quantity']; ?>

                        </div>

                    </div>


                    <div class="cart-item-price">

                        ₹<?php echo number_format($item['subtotal'], 2); ?>

                    </div>

                </div>

                <?php } ?>

            </div>

            <?php } else { ?>

                <div class="items-section">

                    <div class="alert alert-warning">
                        Your cart is empty.
                    </div>

                    <a
                        href="index.php"
                        class="success-button">

                        CONTINUE SHOPPING

                    </a>

                </div>

            <?php } ?>

        </div>


        <!-- =================================
             RIGHT SIDEBAR
        ================================== -->

        <div class="checkout-sidebar">

            <!-- DELIVERY ESTIMATES -->

            <div class="sidebar-title">
                DELIVERY ESTIMATES
            </div>


            <?php
            $estimate_image = "";

            if (!empty($cart_items[0]['image'])) {
                $estimate_image = $cart_items[0]['image'];
            }
            ?>


            <div class="delivery-box">

                <?php if (!empty($estimate_image)) { ?>

                    <img
                        src="uploads/<?php echo htmlspecialchars($estimate_image); ?>"
                        class="delivery-image"
                        alt="Product">

                <?php } else { ?>

                    <div class="delivery-image"></div>

                <?php } ?>


                <div class="delivery-text">

                    Estimated delivery by
                    <strong>
                        <?php
                        echo date(
                            "d M Y",
                            strtotime("+3 days")
                        );
                        ?>
                    </strong>

                </div>

            </div>


            <!-- PRICE DETAILS -->

            <div class="price-title">

                PRICE DETAILS
                (<?php echo count($cart_items); ?> Item<?php echo count($cart_items) != 1 ? 's' : ''; ?>)

            </div>


            <div class="price-row">

                <span>Total MRP</span>

                <span>
                    ₹<?php echo number_format($total, 2); ?>
                </span>

            </div>


            <?php if ($discount > 0) { ?>

            <div class="price-row discount-row">

                <span>
                    Discount on MRP
                </span>

                <span>
                    - ₹<?php echo number_format($discount, 2); ?>
                </span>

            </div>

            <?php } else { ?>

            <div class="price-row">

                <span>
                    Discount on MRP
                </span>

                <span>
                    ₹0
                </span>

            </div>

            <?php } ?>


            <div class="price-row">

                <span>
                    Platform Fee
                </span>

                <span>
                    ₹0
                </span>

            </div>


            <div class="price-divider"></div>


            <div class="total-row">

                <span>
                    Total Amount
                </span>

                <span>
                    ₹<?php echo number_format($final_total, 2); ?>
                </span>

            </div>


            <!-- COUPON -->

            <div class="coupon-box">

                <div class="coupon-title">

                    APPLY COUPON

                </div>


                <form method="POST" class="coupon-form">

                    <input
                        type="text"
                        name="coupon"
                        class="coupon-input"
                        placeholder="Enter coupon code"
                        value="<?php
                        echo isset($_SESSION['coupon_code'])
                            ? htmlspecialchars($_SESSION['coupon_code'])
                            : '';
                        ?>">

                    <button
                        type="submit"
                        name="apply_coupon"
                        class="coupon-button">

                        APPLY

                    </button>

                </form>


                <?php if (!empty($coupon_message)) { ?>

                    <div class="<?php
                        echo strpos(
                            strtolower($coupon_message),
                            'successfully'
                        ) !== false
                        ? 'coupon-success'
                        : 'coupon-error';
                    ?>">

                        <?php echo htmlspecialchars($coupon_message); ?>

                        <?php if (!empty($coupon_type)) { ?>

                            — <?php echo htmlspecialchars($coupon_type); ?>

                        <?php } ?>

                    </div>

                <?php } ?>

            </div>


            <!-- PLACE ORDER -->

            <form method="POST">

                <button
                    type="submit"
                    name="place_order"
                    class="continue-button mt-4"
                    <?php
                    if (empty($cart_items)) {
                        echo "disabled";
                    }
                    ?>>

                    CONTINUE

                </button>

            </form>

        </div>

    </div>

</div>

<?php } ?>


<!-- ========================================
     FOOTER
======================================== -->

<footer class="checkout-footer">

    <div class="footer-inner">

        <div class="payment-methods">

            <div class="payment-box">
                256-bit SSL
            </div>

            <div class="payment-box">
                VISA
            </div>

            <div class="payment-box">
                Mastercard
            </div>

            <div class="payment-box">
                AMEX
            </div>

            <div class="payment-box">
                Net Banking
            </div>

            <div class="payment-box">
                Cash on Delivery
            </div>

            <div class="payment-box">
                RuPay
            </div>

            <div class="payment-box">
                PayPal
            </div>

        </div>


        <div class="help-text">

            Need Help ? Contact Us

        </div>

    </div>

</footer>


</body>
</html>