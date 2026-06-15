<?php
session_start();
include "db.php";
include "includes/header.php";

// USER CHECK
if (!isset($_SESSION['user'])) {
    echo "<div class='alert alert-warning'>Please login first</div>";
    include "includes/footer.php";
    exit;
}

// ENSURE CART EXISTS
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$user_name = $_SESSION['user'];

// GET USER
$user_result = $conn->query("SELECT * FROM users WHERE name='$user_name'");
$user = $user_result->fetch_assoc();
$user_id = $user['id'];

// CALCULATE TOTAL
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $res = $conn->query("SELECT * FROM products WHERE id=$product_id");
        $p = $res->fetch_assoc();
        $total += $p['price'] * $quantity;
    }
}

// DEFAULTS
$discount = 0;
$final_total = $total;

// APPLY COUPON
if (isset($_POST['apply_coupon'])) {
    $coupon = $_POST['coupon'];

    $res = $conn->query("SELECT * FROM coupons WHERE code='$coupon' AND expiry > NOW()");

    if ($res->num_rows > 0) {
        $c = $res->fetch_assoc();

        if ($c['discount_type'] == 'percent') {
            $discount = ($total * $c['value']) / 100;
        } else {
            $discount = $c['value'];
        }

        $_SESSION['discount'] = $discount;
        $final_total = $total - $discount;

        echo "<div class='alert alert-success'>Coupon applied!</div>";
    } else {
        echo "<div class='alert alert-danger'>Invalid coupon</div>";
    }
}

// LOAD SAVED DISCOUNT
if (isset($_SESSION['discount'])) {
    $discount = $_SESSION['discount'];
    $final_total = $total - $discount;
}

// PLACE ORDER
if (isset($_POST['place_order']) && !empty($_SESSION['cart'])) {

    $conn->query("INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $final_total)");
    $order_id = $conn->insert_id;

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $res = $conn->query("SELECT * FROM products WHERE id=$product_id");
        $p = $res->fetch_assoc();

        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price)
                      VALUES ($order_id, $product_id, $quantity, ".$p['price'].")");
    }

    unset($_SESSION['cart']);
    unset($_SESSION['discount']);

    echo "<div class='alert alert-success'>Order placed successfully!</div>";
}
?>

<h2 class="mb-4">Checkout</h2>

<?php if (empty($_SESSION['cart'])) { ?>
    <div class="alert alert-warning">Cart is empty</div>
<?php } ?>

<div class="row">

    <!-- LEFT: ORDER SUMMARY -->
    <div class="col-md-7">

        <div class="card p-4 mb-3">
            <h4>Order Summary</h4>

            <?php if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    $res = $conn->query("SELECT * FROM products WHERE id=$product_id");
                    $p = $res->fetch_assoc();
            ?>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span><?php echo $p['name']; ?> (x<?php echo $quantity; ?>)</span>
                    <span>₹<?php echo $p['price'] * $quantity; ?></span>
                </div>

            <?php 
                }
            } else {
                echo "<p>No items in cart</p>";
            }
            ?>

        </div>

    </div>

    <!-- RIGHT: PAYMENT -->
    <div class="col-md-5">

        <div class="card p-4">

            <h4>Payment Details</h4>

            <!-- COUPON -->
            <form method="POST" class="mb-3">
                <input type="text" name="coupon" 
                       class="form-control mb-2" 
                       placeholder="Enter coupon code">

                <button type="submit" name="apply_coupon" class="btn btn-dark w-100">
                    Apply Coupon
                </button>
            </form>

            <hr>

            <!-- PRICE -->
            <p>Subtotal: ₹<?php echo $total; ?></p>

            <?php if ($discount > 0) { ?>
                <p class="text-success">Discount: -₹<?php echo $discount; ?></p>
            <?php } ?>

            <h4>Total: ₹<?php echo $final_total; ?></h4>

            <!-- PLACE ORDER -->
            <form method="POST">
                <button type="submit" name="place_order" 
                    class="btn btn-dark w-100 mt-3"
                    <?php if(empty($_SESSION['cart'])) echo "disabled"; ?>>
                    Place Order
                </button>
            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>