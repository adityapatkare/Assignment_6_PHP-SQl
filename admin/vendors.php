<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// ADD VENDOR
if (isset($_POST['add_vendor'])) {
    $name = $_POST['name'];
    $user_id = $_POST['user_id'];

    $conn->query("INSERT INTO vendors (user_id, name, status)
                  VALUES ('$user_id','$name','active')");
}

// DELETE
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM vendors WHERE id=" . $_GET['delete']);
}

// TOGGLE STATUS
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $vendor = $conn->query("SELECT status FROM vendors WHERE id=$id")->fetch_assoc();

    $new_status = ($vendor['status'] == 'active') ? 'inactive' : 'active';

    $conn->query("UPDATE vendors SET status='$new_status' WHERE id=$id");
}

// FETCH USERS (for dropdown)
$users = $conn->query("SELECT * FROM users");

// FETCH VENDORS
$result = $conn->query("SELECT v.*, u.name as user_name 
                        FROM vendors v 
                        JOIN users u ON v.user_id = u.id");
?>

<h2 class="mb-4 fw-bold">Manage Vendors</h2>

<!-- ADD VENDOR -->
<div class="card p-4 mb-4 shadow-sm">
    <h5 class="mb-3">Add New Vendor</h5>

    <form method="POST">

        <div class="row">

            <div class="col-md-6 mb-3">
                <input type="text" name="name"
                       class="form-control"
                       placeholder="Vendor Name" required>
            </div>

            <div class="col-md-6 mb-3">
                <select name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php while($u = $users->fetch_assoc()) { ?>
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo $u['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

        </div>

        <button name="add_vendor" class="btn btn-dark w-100">
            Add Vendor
        </button>

    </form>
</div>

<!-- VENDOR LIST -->
<div class="card p-3 shadow-sm">
<table class="table table-hover align-middle">

<tr>
    <th>ID</th>
    <th>Vendor</th>
    <th>User</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>
    <td><?php echo $row['id']; ?></td>
    <td class="fw-bold"><?php echo $row['name']; ?></td>
    <td><?php echo $row['user_name']; ?></td>

    <td>
        <?php if ($row['status'] == 'active') { ?>
            <span class="badge bg-success">Active</span>
        <?php } else { ?>
            <span class="badge bg-secondary">Inactive</span>
        <?php } ?>
    </td>

    <td class="d-flex gap-2">

        <a href="vendors.php?toggle=<?php echo $row['id']; ?>"
           class="btn btn-sm btn-warning">
            Toggle
        </a>

        <a href="vendors.php?delete=<?php echo $row['id']; ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Delete this vendor?')">
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