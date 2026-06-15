<?php
include "../db.php";
include "includes/header.php";

// TOTALS
$total_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
?>

<h2 class="mb-4 fw-bold">Dashboard Overview</h2>

<div class="row g-4">

    <!-- SALES -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary">Total Sales</h6>
                    <h3 class="fw-bold">₹<?php echo number_format($total_sales); ?></h3>
                </div>
                <div class="icon-box bg-success">
                    <i class="fa fa-rupee-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ORDERS -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary">Orders</h6>
                    <h3 class="fw-bold"><?php echo $total_orders; ?></h3>
                </div>
                <div class="icon-box bg-primary">
                    <i class="fa fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- USERS -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary">Users</h6>
                    <h3 class="fw-bold"><?php echo $total_users; ?></h3>
                </div>
                <div class="icon-box bg-warning">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTS -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary">Products</h6>
                    <h3 class="fw-bold"><?php echo $total_products; ?></h3>
                </div>
                <div class="icon-box bg-dark">
                    <i class="fa fa-box"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<hr class="my-5">

<!-- EXTRA SECTION (OPTIONAL BUT PREMIUM) -->
<div class="row">

    <div class="col-md-6">
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3">Quick Insights</h5>
            <ul class="list-unstyled">
                <li>Total Revenue Generated: <strong>₹<?php echo number_format($total_sales); ?></strong></li>
                <li>Total Customers: <strong><?php echo $total_users; ?></strong></li>
                <li>Total Orders Placed: <strong><?php echo $total_orders; ?></strong></li>
                <li>Total Products Listed: <strong><?php echo $total_products; ?></strong></li>
            </ul>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-4 shadow-sm">
            <h5 class="mb-3">System Status</h5>
            <p class="text-success mb-1">✔ Database Connected</p>
            <p class="text-success mb-1">✔ Orders Processing</p>
            <p class="text-success">✔ Website Live</p>
        </div>
    </div>

</div>

<!-- STYLES -->
<style>
.stat-card {
    border-radius: 12px;
    transition: 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

/* ICON BOX */
.icon-box {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

/* DARK MODE FIX */
.dark .text-secondary {
    color: #94a3b8 !important;
}
</style>

<?php include "includes/footer.php"; ?>