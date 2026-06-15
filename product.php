<?php
session_start();
include "db.php";
include "includes/header.php";

// CHECK ID
if (!isset($_GET['id'])) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>No product selected</div></div>";
    include "includes/footer.php";
    exit;
}

$id = intval($_GET['id']);

// FETCH PRODUCT
$product_result = $conn->query("SELECT * FROM products WHERE id=$id");

if (!$product_result || $product_result->num_rows == 0) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Product not found</div></div>";
    include "includes/footer.php";
    exit;
}

$product = $product_result->fetch_assoc();

// FETCH VARIATIONS
$variations_result = $conn->query("SELECT * FROM product_variations WHERE product_id=$id");

// GROUP VARIATIONS
$sizes = [];
$colors = [];

if ($variations_result) {
    while ($v = $variations_result->fetch_assoc()) {
        if ($v['attribute'] == 'size') {
            $sizes[] = $v['value'];
        }
        if ($v['attribute'] == 'color') {
            $colors[] = $v['value'];
        }
    }
}
?>

<div class="container mt-4">

<div class="row">

    <!-- LEFT: IMAGE -->
    <div class="col-md-6">
        <div class="card p-3 text-center shadow-sm">
            <?php if (!empty($product['image'])) { ?>
                <img src="uploads/<?php echo $product['image']; ?>" 
                     class="img-fluid rounded" style="max-height:400px;">
            <?php } else { ?>
                <div style="height:300px; display:flex; align-items:center; justify-content:center;">
                    No Image
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- RIGHT: DETAILS -->
    <div class="col-md-6">

        <h2><?php echo $product['name']; ?></h2>
        <p class="text-secondary"><?php echo $product['description']; ?></p>

        <h3 class="mb-3">₹<?php echo $product['price']; ?></h3>

        <form method="POST" action="cart.php">

            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <!-- SIZE -->
            <?php if (!empty($sizes)) { ?>
                <label class="form-label">Size</label>
                <select name="size" class="form-control mb-3">
                    <?php foreach ($sizes as $s) { ?>
                        <option><?php echo $s; ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <!-- COLOR -->
            <?php if (!empty($colors)) { ?>
                <label class="form-label">Color</label>
                <select name="color" class="form-control mb-3">
                    <?php foreach ($colors as $c) { ?>
                        <option><?php echo $c; ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <button type="submit" name="add_to_cart" class="btn btn-dark w-100 mb-3">
    Add to Cart
</button>

        </form>

    </div>

</div>

<hr class="my-4">

<!-- REVIEWS SECTION -->
<div class="row">

    <!-- ADD REVIEW -->
    <div class="col-md-6">

        <h4>Add Review</h4>

        <?php if (isset($_SESSION['user'])) { ?>
            <form method="POST">

                <input type="number" name="rating" min="1" max="5"
                       class="form-control mb-2" placeholder="Rating (1-5)" required>

                <textarea name="comment"
                          class="form-control mb-2"
                          placeholder="Write your review" required></textarea>

                <button name="submit_review" class="btn btn-primary w-100">
                    Submit Review
                </button>

            </form>
        <?php } else { ?>
            <div class="alert alert-warning">
                Please login to add review
            </div>
        <?php } ?>

        <?php
        // SAVE REVIEW
        if (isset($_POST['submit_review']) && isset($_SESSION['user'])) {

            $user = $_SESSION['user'];
            $user_result = $conn->query("SELECT * FROM users WHERE name='$user'");

            if ($user_result && $user_result->num_rows > 0) {
                $u = $user_result->fetch_assoc();

                $rating = intval($_POST['rating']);
                $comment = $conn->real_escape_string($_POST['comment']);

                $conn->query("INSERT INTO reviews (product_id, user_id, rating, comment)
                              VALUES ($id, {$u['id']}, $rating, '$comment')");

                echo "<div class='alert alert-success mt-2'>Review added!</div>";
            }
        }
        ?>

    </div>

    <!-- SHOW REVIEWS -->
    <div class="col-md-6">

        <h4>Customer Reviews</h4>

        <?php
        $reviews = $conn->query("
            SELECT r.*, u.name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE product_id=$id
        ");

        if ($reviews && $reviews->num_rows > 0) {
            while ($r = $reviews->fetch_assoc()) {
        ?>

            <div class="card p-3 mb-2 shadow-sm">
                <strong><?php echo $r['name']; ?></strong>
                <div class="text-warning"><?php echo str_repeat("⭐", $r['rating']); ?></div>
                <p class="mb-0"><?php echo $r['comment']; ?></p>
            </div>

        <?php 
            }
        } else {
            echo "<p>No reviews yet</p>";
        }
        ?>

    </div>

</div>

</div>

<?php include "includes/footer.php"; ?>