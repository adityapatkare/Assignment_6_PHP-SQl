<?php
session_start();
include "db.php";
include "includes/header.php";

if (!isset($_SESSION['user'])) {
    echo "<div class='alert alert-warning'>Please login first</div>";
    include "includes/footer.php";
    exit;
}

$user_name = $_SESSION['user'];

// get user data
$user_result = $conn->query("SELECT * FROM users WHERE name='$user_name'");
$user = $user_result->fetch_assoc();
?>

<h2 class="mb-4">My Profile</h2>

<div class="row">

    <!-- USER INFO -->
    <div class="col-md-4">

        <div class="card p-4 mb-4 text-center">

            <img src="uploads/IMG_1603.JPG" 
                 style="width:80px; height:80px;    border-radius:50%; margin:auto;">

            <h4 class="mt-3"><?php echo $user['name']; ?></h4>
            <p class="text-muted"><?php echo $user['email']; ?></p>

            <a href="logout.php" class="btn btn-danger btn-sm mt-2">Logout</a>

        </div>

    </div>

    <!-- ORDERS -->
    <div class="col-md-8">

        <div class="card p-4">

            <h4 class="mb-3">My Orders</h4>

<?php
$order_result = $conn->query("SELECT * FROM orders WHERE user_id=".$user['id']." ORDER BY id DESC");

if ($order_result->num_rows > 0) {

    while ($order = $order_result->fetch_assoc()) {
?>

        <div class="border rounded p-3 mb-3">

            <div class="d-flex justify-content-between">
                <strong>Order #<?php echo $order['id']; ?></strong>
                <span class="text-success"><?php echo $order['status'] ?? 'Placed'; ?></span>
            </div>

            <p class="mb-1">Total: ₹<?php echo $order['total_amount']; ?></p>
            <p class="text-muted small">Date: <?php echo $order['created_at']; ?></p>

        </div>

<?php
    }

} else {
    echo "<div class='alert alert-info'>No orders yet</div>";
}
?>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>