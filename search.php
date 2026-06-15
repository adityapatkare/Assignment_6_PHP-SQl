<?php
include "db.php";
include "includes/header.php";

$q = $_GET['q'] ?? "";
$category = $_GET['category'] ?? "";
$min = $_GET['min'] ?? "";
$max = $_GET['max'] ?? "";

// BUILD QUERY
$sql = "SELECT * FROM products WHERE 1";

if (!empty($q)) {
    $sql .= " AND name LIKE '%$q%'";
}

if (!empty($category)) {
    $sql .= " AND category_id = '$category'";
}

if (!empty($min)) {
    $sql .= " AND price >= $min";
}

if (!empty($max)) {
    $sql .= " AND price <= $max";
}

$result = $conn->query($sql);

// FETCH CATEGORIES
$cats = $conn->query("SELECT * FROM categories");
?>

<div class="container mt-4">

<h2 class="mb-4">Search Products</h2>

<div class="row">

    <!-- FILTERS -->
    <div class="col-md-3">
        <div class="card p-3 shadow-sm">

            <form method="GET">

                <input type="text" name="q" class="form-control mb-2"
                       placeholder="Search..." value="<?php echo $q; ?>">

                <select name="category" class="form-control mb-2">
                    <option value="">All Categories</option>
                    <?php while($c = $cats->fetch_assoc()) { ?>
                        <option value="<?php echo $c['id']; ?>"
                            <?php if($category == $c['id']) echo "selected"; ?>>
                            <?php echo $c['name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="number" name="min" class="form-control mb-2"
                       placeholder="Min Price" value="<?php echo $min; ?>">

                <input type="number" name="max" class="form-control mb-2"
                       placeholder="Max Price" value="<?php echo $max; ?>">

                <button class="btn btn-dark w-100">Apply Filters</button>

            </form>

        </div>
    </div>

    <!-- RESULTS -->
    <div class="col-md-9">

        <div class="row">

            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>

                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm"
                             style="cursor:pointer;"
                             onclick="window.location='product.php?id=<?php echo $row['id']; ?>'">

                            <?php if (!empty($row['image'])) { ?>
                                <img src="uploads/<?php echo $row['image']; ?>" 
                                     class="card-img-top" 
                                     style="height:200px; object-fit:cover;">
                            <?php } else { ?>
                                <div style="height:200px; display:flex; align-items:center; justify-content:center;">
                                    No Image
                                </div>
                            <?php } ?>

                            <div class="card-body d-flex flex-column">

                                <h5><?php echo $row['name']; ?></h5>

                                <p class="text-muted small">
                                    <?php echo substr($row['description'], 0, 60); ?>...
                                </p>

                                <h6 class="mt-auto">₹<?php echo $row['price']; ?></h6>

                                <!-- BUTTON -->
                                <a href="product.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-outline-dark btn-sm mt-2"
                                   onclick="event.stopPropagation();">
                                   View Product
                                </a>

                            </div>

                        </div>
                    </div>

            <?php } } else { ?>

                <div class="col-12">
                    <div class="alert alert-warning">No products found</div>
                </div>

            <?php } ?>

        </div>

    </div>

</div>

</div>

<?php include "includes/footer.php"; ?>