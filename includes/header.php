<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MyStore</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            background: #f8f8f8;
            color: #282c3f;
        }

        /* =========================
           HEADER
        ========================= */

        .main-header {
            width: 100%;
            height: 80px;
            background: #ffffff;
            border-bottom: 1px solid #eeeeee;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            height: 100%;
            max-width: 1500px;
            margin: auto;

            display: flex;
            align-items: center;

            padding: 0 35px;
            gap: 25px;
        }

        /* =========================
           LOGO
        ========================= */

        .brand-logo {
            width: 100px;
            min-width: 100px;

            display: flex;
            align-items: center;
            justify-content: flex-start;

            text-decoration: none;
        }

        .brand-logo img {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
        }

        /* =========================
           NAVIGATION
        ========================= */

        .main-navigation {
            display: flex;
            align-items: center;
            gap: 38px;

            white-space: nowrap;
        }

        .main-navigation a {
            position: relative;

            text-decoration: none;
            color: #282c3f;

            font-size: 15px;
            font-weight: 700;

            letter-spacing: 0.3px;

            padding: 31px 0 28px;

            transition: color 0.2s ease;
        }

        .main-navigation a:hover {
            color: #ff3f6c;
        }

        .main-navigation a::after {
            content: "";

            position: absolute;

            left: 0;
            right: 0;
            bottom: 18px;

            height: 3px;

            background: #ff3f6c;

            transform: scaleX(0);
            transform-origin: center;

            transition: transform 0.2s ease;
        }

        .main-navigation a:hover::after {
            transform: scaleX(1);
        }

        /* NEW LABEL */

        .studio-link {
            position: relative;
        }

        .new-label {
            position: absolute;

            top: 15px;
            right: -22px;

            color: #ff3f6c;

            font-size: 9px;
            font-weight: 700;

            letter-spacing: 0;
        }

        /* =========================
           SEARCH
        ========================= */

        .header-search {
            flex: 1;

            max-width: 650px;
            min-width: 200px;

            height: 42px;

            display: flex;
            align-items: center;

            background: #f5f5f6;

            border-radius: 4px;

            margin-left: auto;
        }

        .search-icon {
            width: 50px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #696e79;

            font-size: 16px;
        }

        .header-search input {
            flex: 1;

            height: 100%;

            border: none;
            outline: none;

            background: transparent;

            font-size: 14px;

            color: #282c3f;

            padding-right: 15px;
        }

        .header-search input::placeholder {
            color: #7e818c;
        }

        .header-search:focus-within {
            background: #eeeeef;
        }

        /* =========================
           ACTIONS
        ========================= */

        .header-actions {
            display: flex;
            align-items: center;

            gap: 28px;

            height: 100%;
        }

        .header-action {
            min-width: 45px;

            height: 100%;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-decoration: none;

            color: #282c3f;

            position: relative;

            transition: color 0.2s ease;
        }

        .header-action:hover {
            color: #ff3f6c;
        }

        .header-action i {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header-action span {
            font-size: 11px;
            font-weight: 600;
        }

        /* CART COUNT */

        .cart-count {
            position: absolute;

            top: 15px;
            right: 1px;

            background: #ff3f6c;
            color: #ffffff;

            width: 17px;
            height: 17px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 9px;
            font-weight: 700;
        }

        /* =========================
           MOBILE MENU
        ========================= */

        .mobile-menu-button {
            display: none;

            border: none;
            background: transparent;

            font-size: 22px;
            color: #282c3f;
        }

        /* =========================
           PAGE CONTAINER
        ========================= */

        .page-container {
            width: 100%;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1200px) {

            .header-container {
                padding: 0 20px;
                gap: 18px;
            }

            .main-navigation {
                gap: 20px;
            }

            .main-navigation a {
                font-size: 13px;
            }

            .header-actions {
                gap: 15px;
            }

        }

        @media (max-width: 992px) {

            .main-header {
                height: 70px;
            }

            .header-container {
                padding: 0 18px;
            }

            .brand-logo {
                width: 70px;
                min-width: 70px;
            }

            .main-navigation {
                display: none;
            }

            .mobile-menu-button {
                display: block;
            }

            .header-search {
                max-width: none;
                margin-left: 0;
            }

            .header-actions {
                gap: 12px;
            }

        }

        @media (max-width: 600px) {

            .header-container {
                gap: 10px;
                padding: 0 12px;
            }

            .brand-logo {
                width: 45px;
                min-width: 45px;
            }

            .brand-logo img {
                max-width: 42px;
            }

            .header-search {
                height: 38px;
            }

            .search-icon {
                width: 38px;
                font-size: 14px;
            }

            .header-search input {
                font-size: 12px;
            }

            .header-actions {
                gap: 8px;
            }

            .header-action {
                min-width: 30px;
            }

            .header-action i {
                font-size: 17px;
            }

            .header-action span {
                display: none;
            }

            .cart-count {
                top: 8px;
                right: -3px;
            }

        }

    </style>

</head>

<body>

<!-- =========================
     MAIN HEADER
========================= -->

<header class="main-header">

    <div class="header-container">

        <!-- LOGO -->

        <a
            href="index.php"
            class="brand-logo"
            aria-label="MyStore Home"
        >

            <img
                src="uploads/logo.png"
                alt="MyStore Logo"
            >

        </a>


        <!-- MOBILE MENU -->

        <button
            class="mobile-menu-button"
            type="button"
            onclick="toggleMobileMenu()"
        >

            <i class="fa-solid fa-bars"></i>

        </button>


        <!-- NAVIGATION -->

        <!-- <nav class="main-navigation">

            <a href="/ecommerce/search.php?category=Men">
                MEN
            </a>

            <a href="/ecommerce/search.php?category=Women">
                WOMEN
            </a>

            <a href="/ecommerce/search.php?category=Kids">
                KIDS
            </a>

            <a href="/ecommerce/search.php?category=Home">
                HOME
            </a>

            <a href="/ecommerce/search.php?category=Beauty">
                BEAUTY
            </a>

            <a href="/ecommerce/search.php?category=GenZ">
                GENZ
            </a>

            <a
                href="/ecommerce/search.php?category=Studio"
                class="studio-link"
            >
                STUDIO

                <span class="new-label">
                    NEW
                </span>

            </a>

        </nav> -->


        <!-- SEARCH -->

        <form
            class="header-search"
            method="GET"
            action="/ecommerce/search.php"
        >

            <div class="search-icon">

                <i class="fa-solid fa-magnifying-glass"></i>

            </div>

            <input
                type="text"
                name="q"
                placeholder="Search for products, brands and more"
                autocomplete="off"
            >

        </form>


        <!-- HEADER ACTIONS -->

        <div class="header-actions">

            <!-- PROFILE -->

            <a
                href="/ecommerce/profile.php"
                class="header-action"
            >

                <i class="fa-regular fa-user"></i>

                <span>
                    Profile
                </span>

            </a>


            <!-- WISHLIST -->

            <a
                href="/ecommerce/wishlist.php"
                class="header-action"
            >

                <i class="fa-regular fa-heart"></i>

                <span>
                    Wishlist
                </span>

            </a>


            <!-- BAG -->

            <a
                href="/ecommerce/cart.php"
                class="header-action"
            >

                <i class="fa-solid fa-bag-shopping"></i>

                <span>
                    Bag
                </span>

                <?php

                $cart_count = 0;

                if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {

                    foreach ($_SESSION['cart'] as $item) {

                        if (is_array($item)) {

                            $cart_count += isset($item['quantity'])
                                ? (int)$item['quantity']
                                : 1;

                        } else {

                            $cart_count++;

                        }

                    }

                }

                if ($cart_count > 0) {

                    ?>

                    <span class="cart-count">
                        <?php echo $cart_count; ?>
                    </span>

                    <?php

                }

                ?>

            </a>

        </div>

    </div>

</header>


<!-- =========================
     MOBILE NAVIGATION
========================= -->

<div
    id="mobileMenu"
    style="
        display:none;
        background:#ffffff;
        border-bottom:1px solid #eeeeee;
        padding:15px 20px;
    "
>

    <a
        href="/ecommerce/search.php?category=Men"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        MEN
    </a>

    <a
        href="/ecommerce/search.php?category=Women"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        WOMEN
    </a>

    <a
        href="/ecommerce/search.php?category=Kids"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        KIDS
    </a>

    <a
        href="/ecommerce/search.php?category=Home"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        HOME
    </a>

    <a
        href="/ecommerce/search.php?category=Beauty"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        BEAUTY
    </a>

    <a
        href="/ecommerce/search.php?category=GenZ"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        GENZ
    </a>

    <a
        href="/ecommerce/search.php?category=Studio"
        class="d-block py-2 text-dark text-decoration-none fw-semibold"
    >
        STUDIO
        <span style="color:#ff3f6c;font-size:10px;">
            NEW
        </span>
    </a>

</div>


<script>

function toggleMobileMenu() {

    const menu = document.getElementById("mobileMenu");

    if (menu.style.display === "none" || menu.style.display === "") {

        menu.style.display = "block";

    } else {

        menu.style.display = "none";

    }

}

</script>


<!-- PAGE CONTENT -->

<div class="page-container">