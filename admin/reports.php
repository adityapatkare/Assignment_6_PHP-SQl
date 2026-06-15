<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

/* =========================
   EXPORT CSV
========================= */
if (isset($_GET['export']) && $_GET['export'] == 'csv') {

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report.csv"');

    $output = fopen("php://output", "w");

    fputcsv($output, ['Order ID', 'Customer', 'Total', 'Status', 'Date']);

    $export = $conn->query("
        SELECT o.id, u.name, o.total_amount, o.status, o.created_at
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.id DESC
    ");

    while ($row = $export->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

/* =========================
   ANALYTICS
========================= */

$total_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;

$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

$avg_order = $total_orders ? round($total_sales / $total_orders) : 0;

/* TOP PRODUCTS */
$top_products = $conn->query("
    SELECT p.name, SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");

/* RECENT ORDERS */
$recent_orders = $conn->query("
    SELECT o.*, u.name 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
    LIMIT 5
");
?>

<h2 class="mb-4 fw-bold">Reports & Analytics</h2>

<!-- ANALYTICS CARDS -->
<div class="row mb-4">

    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6>Total Sales</h6>
            <h3>₹<?php echo $total_sales; ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6>Total Orders</h6>
            <h3><?php echo $total_orders; ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-4 shadow-sm">
            <h6>Average Order Value</h6>
            <h3>₹<?php echo $avg_order; ?></h3>
        </div>
    </div>

</div>

<!-- EXPORT BUTTON -->
<div class="mb-4">
    <a href="reports.php?export=csv" class="btn btn-success">
        Export CSV
    </a>
</div>

<div class="row">

    <!-- TOP PRODUCTS -->
    <div class="col-md-6">
        <div class="card p-4 shadow-sm mb-4">

            <h5 class="mb-3">Top Selling Products</h5>

            <?php while ($p = $top_products->fetch_assoc()) { ?>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span><?php echo $p['name']; ?></span>
                    <span><?php echo $p['total_sold']; ?> sold</span>
                </div>
            <?php } ?>

        </div>
    </div>

    <!-- RECENT ORDERS -->
    <div class="col-md-6">
        <div class="card p-4 shadow-sm mb-4">

            <h5 class="mb-3">Recent Orders</h5>

            <?php while ($o = $recent_orders->fetch_assoc()) { ?>
                <div class="border-bottom py-2">
                    <strong>#<?php echo $o['id']; ?></strong> -
                    <?php echo $o['name']; ?> -
                    ₹<?php echo $o['total_amount']; ?>
                    <br>

                    <small class="text-secondary">
                        Status: <?php echo $o['status'] ?? 'Pending'; ?>
                    </small>
                </div>
            <?php } ?>

        </div>
    </div>

</div>

<style>
.card {
    border-radius: 12px;
}

/* DARK MODE FIX */
.dark .card {
    background: #020617;
    color: #e2e8f0;
}
</style>

<?php include "includes/footer.php"; ?>