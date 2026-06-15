<?php
session_start();

if (!isset($_SESSION['vendor_id'])) {
    header("Location: index.php");
    exit;
}

include "../db.php";
include "includes/header.php";

$vendor_user_id = $_SESSION['vendor_id'];

// GET VENDOR
$v = $conn->query("SELECT * FROM vendors WHERE user_id=$vendor_user_id")->fetch_assoc();

if (!$v) {
    echo "<div class='alert alert-danger'>Vendor not found</div>";
    exit;
}

$vid = $v['id'];

// DATA
$total_sales = $conn->query("
SELECT SUM(oi.price * oi.quantity) as total
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE p.vendor_id = $vid
")->fetch_assoc()['total'] ?? 0;

$total_orders = $conn->query("
SELECT COUNT(DISTINCT oi.order_id) as total
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE p.vendor_id = $vid
")->fetch_assoc()['total'] ?? 0;

$total_products = $conn->query("
SELECT COUNT(*) as total FROM products WHERE vendor_id=$vid
")->fetch_assoc()['total'] ?? 0;
?>

<h2 class="mb-4 fw-bold">Dashboard</h2>

<div class="row g-4">

    <!-- SALES -->
    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6 class="text-secondary">Total Sales</h6>
            <h3>₹<?php echo number_format($total_sales); ?></h3>
        </div>
    </div>

    <!-- ORDERS -->
    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6 class="text-secondary">Orders</h6>
            <h3><?php echo $total_orders; ?></h3>
        </div>
    </div>

    <!-- PRODUCTS -->
    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6 class="text-secondary">Products</h6>
            <h3><?php echo $total_products; ?></h3>
        </div>
    </div>

</div>

<!-- RECENT ORDERS -->
<div class="card p-4 mt-5 shadow-sm">
    <h5 class="mb-3">Recent Orders</h5>

    <table class="table">
        <tr>
            <th>Order</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Status</th>
        </tr>

        <?php
        $orders = $conn->query("
        SELECT o.id, o.status, p.name, oi.quantity
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE p.vendor_id = $vid
        ORDER BY o.id DESC LIMIT 5
        ");

        while ($o = $orders->fetch_assoc()) {
        ?>
        <tr>
            <td>#<?php echo $o['id']; ?></td>
            <td><?php echo $o['name']; ?></td>
            <td><?php echo $o['quantity']; ?></td>
            <td><span class="badge bg-secondary"><?php echo $o['status']; ?></span></td>
        </tr>
        <?php } ?>

    </table>
</div>