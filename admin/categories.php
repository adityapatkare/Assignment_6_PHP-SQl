<?php
session_start();
include "../db.php";

// Show DB errors instead of failing silently
$conn->set_charset("utf8mb4");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$uploadDir = __DIR__ . "/../uploads/categories/";
$uploadWebPath = "../uploads/categories/"; // used in <img src>

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$edit_data = null;

// LOAD EDIT DATA
if (isset($_GET['edit'])) {

    $edit_id = (int) $_GET['edit'];

    $edit_query = $conn->query("SELECT * FROM categories WHERE id = $edit_id");

    if ($edit_query && $edit_query->num_rows > 0) {
        $edit_data = $edit_query->fetch_assoc();
    }
}

// ADD
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $image = "";

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $filename = uniqid("cat_") . "." . $ext;
            $target = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $filename;
            } else {
                $_SESSION['cat_error'] = "Image upload failed — check folder permissions on uploads/categories.";
            }
        } else {
            $_SESSION['cat_error'] = "Invalid image type. Use jpg, jpeg, png, or webp.";
        }
    }

    if ($name !== "") {
        try {
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $image);
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Most common cause: the `image` column doesn't exist yet
            die("Database error: " . $e->getMessage() .
                "<br>Run this once in phpMyAdmin: ALTER TABLE categories ADD image VARCHAR(255) DEFAULT NULL;");
        }
    }

    header("Location: categories.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);

    if ($name === "") {
        $_SESSION['cat_error'] = "Category name cannot be empty.";
        header("Location: categories.php?edit=$id");
        exit;
    }

    $image_sql = "";
    $new_image = null;

    // NEW IMAGE UPLOADED?
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            $filename = uniqid("cat_") . "." . $ext;
            $target = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {

                $new_image = $filename;

                // delete old image file if it exists
                $old_res = $conn->query("SELECT image FROM categories WHERE id=$id");
                if ($old_res && $old_row = $old_res->fetch_assoc()) {
                    if (!empty($old_row['image'])) {
                        $old_path = $uploadDir . $old_row['image'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                }

            } else {
                $_SESSION['cat_error'] = "Image upload failed — check folder permissions on uploads/categories.";
            }
        } else {
            $_SESSION['cat_error'] = "Invalid image type. Use jpg, jpeg, png, or webp.";
        }
    }

    try {
        if ($new_image !== null) {
            $stmt = $conn->prepare("UPDATE categories SET name = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $new_image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
        }
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        die("Database error: " . $e->getMessage());
    }

    header("Location: categories.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $res = $conn->query("SELECT image FROM categories WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['image'])) {
            $path = $uploadDir . $row['image'];
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    $conn->query("DELETE FROM categories WHERE id=$id");

    header("Location: categories.php");
    exit;
}

include "includes/header.php";

// FETCH
$categories = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<h2 class="mb-4 fw-bold">Manage Categories</h2>

<?php if (isset($_SESSION['cat_error'])) { ?>
    <div class="alert alert-warning">
        <?php echo htmlspecialchars($_SESSION['cat_error']); unset($_SESSION['cat_error']); ?>
    </div>
<?php } ?>

<!-- ADD / EDIT CATEGORY -->
<div class="card p-4 mb-4 shadow-sm">
    <h5 class="mb-3">
        <?php echo $edit_data ? "Edit Category" : "Add New Category"; ?>
    </h5>

    <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 flex-wrap align-items-center">

        <?php if ($edit_data) { ?>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <?php } ?>

        <input type="text" name="name"
               class="form-control"
               style="max-width: 250px;"
               placeholder="Enter category name"
               value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>"
               required>

        <input type="file" name="image"
               class="form-control"
               style="max-width: 250px;"
               accept="image/*">

        <?php if ($edit_data && !empty($edit_data['image']) && file_exists($uploadDir . $edit_data['image'])) { ?>
            <img src="<?php echo $uploadWebPath . htmlspecialchars($edit_data['image']); ?>"
                 alt="current image"
                 style="width:45px;height:45px;object-fit:cover;border-radius:6px;">
        <?php } ?>

        <?php if ($edit_data) { ?>

            <button type="submit" name="update" class="btn btn-dark">
                Update
            </button>

            <a href="categories.php" class="btn btn-secondary">
                Cancel
            </a>

        <?php } else { ?>

            <button type="submit" name="add" class="btn btn-dark">
                Add
            </button>

        <?php } ?>

    </form>
</div>

<!-- CATEGORY LIST -->
<div class="row">
<?php while ($row = $categories->fetch_assoc()) { ?>
    <div class="col-md-3 mb-3">
        <div class="card p-3 shadow-sm category-card">

            <?php if (!empty($row['image']) && file_exists($uploadDir . $row['image'])) { ?>
                <img src="<?php echo $uploadWebPath . htmlspecialchars($row['image']); ?>"
                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                     class="category-thumb mb-2">
            <?php } else { ?>
                <div class="category-thumb-placeholder mb-2">
                    <i class="fa fa-image"></i>
                </div>
            <?php } ?>

            <div class="d-flex justify-content-between align-items-center">
                <strong><?php echo htmlspecialchars($row['name']); ?></strong>

                <div class="d-flex gap-1">

                    <a href="categories.php?edit=<?php echo $row['id']; ?>"
                       class="btn btn-sm btn-primary">
                       <i class="fa fa-pen"></i>
                    </a>

                    <a href="categories.php?delete=<?php echo $row['id']; ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this category?')">
                       <i class="fa fa-trash"></i>
                    </a>

                </div>

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
.category-thumb {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
}
.category-thumb-placeholder {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 28px;
}
.dark strong {
    color: #e2e8f0;
}
</style>

<?php include "includes/footer.php"; ?>