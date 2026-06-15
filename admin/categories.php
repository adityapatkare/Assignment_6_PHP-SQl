<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// ADD
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $conn->query("INSERT INTO categories (name) VALUES ('$name')");
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id=$id");
}

// FETCH
$categories = $conn->query("SELECT * FROM categories");
?>

<h2 class="mb-4 fw-bold">Manage Categories</h2>

<!-- ADD CATEGORY -->
<div class="card p-4 mb-4 shadow-sm">
    <h5 class="mb-3">Add New Category</h5>

    <form method="POST" class="d-flex gap-2">

        <input type="text" name="name"
               class="form-control"
               placeholder="Enter category name"
               required>

        <button name="add" class="btn btn-dark">
            Add
        </button>

    </form>
</div>

<!-- CATEGORY LIST -->
<div class="row">

<?php while ($row = $categories->fetch_assoc()) { ?>

    <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm category-card">

            <div class="d-flex justify-content-between align-items-center">

                <strong><?php echo $row['name']; ?></strong>

                <a href="categories.php?delete=<?php echo $row['id']; ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete this category?')">
                   <i class="fa fa-trash"></i>
                </a>

            </div>

        </div>
    </div>

<?php } ?>

</div>

<style>
.category-card {
    border-radius: 10px;
    transition: 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

/* DARK MODE FIX */
.dark strong {
    color: #e2e8f0;
}
</style>

<?php include "includes/footer.php"; ?>