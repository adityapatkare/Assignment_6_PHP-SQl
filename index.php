<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "db.php";
include "includes/header.php";
?>

<div class="container mt-4">

<?php
$banner = $conn->query("SELECT * FROM banners WHERE status=1 ORDER BY id DESC LIMIT 1")->fetch_assoc();
$bannerImage = !empty($banner['image']) ? 'uploads/' . htmlspecialchars($banner['image']) : 'uploads/banner.jpg';
?>

<div class="hero-section mb-5"
     onclick="window.location='<?php echo htmlspecialchars($banner['link'] ?? '#'); ?>'"
     style="cursor:pointer;
            height:350px;
            border-radius:10px;
            background:url('<?php echo $bannerImage; ?>') center/cover no-repeat;">
</div>

<!-- SEARCH + CATEGORIES INLINE -->
<div class="row align-items-center mb-5">

    <!-- SEARCH -->
    <div class="col-md-6 mb-2">
        <form action="search.php" method="GET" class="d-flex">

            <input type="text" name="q" 
                   class="form-control me-2 shadow-sm"
                   placeholder="Search for products...">

            <button class="btn btn-dark">Search</button>

        </form>
    </div>

    <!-- CATEGORIES -->
    <div class="col-md-6">
        <div class="d-flex flex-wrap justify-content-md-end gap-2">

            <?php
            $cats = $conn->query("SELECT * FROM categories LIMIT 6");

            while ($c = $cats->fetch_assoc()) {
            ?>
                <button class="btn btn-outline-light btn-sm"
                        onclick="window.location='search.php?category=<?php echo $c['id']; ?>'">
                    <?php echo htmlspecialchars($c['name']); ?>
                </button>
            <?php } ?>

        </div>
    </div>

</div>

<!-- PRODUCTS -->
<h3 class="mb-4">Featured Products</h3>

<div class="row">

<?php
$sql = "SELECT * FROM products WHERE is_featured=1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
?>

    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm product-card"
             style="cursor:pointer;"
             onclick="window.location='product.php?id=<?php echo $row['id']; ?>'">

            <?php if (!empty($row['image'])) { ?>
                <img src="./uploads/<?php echo htmlspecialchars($row['image']); ?>"
                     class="card-img-top" 
                     style="height:200px; object-fit:contain;">
            <?php } else { ?>
                <div style="height:200px; display:flex; align-items:center; justify-content:center;">
                    No Image
                </div>
            <?php } ?>

            <div class="card-body d-flex flex-column">

                <h5><?php echo htmlspecialchars($row['name']); ?></h5>

                <p class="text-muted small">
                    <?php echo htmlspecialchars(substr($row['description'], 0, 60)); ?>...
                </p>

                <h6 class="mt-auto fw-bold">₹<?php echo htmlspecialchars($row['price']); ?></h6>

                <form method="POST" action="cart.php"
                      onclick="event.stopPropagation();">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="add_to_cart" 
                            class="btn btn-dark w-100 mt-2">
                        Add to Cart
                    </button>
                </form>

            </div>

        </div>
    </div>

<?php
    }
} else {
    echo "<div class='alert alert-warning'>No products found</div>";
}
?>

</div>

</div>

<!-- STYLES -->
<style>

/* HERO IMAGE */
.hero-section {
    transition: 0.3s;
}

/* PRODUCT HOVER */
.product-card {
    transition: 0.3s;
}
.product-card:hover {
    transform: translateY(-5px);
}

/* CATEGORY BUTTON DARK THEME FIX */
.btn-outline-light {
    border-color: #000;
    color: #000;
}
.btn-outline-light:hover {
    background-color: #fff;
    color: #000;
}

</style>

<?php include "includes/footer.php"; ?>
