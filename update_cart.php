<?php
session_start();
include "db.php";

$id = $_POST['product_id'];
$action = $_POST['action'];

// update cart
if ($action == "increase") {
    $_SESSION['cart'][$id]++;
}

if ($action == "decrease") {
    if ($_SESSION['cart'][$id] > 1) {
        $_SESSION['cart'][$id]--;
    } else {
        unset($_SESSION['cart'][$id]);
    }
}

// calculate total
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $res = $conn->query("SELECT price FROM products WHERE id=$pid");
        $p = $res->fetch_assoc();
        $total += $p['price'] * $qty;
    }
}

// send response
echo $total;
?>