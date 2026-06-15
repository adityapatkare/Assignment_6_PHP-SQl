<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// ADD COUPON
if (isset($_POST['add_coupon'])) {
    $code = $_POST['code'];
    $type = $_POST['discount_type'];
    $value = $_POST['value'];
    $expiry = $_POST['expiry'];

    $conn->query("INSERT INTO coupons (code, discount_type, value, expiry)
                  VALUES ('$code','$type','$value','$expiry')");
}

// DELETE COUPON
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM coupons WHERE id=" . $_GET['delete']);
}

// FETCH COUPONS
$coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
?>

<h2 class="mb-4 fw-bold">Coupon Management</h2>

<!-- ADD COUPON -->
<div class="card p-4 mb-4 shadow-sm">
    <h5 class="mb-3">Create New Coupon</h5>

    <form method="POST">

        <div class="row">

            <div class="col-md-3 mb-3">
                <input type="text" name="code"
                       class="form-control"
                       placeholder="Coupon Code"
                       required>
            </div>

            <div class="col-md-3 mb-3">
                <select name="discount_type" class="form-control" required>
                    <option value="percent">Percent (%)</option>
                    <option value="fixed">Fixed (₹)</option>
                </select>
            </div>

            <div class="col-md-2 mb-3">
                <input type="number" name="value"
                       class="form-control"
                       placeholder="Value"
                       required>
            </div>

            <div class="col-md-3 mb-3">
                <input type="datetime-local" name="expiry"
                       class="form-control"
                       required>
            </div>

            <div class="col-md-1 mb-3">
                <button name="add_coupon" class="btn btn-dark w-100">
                    Add
                </button>
            </div>

        </div>

    </form>
</div>

<!-- COUPON LIST -->
<div class="card p-3 shadow-sm">

<table class="table table-hover align-middle">

<tr>
    <th>ID</th>
    <th>Code</th>
    <th>Type</th>
    <th>Value</th>
    <th>Expiry</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($c = $coupons->fetch_assoc()) { ?>

<tr>
    <td><?php echo $c['id']; ?></td>

    <td class="fw-bold"><?php echo $c['code']; ?></td>

    <td>
        <?php echo ($c['discount_type'] == 'percent') ? "Percent" : "Fixed"; ?>
    </td>

    <td>
        <?php
        if ($c['discount_type'] == 'percent') {
            echo $c['value'] . "%";
        } else {
            echo "₹" . $c['value'];
        }
        ?>
    </td>

    <td><?php echo $c['expiry']; ?></td>

    <!-- STATUS -->
    <td>
        <?php if (strtotime($c['expiry']) > time()) { ?>
            <span class="badge bg-success">Active</span>
        <?php } else { ?>
            <span class="badge bg-danger">Expired</span>
        <?php } ?>
    </td>

    <td>
        <a href="coupons.php?delete=<?php echo $c['id']; ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Delete this coupon?')">
           Delete
        </a>
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