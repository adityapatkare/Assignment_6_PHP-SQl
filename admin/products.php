<?php
session_start();
include "../db.php";
include "includes/header.php";

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$message = "";
$edit_data = null;

/* =========================
   CREATE UPLOAD FOLDER
========================= */
if (!file_exists("../uploads")) {
    mkdir("../uploads", 0777, true);
}

/* =========================
   EDIT PRODUCT
========================= */
if (isset($_GET['edit'])) {

    $edit_id = (int) $_GET['edit'];

    $edit_query = $conn->query("
        SELECT * FROM products 
        WHERE id = $edit_id
    ");

    if ($edit_query && $edit_query->num_rows > 0) {
        $edit_data = $edit_query->fetch_assoc();
    }
}

/* =========================
   DELETE PRODUCT
========================= */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    // DELETE VARIATIONS
    $conn->query("
        DELETE FROM product_variations
        WHERE product_id = $id
    ");

    // DELETE PRODUCT
    $conn->query("
        DELETE FROM products
        WHERE id = $id
    ");

    $message = "
    <div class='alert alert-danger'>
        Product deleted successfully
    </div>";
}

/* =========================
   ADD PRODUCT
========================= */
if (isset($_POST['add_product'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category = (int) $_POST['category'];

    $vendor_id = 1;

    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $image = "";

    /* IMAGE UPLOAD */
    if (!empty($_FILES['image']['name'])) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        $file_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            $image = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);

            $destination = "../uploads/" . $image;

            if (move_uploaded_file($tmp_name, $destination)) {

                $message = "
                <div class='alert alert-success'>
                    Image uploaded successfully
                </div>";

            } else {

                $message = "
                <div class='alert alert-danger'>
                    Failed to upload image
                </div>";
            }

        } else {

            $message = "
            <div class='alert alert-danger'>
                Only JPG, JPEG, PNG & WEBP files allowed
            </div>";
        }
    }

    /* INSERT PRODUCT */
    $insert = $conn->query("
        INSERT INTO products
        (
            name,
            description,
            price,
            stock,
            image,
            category_id,
            vendor_id,
            is_featured
        )
        VALUES
        (
            '$name',
            '$description',
            '$price',
            '$stock',
            '$image',
            '$category',
            '$vendor_id',
            '$is_featured'
        )
    ");

    if ($insert) {

        $product_id = $conn->insert_id;

        /* SAVE SIZES */
        if (!empty($_POST['sizes'])) {

            $sizes = explode(",", $_POST['sizes']);

            foreach ($sizes as $size) {

                $size = trim($size);

                if (!empty($size)) {

                    $conn->query("
                        INSERT INTO product_variations
                        (
                            product_id,
                            attribute,
                            value,
                            stock
                        )
                        VALUES
                        (
                            '$product_id',
                            'size',
                            '$size',
                            '10'
                        )
                    ");
                }
            }
        }

        /* SAVE COLORS */
        if (!empty($_POST['colors'])) {

            $colors = explode(",", $_POST['colors']);

            foreach ($colors as $color) {

                $color = trim($color);

                if (!empty($color)) {

                    $conn->query("
                        INSERT INTO product_variations
                        (
                            product_id,
                            attribute,
                            value,
                            stock
                        )
                        VALUES
                        (
                            '$product_id',
                            'color',
                            '$color',
                            '10'
                        )
                    ");
                }
            }
        }

        $message .= "
        <div class='alert alert-success'>
            Product added successfully
        </div>";
    }
}

/* =========================
   UPDATE PRODUCT
========================= */
if (isset($_POST['update_product'])) {

    $id = (int) $_POST['id'];

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category = (int) $_POST['category'];

    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $query = "
        UPDATE products SET
        name='$name',
        description='$description',
        price='$price',
        stock='$stock',
        category_id='$category',
        is_featured='$is_featured'
    ";

    /* UPDATE IMAGE */
    if (!empty($_FILES['image']['name'])) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        $file_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            $new_image = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);

            move_uploaded_file($tmp_name, "../uploads/" . $new_image);

            $query .= ", image='$new_image'";
        }
    }

    $query .= " WHERE id='$id'";

    $conn->query($query);

    /* DELETE OLD VARIATIONS */
    $conn->query("
        DELETE FROM product_variations
        WHERE product_id = $id
    ");

    /* UPDATE SIZES */
    if (!empty($_POST['sizes'])) {

        $sizes = explode(",", $_POST['sizes']);

        foreach ($sizes as $size) {

            $size = trim($size);

            if (!empty($size)) {

                $conn->query("
                    INSERT INTO product_variations
                    (
                        product_id,
                        attribute,
                        value,
                        stock
                    )
                    VALUES
                    (
                        '$id',
                        'size',
                        '$size',
                        '10'
                    )
                ");
            }
        }
    }

    /* UPDATE COLORS */
    if (!empty($_POST['colors'])) {

        $colors = explode(",", $_POST['colors']);

        foreach ($colors as $color) {

            $color = trim($color);

            if (!empty($color)) {

                $conn->query("
                    INSERT INTO product_variations
                    (
                        product_id,
                        attribute,
                        value,
                        stock
                    )
                    VALUES
                    (
                        '$id',
                        'color',
                        '$color',
                        '10'
                    )
                ");
            }
        }
    }

    $message = "
    <div class='alert alert-success'>
        Product updated successfully
    </div>";
}

/* =========================
   FETCH DATA
========================= */

$categories = $conn->query("
    SELECT * FROM categories
");

$products = $conn->query("
    SELECT * FROM products
    ORDER BY id DESC
");

?>

<div class="container-fluid">

    <h2 class="fw-bold mb-4">
        Manage Products
    </h2>

    <?php echo $message; ?>

    <!-- ADD / EDIT FORM -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <h5 class="mb-4">
                <?php echo $edit_data ? "Edit Product" : "Add Product"; ?>
            </h5>

            <form method="POST" enctype="multipart/form-data">

                <input type="hidden"
                       name="id"
                       value="<?php echo $edit_data['id'] ?? ''; ?>">

                <div class="row">

                    <!-- PRODUCT NAME -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Product Name
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="<?php echo $edit_data['name'] ?? ''; ?>"
                               required>
                    </div>

                    <!-- PRICE -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Price
                        </label>

                        <input type="number"
                               step="0.01"
                               name="price"
                               class="form-control"
                               value="<?php echo $edit_data['price'] ?? ''; ?>"
                               required>
                    </div>

                    <!-- STOCK -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Stock
                        </label>

                        <input type="number"
                               name="stock"
                               class="form-control"
                               value="<?php echo $edit_data['stock'] ?? ''; ?>"
                               required>
                    </div>

                    <!-- CATEGORY -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Category
                        </label>

                        <select name="category"
                                class="form-select">

                            <?php while($cat = $categories->fetch_assoc()) { ?>

                                <option value="<?php echo $cat['id']; ?>"

                                    <?php
                                    if (($edit_data['category_id'] ?? '') == $cat['id']) {
                                        echo "selected";
                                    }
                                    ?>

                                >
                                    <?php echo $cat['name']; ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <!-- FEATURED -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">

                        <input type="checkbox"
                               name="is_featured"
                               value="1"
                               style="width:20px;height:20px;"
                               <?php
                               if (($edit_data['is_featured'] ?? 0) == 1) {
                                   echo "checked";
                               }
                               ?>>

                        <label class="ms-2 fw-semibold">
                            Featured Product
                        </label>

                    </div>

                    <!-- DESCRIPTION -->
                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="form-control"><?php echo $edit_data['description'] ?? ''; ?></textarea>

                    </div>

                    <!-- SIZES -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Sizes
                        </label>

                        <input type="text"
                               name="sizes"
                               class="form-control"
                               placeholder="S,M,L,XL">

                        <small class="text-muted">
                            Example: S,M,L,XL
                        </small>

                    </div>

                    <!-- COLORS -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Colors
                        </label>

                        <input type="text"
                               name="colors"
                               class="form-control"
                               placeholder="Black,White,Blue">

                        <small class="text-muted">
                            Example: Black,White,Blue
                        </small>

                    </div>

                    <!-- IMAGE -->
                    <div class="col-md-12 mb-4">

                        <label class="form-label">
                            Product Image
                        </label>

                        <input type="file"
                               name="image"
                               class="form-control"
                               accept=".jpg,.jpeg,.png,.webp">

                    </div>

                </div>

                <button type="submit"
                        name="<?php echo $edit_data ? 'update_product' : 'add_product'; ?>"
                        class="btn btn-dark w-100 py-2">

                    <?php echo $edit_data ? "Update Product" : "Add Product"; ?>

                </button>

            </form>

        </div>
    </div>

    <!-- PRODUCT LIST -->
    <div class="row">

        <?php while ($row = $products->fetch_assoc()) { ?>

            <div class="col-md-3 mb-4">

                <div class="card border-0 shadow-sm h-100 product-card">

                    <?php if (!empty($row['image'])) { ?>

                        <img src="../uploads/<?php echo $row['image']; ?>"
                             class="card-img-top"
                             style="height:220px; object-fit:contain;">

                    <?php } else { ?>

                        <div class="d-flex align-items-center justify-content-center"
                             style="height:220px;">
                            No Image
                        </div>

                    <?php } ?>

                    <div class="card-body d-flex flex-column">

                        <?php if ($row['is_featured'] == 1) { ?>

                            <span class="badge bg-success mb-2">
                                Featured
                            </span>

                        <?php } ?>

                        <h5 class="fw-bold">
                            <?php echo $row['name']; ?>
                        </h5>

                        <p class="small text-muted mb-1">
                            ₹<?php echo $row['price']; ?>
                        </p>

                        <p class="small mb-3">
                            Stock: <?php echo $row['stock']; ?>
                        </p>

                        <div class="mt-auto d-flex gap-2">

                            <a href="products.php?edit=<?php echo $row['id']; ?>"
                               class="btn btn-primary w-50">
                                Edit
                            </a>

                            <a href="products.php?delete=<?php echo $row['id']; ?>"
                               class="btn btn-danger w-50"
                               onclick="return confirm('Delete this product?')">
                                Delete
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<style>

.product-card{
    transition:0.3s;
}

.product-card:hover{
    transform:translateY(-5px);
}

</style>

<?php include "includes/footer.php"; ?>