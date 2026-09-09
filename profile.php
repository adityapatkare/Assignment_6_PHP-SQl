<?php
session_start();
include "db.php";
include "includes/header.php";
?>

<style>

/* =========================================
   PROFILE PAGE
========================================= */

.profile-page {
    background: #fff;
    min-height: calc(100vh - 100px);
    padding: 60px 0 90px;
    color: #282c3f;
}

.profile-container {
    width: 92%;
    max-width: 1350px;
    margin: auto;
}


/* =========================================
   LOGGED OUT STATE
========================================= */

.login-profile-page {
    min-height: 620px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.login-profile-content {
    width: 100%;
    max-width: 550px;
    margin: auto;
    padding: 50px 20px;
}

.login-profile-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 30px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;
    background: #fff5f7;
    color: #ff3f6c;

    font-size: 38px;
}

.login-profile-content h1 {
    margin: 0 0 12px;

    font-size: 25px;
    font-weight: 700;

    letter-spacing: .5px;
    color: #282c3f;
}

.login-profile-content p {
    margin: 0 auto 30px;

    color: #7e818c;
    font-size: 14px;
    line-height: 1.7;
}


/* LOGIN BUTTON */

.login-profile-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 180px;
    padding: 14px 30px;

    background: #fff;
    border: 1px solid #ff3f6c;

    color: #ff3f6c;
    text-decoration: none;

    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;

    transition: all .2s ease;
}

.login-profile-btn:hover {
    background: #ff3f6c;
    color: #fff;
}


/* REGISTER TEXT */

.login-register-text {
    margin-top: 25px;
    font-size: 13px;
    color: #94969f;
}

.login-register-text a {
    color: #ff3f6c;
    text-decoration: none;
    font-weight: 600;
}


/* =========================================
   LOGGED IN PROFILE
========================================= */

.profile-heading {
    margin-bottom: 35px;
}

.profile-heading h1 {
    font-size: 28px;
    font-weight: 700;
    color: #282c3f;
    margin-bottom: 7px;
}

.profile-heading p {
    margin: 0;
    color: #7e818c;
    font-size: 14px;
}


/* PROFILE GRID */

.profile-grid {
    display: grid;
    grid-template-columns: 330px 1fr;
    gap: 30px;
    align-items: start;
}


/* =========================================
   USER CARD
========================================= */

.user-card {
    background: #fff;
    border: 1px solid #eaeaec;
    padding: 30px;
    text-align: center;
}

.profile-image-wrapper {
    width: 105px;
    height: 105px;

    margin: 0 auto 20px;

    border-radius: 50%;
    overflow: hidden;

    border: 1px solid #e5e5e5;
    background: #f7f7f7;
}

.profile-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-card h2 {
    font-size: 20px;
    font-weight: 700;
    color: #282c3f;
    margin-bottom: 7px;
}

.user-email {
    font-size: 13px;
    color: #7e818c;
    word-break: break-word;
}


/* ACCOUNT DETAILS */

.account-details {
    text-align: left;

    border-top: 1px solid #eeeeee;

    padding-top: 20px;
    margin-top: 20px;
}

.account-detail {
    margin-bottom: 18px;
}

.account-detail:last-child {
    margin-bottom: 0;
}

.account-detail-label {
    display: block;

    font-size: 11px;
    text-transform: uppercase;

    letter-spacing: 1px;

    color: #94969f;

    margin-bottom: 4px;
}

.account-detail-value {
    font-size: 14px;
    color: #282c3f;
    font-weight: 500;
}


/* LOGOUT */

.logout-btn {
    display: block;

    width: 100%;

    padding: 13px 15px;

    margin-top: 25px;

    border: 1px solid #ff3f6c;

    background: #fff;

    color: #ff3f6c;

    text-decoration: none;

    font-size: 13px;
    font-weight: 700;

    letter-spacing: .7px;

    transition: .2s ease;
}

.logout-btn:hover {
    background: #ff3f6c;
    color: #fff;
}


/* =========================================
   ORDERS
========================================= */

.orders-card {
    background: #fff;
    border: 1px solid #eaeaec;
}

.orders-header {
    padding: 25px 30px;
    border-bottom: 1px solid #eeeeee;
}

.orders-header h2 {
    margin: 0 0 5px;

    font-size: 20px;
    font-weight: 700;

    color: #282c3f;
}

.orders-header p {
    margin: 0;

    color: #94969f;
    font-size: 13px;
}


/* ORDER */

.order-item {
    padding: 25px 30px;

    border-bottom: 1px solid #eeeeee;

    transition: .2s ease;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item:hover {
    background: #fafafa;
}


.order-top {
    display: flex;

    justify-content: space-between;
    align-items: center;

    gap: 15px;

    margin-bottom: 18px;
}

.order-number {
    font-size: 15px;
    font-weight: 700;
    color: #282c3f;
}


/* STATUS */

.order-status {
    padding: 6px 12px;

    font-size: 11px;
    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: .5px;
}

.status-pending {
    background: #fff4e5;
    color: #c77b00;
}

.status-processing {
    background: #eef3ff;
    color: #3f51b5;
}

.status-completed,
.status-delivered {
    background: #e8f8ef;
    color: #16834b;
}

.status-cancelled {
    background: #ffe9ed;
    color: #d9304f;
}


/* ORDER INFO */

.order-info {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 15px;
}

.order-info-box {
    background: #f8f8f8;
    padding: 14px 16px;
}

.order-info-label {
    display: block;

    color: #94969f;

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: .7px;

    margin-bottom: 5px;
}

.order-info-value {
    font-size: 15px;
    font-weight: 600;
    color: #282c3f;
}


/* =========================================
   NO ORDERS
========================================= */

.no-orders {
    text-align: center;
    padding: 70px 30px;
}

.no-orders-icon {
    width: 65px;
    height: 65px;

    margin: 0 auto 20px;

    border-radius: 50%;

    background: #f8f8f8;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 25px;

    color: #94969f;
}

.no-orders h3 {
    font-size: 18px;
    margin-bottom: 8px;
    color: #282c3f;
}

.no-orders p {
    color: #94969f;
    font-size: 13px;

    margin-bottom: 20px;
}

.shop-btn {
    display: inline-block;

    padding: 12px 25px;

    background: #ff3f6c;

    color: #fff;

    text-decoration: none;

    font-size: 12px;
    font-weight: 700;

    letter-spacing: .7px;

    transition: .2s ease;
}

.shop-btn:hover {
    background: #e6335e;
    color: #fff;
}


/* =========================================
   RESPONSIVE
========================================= */

@media(max-width: 900px) {

    .profile-grid {
        grid-template-columns: 1fr;
    }

    .user-card {
        max-width: 500px;
    }

}


@media(max-width: 600px) {

    .profile-page {
        padding: 30px 0 60px;
    }

    .profile-container {
        width: 94%;
    }

    .login-profile-content {
        padding: 40px 15px;
    }

    .login-profile-content h1 {
        font-size: 22px;
    }

    .profile-heading h1 {
        font-size: 23px;
    }

    .orders-header,
    .order-item {
        padding: 20px;
    }

    .order-top {
        align-items: flex-start;
        flex-direction: column;
    }

    .order-info {
        grid-template-columns: 1fr;
    }

}

</style>


<?php

/* =========================================
   LOGGED OUT
========================================= */

if (!isset($_SESSION['user'])) {
?>

<div class="profile-page">

    <div class="profile-container">

        <div class="login-profile-page">

            <div class="login-profile-content">

                <div class="login-profile-icon">
                    <i class="fa-regular fa-user"></i>
                </div>

                <h1>
                    PLEASE LOG IN
                </h1>

                <p>
                    Login to view your profile, orders,
                    account details and shopping activity.
                </p>

                <a href="login.php" class="login-profile-btn">
                    LOGIN
                </a>

                <div class="login-register-text">

                    New to our store?

                    <a href="register.php">
                        Create an account
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include "includes/footer.php";
exit;

}


/* =========================================
   GET USER
========================================= */

$user_name = $_SESSION['user'];

$stmt = $conn->prepare(
    "SELECT * FROM users WHERE name = ? LIMIT 1"
);

$stmt->bind_param("s", $user_name);

$stmt->execute();

$user_result = $stmt->get_result();

$user = $user_result->fetch_assoc();


if (!$user) {

?>

<div class="profile-page">

    <div class="login-profile-page">

        <div class="login-profile-content">

            <div class="login-profile-icon">
                <i class="fa-regular fa-circle-xmark"></i>
            </div>

            <h1>
                ACCOUNT NOT FOUND
            </h1>

            <p>
                We couldn't find your account information.
                Please log in again.
            </p>

            <a href="logout.php" class="login-profile-btn">
                LOG OUT
            </a>

        </div>

    </div>

</div>

<?php

include "includes/footer.php";
exit;

}


$user_id = $user['id'];


/* =========================================
   GET ORDERS
========================================= */

$order_stmt = $conn->prepare(
    "SELECT *
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);

$order_stmt->bind_param("i", $user_id);

$order_stmt->execute();

$order_result = $order_stmt->get_result();

?>


<div class="profile-page">

<div class="profile-container">


    <!-- PAGE HEADER -->

    <div class="profile-heading">

        <h1>
            My Profile
        </h1>

        <p>
            Manage your account and view your order history.
        </p>

    </div>


    <!-- PROFILE GRID -->

    <div class="profile-grid">


        <!-- USER -->

        <div class="user-card">

            <div class="profile-image-wrapper">

                <img
                    src="uploads/IMG_1603.JPG"
                    alt="Profile Picture"
                    onerror="this.src='uploads/logo.png';"
                >

            </div>


            <h2>
                <?php
                echo htmlspecialchars($user['name']);
                ?>
            </h2>


            <div class="user-email">

                <?php
                echo htmlspecialchars($user['email']);
                ?>

            </div>


            <div class="account-details">


                <div class="account-detail">

                    <span class="account-detail-label">
                        Account Name
                    </span>

                    <span class="account-detail-value">

                        <?php
                        echo htmlspecialchars($user['name']);
                        ?>

                    </span>

                </div>


                <div class="account-detail">

                    <span class="account-detail-label">
                        Email Address
                    </span>

                    <span class="account-detail-value">

                        <?php
                        echo htmlspecialchars($user['email']);
                        ?>

                    </span>

                </div>


                <div class="account-detail">

                    <span class="account-detail-label">
                        Account Type
                    </span>

                    <span class="account-detail-value">

                        <?php

                        echo isset($user['role'])
                            ? ucfirst(
                                htmlspecialchars(
                                    $user['role']
                                )
                            )
                            : 'Customer';

                        ?>

                    </span>

                </div>


            </div>


            <a
                href="logout.php"
                class="logout-btn"
            >
                LOG OUT
            </a>

        </div>


        <!-- ORDERS -->

        <div class="orders-card">

            <div class="orders-header">

                <h2>
                    My Orders
                </h2>

                <p>
                    View your recent purchases and order status.
                </p>

            </div>


            <?php

            if ($order_result->num_rows > 0) {

                while (
                    $order =
                    $order_result->fetch_assoc()
                ) {


                    $status = strtolower(
                        trim(
                            $order['status']
                            ?? 'placed'
                        )
                    );


                    $status_class =
                        'status-pending';


                    if (
                        $status === 'completed'
                        ||
                        $status === 'delivered'
                    ) {

                        $status_class =
                            'status-completed';

                    }

                    elseif (
                        $status === 'processing'
                    ) {

                        $status_class =
                            'status-processing';

                    }

                    elseif (
                        $status === 'cancelled'
                    ) {

                        $status_class =
                            'status-cancelled';

                    }

            ?>


            <div class="order-item">


                <div class="order-top">

                    <div class="order-number">

                        Order
                        #<?php
                        echo $order['id'];
                        ?>

                    </div>


                    <span
                        class="order-status <?php
                        echo $status_class;
                        ?>"
                    >

                        <?php

                        echo ucfirst(
                            htmlspecialchars(
                                $order['status']
                                ?? 'Placed'
                            )
                        );

                        ?>

                    </span>

                </div>


                <div class="order-info">


                    <div class="order-info-box">

                        <span class="order-info-label">
                            Total Amount
                        </span>

                        <span class="order-info-value">

                            ₹<?php

                            echo number_format(
                                (float)
                                $order['total_amount'],
                                2
                            );

                            ?>

                        </span>

                    </div>


                    <div class="order-info-box">

                        <span class="order-info-label">
                            Order Date
                        </span>

                        <span class="order-info-value">

                            <?php

                            echo date(
                                'd M Y',
                                strtotime(
                                    $order['created_at']
                                )
                            );

                            ?>

                        </span>

                    </div>


                </div>


            </div>


            <?php

                }

            }

            else {

            ?>


            <div class="no-orders">

                <div class="no-orders-icon">

                    <i class="fa-solid fa-bag-shopping"></i>

                </div>


                <h3>
                    No Orders Yet
                </h3>


                <p>
                    You haven't placed any orders yet.
                    Start shopping to see your orders here.
                </p>


                <a
                    href="index.php"
                    class="shop-btn"
                >
                    START SHOPPING
                </a>

            </div>


            <?php

            }

            ?>


        </div>

    </div>

</div>

</div>


<?php

include "includes/footer.php";

?>