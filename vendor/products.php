<?php
session_start();

if (!isset($_SESSION['vendor_id'])) {
    header("Location: index.php");
    exit;
}

include "../db.php";
include "includes/header.php";

$vendor_user_id = $_SESSION['vendor_id'];

// GET VENDOR
$v = $conn->query("SELECT * FROM vendors WHERE user_id=$vendor_user_id")->fetch_assoc();

if (!$v) {
    echo "<div class='alert alert-danger'>Vendor not found</div>";
    exit;
}

$vendor_id = $v['id'];

// ADD PRODUCT
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];

    $conn->query("INSERT INTO products (name, price, vendor_id)
                  VALUES ('$name','$price',$vendor_id)");
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM products WHERE id=".$_GET['delete']." AND vendor_id=$vendor_id");
}

// FETCH PRODUCTS
$res = $conn->query("SELECT * FROM products WHERE vendor_id=$vendor_id");
?>

<h2 class="mb-4 fw-bold">My Products</h2>

<!-- ADD PRODUCT -->
<div class="card p-4 mb-4 shadow-sm">
    <h5 class="mb-3">Add Product</h5>

    <form method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <input type="text" name="name" class="form-control" placeholder="Product Name" required>
            </div>

            <div class="col-md-6 mb-3">
                <input type="number" name="price" class="form-control" placeholder="Price" required>
            </div>
        </div>

        <button name="add" class="btn btn-dark w-100">
            Add Product
        </button>
    </form>
</div>

<!-- PRODUCT LIST -->
<div class="row">

<?php if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) { ?>

    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm product-card">

            <div class="card-body d-flex flex-column">

                <h6 class="fw-bold"><?php echo $row['name']; ?></h6>

                <p class="text-secondary">₹<?php echo $row['price']; ?></p>

                <div class="mt-auto">
                    <a href="?delete=<?php echo $row['id']; ?>"
                       class="btn btn-danger btn-sm w-100"
                       onclick="return confirm('Delete this product?')">
                       Delete
                    </a>
                </div>

            </div>

        </div>
    </div>

<?php } } else { ?>
    <div class="col-12">
        <div class="alert alert-warning">No products found</div>
    </div>
<?php } ?>

</div>

<style>
.product-card {
    transition: 0.3s;
}
.product-card:hover {
    transform: translateY(-5px);
}

/* DARK MODE SUPPORT */
body.dark .card {
    background: #020617;
    color: #fff;
}
</style>