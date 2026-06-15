<?php
session_start();
include "db.php";
include "includes/header.php";

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    echo "<div class='alert alert-success'>Product added to cart!</div>";
}

// INCREASE QUANTITY
if (isset($_POST['increase'])) {
    $id = $_POST['product_id'];
    $_SESSION['cart'][$id]++;
}

// DECREASE QUANTITY
if (isset($_POST['decrease'])) {
    $id = $_POST['product_id'];

    if ($_SESSION['cart'][$id] > 1) {
        $_SESSION['cart'][$id]--;
    } else {
        unset($_SESSION['cart'][$id]);
    }
}

// Remove item
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
}

?>

<h2 class="mb-4">Your Cart</h2>

<?php
$total = 0;

if (!empty($_SESSION['cart'])) {
?>

<div class="row">

    <!-- CART ITEMS -->
    <div class="col-md-8">

<?php
    if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {

        $sql = "SELECT * FROM products WHERE id=$product_id";
        $result = $conn->query($sql);
        $product = $result->fetch_assoc();

        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
?>

        <div class="card mb-3 p-3">
            <div class="row align-items-center">

                <div class="col-md-3">
                    <?php if (!empty($product['image'])) { ?>
                        <img src="uploads/<?php echo $product['image']; ?>" 
                             class="img-fluid rounded">
                    <?php } ?>
                </div>

                <div class="col-md-6">
                    <h5><?php echo $product['name']; ?></h5>
                    <p class="mb-1">Price: ₹<?php echo $product['price']; ?></p>
                    <div class="d-flex align-items-center gap-2 mt-2">

    <button onclick="updateCart(<?php echo $product_id; ?>, 'decrease')" 
            class="btn btn-outline-primary btn-sm">−</button>

    <span id="qty-<?php echo $product_id; ?>" class="fw-bold">
        <?php echo $quantity; ?>
    </span>

    <button onclick="updateCart(<?php echo $product_id; ?>, 'increase')" 
            class="btn btn-outline-primary btn-sm">+</button>

</div>
                    <p class="fw-bold">Subtotal: ₹<?php echo $subtotal; ?></p>
                </div>

                <div class="col-md-3 text-end">
                    <a href="cart.php?remove=<?php echo $product_id; ?>" 
                       class="btn btn-danger btn-sm">
                       Remove
                    </a>
                </div>

            </div>
        </div>

<?php
    }
?>

    </div>

    <!-- SUMMARY -->
    <div class="col-md-4">

        <div class="card p-4">

            <h4>Total</h4>
            <h2 class="mb-3">₹<span id="cart-total"><?php echo $total; ?></span></h2>

            <a href="checkout.php" class="btn btn-dark w-100">
                Proceed to Checkout
            </a>

        </div>

    </div>

</div>

<?php
    }
} else {
    echo "<div class='alert alert-warning'>Cart is empty</div>";
}
?>
<script>
function updateCart(productId, action) {

    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'product_id=' + productId + '&action=' + action
    })
    .then(response => response.text())
    .then(data => {
        // update total
        document.getElementById("cart-total").innerText = data;

        // update quantity visually
        let qtyElement = document.getElementById("qty-" + productId);

        if (action === "increase") {
            qtyElement.innerText++;
        } else {
            if (qtyElement.innerText > 1) {
                qtyElement.innerText--;
            } else {
                location.reload(); // remove item if 0
            }
        }
    });
}
</script>

<?php include "includes/footer.php"; ?>