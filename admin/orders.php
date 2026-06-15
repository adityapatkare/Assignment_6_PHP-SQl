<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// UPDATE STATUS
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET status='$status' WHERE id=$order_id");
}

// FETCH ORDERS
$orders = $conn->query("
    SELECT o.*, u.name as user_name 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
");
?>

<h2 class="mb-4 fw-bold">Manage Orders</h2>

<div class="card p-3 shadow-sm">

<table class="table table-hover align-middle">

<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Total</th>
    <th>Status</th>
    <th>Items</th>
    <th>Update</th>
</tr>

<?php while($order = $orders->fetch_assoc()) { ?>

<tr>
    <td>#<?php echo $order['id']; ?></td>
    <td><?php echo $order['user_name']; ?></td>
    <td>₹<?php echo $order['total_amount']; ?></td>

    <!-- STATUS BADGE -->
    <td>
        <?php 
        $status = $order['status'] ?? 'Pending';

        if ($status == "Pending") echo "<span class='badge bg-secondary'>Pending</span>";
        elseif ($status == "Processing") echo "<span class='badge bg-warning'>Processing</span>";
        elseif ($status == "Shipped") echo "<span class='badge bg-info'>Shipped</span>";
        elseif ($status == "Delivered") echo "<span class='badge bg-success'>Delivered</span>";
        ?>
    </td>

    <!-- ORDER ITEMS -->
    <td>
        <?php
        $items = $conn->query("
            SELECT oi.*, p.name 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = {$order['id']}
        ");

        while ($item = $items->fetch_assoc()) {
            echo "<div class='small'>{$item['name']} (x{$item['quantity']})</div>";
        }
        ?>
    </td>

    <!-- UPDATE FORM -->
    <td>
        <form method="POST" class="d-flex gap-2">

            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

            <select name="status" class="form-select form-select-sm">

                <option <?php if($status=='Pending') echo 'selected'; ?>>Pending</option>
                <option <?php if($status=='Processing') echo 'selected'; ?>>Processing</option>
                <option <?php if($status=='Shipped') echo 'selected'; ?>>Shipped</option>
                <option <?php if($status=='Delivered') echo 'selected'; ?>>Delivered</option>

            </select>

            <button name="update_status" class="btn btn-dark btn-sm">
                Save
            </button>

        </form>
    </td>

</tr>

<?php } ?>

</table>

</div>

<style>
.table {
    border-radius: 10px;
    overflow: hidden;
}

.card {
    border-radius: 12px;
}

/* DARK MODE FIX */
.dark .table {
    color: #e2e8f0;
}
</style>

<?php include "includes/footer.php"; ?>