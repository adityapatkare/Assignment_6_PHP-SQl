<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// DELETE USER
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM users WHERE id=" . $_GET['delete']);
}

// CHANGE ROLE
if (isset($_GET['role'])) {
    $id = $_GET['id'];
    $role = $_GET['role'];

    $conn->query("UPDATE users SET role='$role' WHERE id=$id");
}

$users = $conn->query("SELECT * FROM users");
?>

<h2 class="mb-4">Manage Users</h2>

<div class="card p-3">

<table class="table">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Actions</th>
</tr>

<?php while($u = $users->fetch_assoc()) { ?>
<tr>
    <td><?php echo $u['id']; ?></td>
    <td><?php echo $u['name']; ?></td>
    <td><?php echo $u['email']; ?></td>
    <td><?php echo $u['role']; ?></td>

    <td>
        <a href="?id=<?php echo $u['id']; ?>&role=admin" class="btn btn-sm btn-warning">Make Admin</a>
        <a href="?id=<?php echo $u['id']; ?>&role=user" class="btn btn-sm btn-info">Make User</a>
        <a href="?delete=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
    </td>
</tr>
<?php } ?>

</table>
</div>

<?php include "includes/footer.php"; ?>