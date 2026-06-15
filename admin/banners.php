<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// ADD BANNER
if (isset($_POST['add_banner'])) {

    $link = $_POST['link'];
    $status = isset($_POST['status']) ? 1 : 0;

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
    }

    $conn->query("INSERT INTO banners (image, link, status)
                  VALUES ('$image','$link','$status')");
}

// DELETE
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM banners WHERE id={$_GET['delete']}");
}

// FETCH
$result = $conn->query("SELECT * FROM banners");
?>

<h2 class="mb-4 fw-bold">Manage Banners</h2>

<!-- ADD FORM -->
<div class="card p-4 mb-4">
    <form method="POST" enctype="multipart/form-data">

        <input type="file" name="image" class="form-control mb-3" required>

        <input type="text" name="link" class="form-control mb-3"
               placeholder="Redirect link (e.g. search.php)">

        <div class="form-check mb-3">
            <input type="checkbox" name="status" class="form-check-input" checked>
            <label class="form-check-label">Active</label>
        </div>

        <button name="add_banner" class="btn btn-dark w-100">
            Add Banner
        </button>

    </form>
</div>

<!-- LIST -->
<div class="row">

<?php while($row = $result->fetch_assoc()) { ?>

    <div class="col-md-4 mb-4">
        <div class="card p-3">

            <img src="../uploads/<?php echo $row['image']; ?>" 
                 class="img-fluid mb-2 rounded">

            <p class="small">Link: <?php echo $row['link']; ?></p>

            <p>Status: 
                <?php echo $row['status'] ? 
                    "<span class='text-success'>Active</span>" : 
                    "<span class='text-danger'>Inactive</span>"; ?>
            </p>

            <a href="banners.php?delete=<?php echo $row['id']; ?>"
               class="btn btn-danger btn-sm"
               onclick="return confirm('Delete banner?')">
               Delete
            </a>

        </div>
    </div>

<?php } ?>

</div>

<?php include "includes/footer.php"; ?>