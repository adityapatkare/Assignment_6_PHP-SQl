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

$vendor_id = $v['id'];

// FETCH ORDERS
$res = $conn->query("
SELECT o.id as order_id, o.total_amount, o.status,
p.name, oi.quantity
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN orders o ON oi.order_id = o.id
WHERE p.vendor_id = $vendor_id
ORDER BY o.id DESC
");
?>

<h2 class="mb-4 fw-bold">My Orders</h2>

<div class="card p-4 shadow-sm">

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>#Order</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) { ?>
                <tr>
                    <td><strong>#<?php echo $row['order_id']; ?></strong></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                </tr>
        <?php } } else { ?>
            <tr>
                <td colspan="4" class="text-center text-muted">No orders found</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>