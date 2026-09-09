<?php
include "db.php";
include "includes/header.php";

/* =========================
   GET FILTER VALUES
========================= */

$q        = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$min      = $_GET['min'] ?? '';
$max      = $_GET['max'] ?? '';
$sort     = $_GET['sort'] ?? 'recommended';

/* =========================
   ESCAPE FUNCTION
========================= */

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* =========================
   BUILD PRODUCT QUERY
========================= */

$sql = "SELECT * FROM products WHERE 1=1";

/* Search */
if ($q !== '') {
    $safe_q = $conn->real_escape_string($q);
    $sql .= " AND (
        name LIKE '%$safe_q%'
        OR description LIKE '%$safe_q%'
    )";
}

/* Category */
if ($category !== '' && is_numeric($category)) {
    $category_id = intval($category);
    $sql .= " AND category_id = $category_id";
}

/* Minimum price */
if ($min !== '' && is_numeric($min)) {
    $min_price = floatval($min);
    $sql .= " AND price >= $min_price";
}

/* Maximum price */
if ($max !== '' && is_numeric($max)) {
    $max_price = floatval($max);
    $sql .= " AND price <= $max_price";
}

/* =========================
   SORTING
========================= */

switch ($sort) {

    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;

    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;

    case 'newest':
        $sql .= " ORDER BY id DESC";
        break;

    case 'name':
        $sql .= " ORDER BY name ASC";
        break;

    default:
        $sql .= " ORDER BY id DESC";
        break;
}

$result = $conn->query($sql);

/* =========================
   CATEGORIES
========================= */

$cats = $conn->query("
    SELECT * 
    FROM categories 
    ORDER BY name ASC
");

/* =========================
   PRODUCT COUNT
========================= */

$product_count = $result ? $result->num_rows : 0;
?>

<style>

/* =========================================
   SEARCH PAGE
========================================= */

.search-page {
    background: #fff;
    min-height: 100vh;
    padding: 25px 30px 60px;
}

/* =========================================
   BREADCRUMB
========================================= */

.breadcrumb-area {
    margin-bottom: 20px;
    font-size: 15px;
}

.breadcrumb-area a {
    color: #17233c;
    text-decoration: none;
}

.breadcrumb-area span {
    color: #777;
}

.breadcrumb-title {
    font-weight: 700;
    color: #17233c;
}

/* =========================================
   TOP TITLE
========================================= */

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-heading h2 {
    font-size: 22px;
    font-weight: 700;
    color: #17233c;
    margin: 0;
}

.product-count {
    color: #777;
    font-weight: 400;
}

/* =========================================
   MAIN LAYOUT
========================================= */

.shop-layout {
    display: flex;
    align-items: flex-start;
}

/* =========================================
   SIDEBAR
========================================= */

.filters-sidebar {
    width: 270px;
    flex-shrink: 0;
    border-right: 1px solid #eee;
    padding-right: 20px;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 18px;
}

.filter-header h4 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: #17233c;
}

.clear-filters {
    color: #ff3f6c;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.clear-filters:hover {
    color: #e62955;
}

/* FILTER BOX */

.filter-box {
    border-top: 1px solid #eee;
    padding: 20px 0;
}

.filter-box h5 {
    font-size: 15px;
    font-weight: 700;
    color: #17233c;
    margin-bottom: 16px;
}

/* RADIO */

.filter-option {
    display: flex;
    align-items: center;
    margin-bottom: 13px;
    font-size: 14px;
    color: #17233c;
    cursor: pointer;
}

.filter-option input {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    accent-color: #ff3f6c;
}

/* CATEGORY */

.category-option {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    font-size: 14px;
    color: #17233c;
}

.category-option input {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    accent-color: #ff3f6c;
}

.category-option a {
    color: #17233c;
    text-decoration: none;
}

.category-option a:hover {
    color: #ff3f6c;
}

/* PRICE */

.price-inputs {
    display: flex;
    gap: 8px;
}

.price-inputs input {
    width: 50%;
    height: 38px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    font-size: 13px;
}

.apply-price {
    width: 100%;
    margin-top: 10px;
    border: 1px solid #ff3f6c;
    background: white;
    color: #ff3f6c;
    padding: 8px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 3px;
}

.apply-price:hover {
    background: #ff3f6c;
    color: white;
}

/* =========================================
   PRODUCTS AREA
========================================= */

.products-area {
    flex: 1;
    padding-left: 25px;
}

/* =========================================
   TOOLBAR
========================================= */

.shop-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.filter-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-chip {
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 8px 14px;
    color: #17233c;
    font-size: 13px;
    background: white;
}

.filter-chip:hover {
    border-color: #ff3f6c;
    color: #ff3f6c;
}

.sort-box {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sort-box label {
    font-size: 13px;
    color: #777;
}

.sort-box select {
    min-width: 190px;
    height: 42px;
    border: 1px solid #ddd;
    background: white;
    padding: 0 12px;
    font-size: 14px;
    color: #17233c;
    outline: none;
}

/* =========================================
   PRODUCT GRID
========================================= */

.product-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 28px 20px;
}

/* =========================================
   PRODUCT CARD
========================================= */

.product-card {
    position: relative;
    background: white;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
}

.product-card:hover {
    transform: translateY(-4px);
}

/* IMAGE */

.product-image-wrapper {
    position: relative;
    width: 100%;
    height: 330px;
    background: #f6f6f6;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
}

.product-card:hover .product-image {
    transform: scale(1.04);
}

.no-image {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 14px;
}

/* WISHLIST */

.wishlist-btn {
    position: absolute;
    right: 10px;
    bottom: 10px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #555;
    font-size: 16px;
    cursor: pointer;
    z-index: 5;
}

.wishlist-btn:hover {
    color: #ff3f6c;
}

/* PRODUCT DETAILS */

.product-details {
    padding: 12px 5px 0;
}

.product-name {
    font-size: 15px;
    font-weight: 700;
    color: #17233c;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.product-description {
    font-size: 13px;
    color: #777;
    margin-bottom: 7px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* PRICE */

.product-price {
    font-size: 15px;
    font-weight: 700;
    color: #17233c;
}

.original-price {
    font-size: 13px;
    color: #999;
    text-decoration: line-through;
    margin-left: 5px;
}

.discount {
    color: #ff6b35;
    font-size: 13px;
    margin-left: 4px;
}

/* VIEW BUTTON */

.view-product {
    margin-top: 10px;
    width: 100%;
    border: 1px solid #ddd;
    background: white;
    color: #17233c;
    padding: 8px;
    font-size: 13px;
    font-weight: 600;
    opacity: 0;
    transition: 0.2s ease;
}

.product-card:hover .view-product {
    opacity: 1;
}

.view-product:hover {
    border-color: #ff3f6c;
    color: #ff3f6c;
}

/* =========================================
   NO PRODUCTS
========================================= */

.no-products {
    width: 100%;
    padding: 80px 20px;
    text-align: center;
}

.no-products h3 {
    color: #17233c;
    margin-bottom: 10px;
}

.no-products p {
    color: #777;
}

/* =========================================
   MOBILE FILTER BUTTON
========================================= */

.mobile-filter-btn {
    display: none;
}

/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 1200px) {

    .product-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

}

@media (max-width: 900px) {

    .search-page {
        padding: 20px 15px 50px;
    }

    .filters-sidebar {
        width: 220px;
    }

    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}

@media (max-width: 700px) {

    .shop-layout {
        display: block;
    }

    .filters-sidebar {
        display: none;
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #eee;
        padding: 15px;
        margin-bottom: 20px;
    }

    .filters-sidebar.active {
        display: block;
    }

    .mobile-filter-btn {
        display: block;
        width: 100%;
        margin-bottom: 15px;
        background: white;
        border: 1px solid #ddd;
        padding: 10px;
        font-weight: 600;
    }

    .products-area {
        padding-left: 0;
    }

    .shop-toolbar {
        display: block;
    }

    .sort-box {
        margin-top: 12px;
        justify-content: space-between;
    }

    .sort-box select {
        flex: 1;
    }

    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 25px 12px;
    }

    .product-image-wrapper {
        height: 250px;
    }

    .view-product {
        opacity: 1;
    }

}

@media (max-width: 450px) {

    .page-heading h2 {
        font-size: 18px;
    }

    .product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .product-image-wrapper {
        height: 210px;
    }

    .product-name {
        font-size: 13px;
    }

    .product-description {
        font-size: 11px;
    }

    .product-price {
        font-size: 13px;
    }

}

</style>


<div class="search-page">

    <!-- =====================================
         BREADCRUMB
    ====================================== -->

    <div class="breadcrumb-area">

        <a href="index.php">Home</a>

        <span> / </span>

        <?php if ($q !== '') { ?>

            <span class="breadcrumb-title">
                Search: <?php echo e($q); ?>
            </span>

        <?php } elseif ($category !== '') { ?>

            <?php
            $category_name = "Category";

            $category_lookup = $conn->query(
                "SELECT name FROM categories WHERE id=" . intval($category)
            );

            if ($category_lookup && $category_lookup->num_rows > 0) {
                $category_row = $category_lookup->fetch_assoc();
                $category_name = $category_row['name'];
            }
            ?>

            <span class="breadcrumb-title">
                <?php echo e($category_name); ?>
            </span>

        <?php } else { ?>

            <span class="breadcrumb-title">
                All Products
            </span>

        <?php } ?>

    </div>


    <!-- =====================================
         PAGE HEADING
    ====================================== -->

    <div class="page-heading">

        <h2>

            <?php
            if ($q !== '') {
                echo "Search Results";
            } elseif ($category !== '') {
                echo e($category_name ?? "Products");
            } else {
                echo "All Products";
            }
            ?>

            <span class="product-count">
                - <?php echo $product_count; ?> items
            </span>

        </h2>

    </div>


    <!-- MOBILE FILTER -->
    <button
        class="mobile-filter-btn"
        onclick="toggleFilters()">

        Filters & Sort

    </button>


    <div class="shop-layout">


        <!-- =====================================
             FILTER SIDEBAR
        ====================================== -->

        <aside class="filters-sidebar" id="filtersSidebar">

            <div class="filter-header">

                <h4>FILTERS</h4>

                <a
                    href="search.php"
                    class="clear-filters">

                    CLEAR ALL

                </a>

            </div>


            <!-- GENDER -->

            <div class="filter-box">

                <h5>GENDER</h5>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="gender"
                        checked>

                    Men

                </label>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="gender">

                    Women

                </label>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="gender">

                    Boys

                </label>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="gender">

                    Girls

                </label>

            </div>


            <!-- CATEGORIES -->

            <div class="filter-box">

                <h5>CATEGORIES</h5>

                <?php

                if ($cats && $cats->num_rows > 0) {

                    while ($c = $cats->fetch_assoc()) {

                ?>

                    <div class="category-option">

                        <input
                            type="checkbox"
                            <?php
                            if (
                                $category !== '' &&
                                $category == $c['id']
                            ) {
                                echo "checked";
                            }
                            ?>
                            onchange="window.location='search.php?category=<?php echo intval($c['id']); ?>'">

                        <a
                            href="search.php?category=<?php echo intval($c['id']); ?>">

                            <?php echo e($c['name']); ?>

                        </a>

                    </div>

                <?php

                    }

                } else {

                ?>

                    <p class="text-muted small">
                        No categories available.
                    </p>

                <?php } ?>

            </div>


            <!-- PRICE -->

            <div class="filter-box">

                <h5>PRICE</h5>

                <form method="GET">

                    <?php if ($q !== '') { ?>

                        <input
                            type="hidden"
                            name="q"
                            value="<?php echo e($q); ?>">

                    <?php } ?>

                    <?php if ($category !== '') { ?>

                        <input
                            type="hidden"
                            name="category"
                            value="<?php echo e($category); ?>">

                    <?php } ?>

                    <div class="price-inputs">

                        <input
                            type="number"
                            name="min"
                            placeholder="Min"
                            value="<?php echo e($min); ?>">

                        <input
                            type="number"
                            name="max"
                            placeholder="Max"
                            value="<?php echo e($max); ?>">

                    </div>

                    <button
                        type="submit"
                        class="apply-price">

                        APPLY

                    </button>

                </form>

            </div>


            <!-- SORT OPTIONS -->

            <div class="filter-box">

                <h5>SORT</h5>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="sidebar_sort"
                        onclick="changeSort('recommended')">

                    Recommended

                </label>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="sidebar_sort"
                        onclick="changeSort('price_low')">

                    Price: Low to High

                </label>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="sidebar_sort"
                        onclick="changeSort('price_high')">

                    Price: High to Low

                </label>

                <label class="filter-option">

                    <input
                        type="radio"
                        name="sidebar_sort"
                        onclick="changeSort('newest')">

                    Newest First

                </label>

            </div>

        </aside>


        <!-- =====================================
             PRODUCTS
        ====================================== -->

        <main class="products-area">


            <!-- TOOLBAR -->

            <div class="shop-toolbar">

                <div class="filter-chips">

                    <?php if ($q !== '') { ?>

                        <span class="filter-chip">

                            Search: <?php echo e($q); ?>

                        </span>

                    <?php } ?>

                    <?php if ($category !== '') { ?>

                        <span class="filter-chip">

                            <?php echo e($category_name ?? 'Category'); ?>

                        </span>

                    <?php } ?>

                    <?php if ($min !== '') { ?>

                        <span class="filter-chip">

                            ₹<?php echo e($min); ?>+

                        </span>

                    <?php } ?>

                </div>


                <!-- SORT -->

                <form method="GET" class="sort-box">

                    <?php if ($q !== '') { ?>

                        <input
                            type="hidden"
                            name="q"
                            value="<?php echo e($q); ?>">

                    <?php } ?>

                    <?php if ($category !== '') { ?>

                        <input
                            type="hidden"
                            name="category"
                            value="<?php echo e($category); ?>">

                    <?php } ?>

                    <?php if ($min !== '') { ?>

                        <input
                            type="hidden"
                            name="min"
                            value="<?php echo e($min); ?>">

                    <?php } ?>

                    <?php if ($max !== '') { ?>

                        <input
                            type="hidden"
                            name="max"
                            value="<?php echo e($max); ?>">

                    <?php } ?>

                    <label>
                        Sort by:
                    </label>

                    <select
                        name="sort"
                        onchange="this.form.submit()">

                        <option
                            value="recommended"
                            <?php
                            if ($sort == 'recommended') {
                                echo 'selected';
                            }
                            ?>>

                            Recommended

                        </option>

                        <option
                            value="price_low"
                            <?php
                            if ($sort == 'price_low') {
                                echo 'selected';
                            }
                            ?>>

                            Price: Low to High

                        </option>

                        <option
                            value="price_high"
                            <?php
                            if ($sort == 'price_high') {
                                echo 'selected';
                            }
                            ?>>

                            Price: High to Low

                        </option>

                        <option
                            value="newest"
                            <?php
                            if ($sort == 'newest') {
                                echo 'selected';
                            }
                            ?>>

                            Newest

                        </option>

                        <option
                            value="name"
                            <?php
                            if ($sort == 'name') {
                                echo 'selected';
                            }
                            ?>>

                            Name

                        </option>

                    </select>

                </form>

            </div>


            <!-- PRODUCT GRID -->

            <?php if ($result && $result->num_rows > 0) { ?>

                <div class="product-grid">

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <?php

                        /*
                         * Calculate discount.
                         * If your database has an old_price
                         * column, it will use it.
                         */

                        $old_price = 0;

                        if (isset($row['old_price']) && $row['old_price'] > $row['price']) {
                            $old_price = $row['old_price'];
                        }

                        $discount = 0;

                        if ($old_price > 0) {

                            $discount = round(
                                (($old_price - $row['price']) / $old_price) * 100
                            );

                        }

                        ?>


                        <div
                            class="product-card"
                            onclick="window.location='product.php?id=<?php echo intval($row['id']); ?>'">


                            <!-- IMAGE -->

                            <div class="product-image-wrapper">

                                <?php if (!empty($row['image'])) { ?>

                                    <img
                                        src="uploads/<?php echo e($row['image']); ?>"
                                        class="product-image"
                                        alt="<?php echo e($row['name']); ?>">

                                <?php } else { ?>

                                    <div class="no-image">

                                        No Image

                                    </div>

                                <?php } ?>


                                <!-- WISHLIST -->

                                <button
                                    class="wishlist-btn"
                                    onclick="event.stopPropagation();">

                                    ♡

                                </button>

                            </div>


                            <!-- DETAILS -->

                            <div class="product-details">

                                <div class="product-name">

                                    <?php echo e($row['name']); ?>

                                </div>


                                <div class="product-description">

                                    <?php

                                    $description = strip_tags(
                                        $row['description'] ?? ''
                                    );

                                    echo e(
                                        strlen($description) > 45
                                        ? substr($description, 0, 45) . '...'
                                        : $description
                                    );

                                    ?>

                                </div>


                                <!-- PRICE -->

                                <div>

                                    <span class="product-price">

                                        ₹<?php echo number_format($row['price']); ?>

                                    </span>


                                    <?php if ($old_price > 0) { ?>

                                        <span class="original-price">

                                            ₹<?php echo number_format($old_price); ?>

                                        </span>

                                        <span class="discount">

                                            (<?php echo $discount; ?>% OFF)

                                        </span>

                                    <?php } ?>

                                </div>


                                <!-- VIEW PRODUCT -->

                                <button
                                    class="view-product"
                                    onclick="event.stopPropagation(); window.location='product.php?id=<?php echo intval($row['id']); ?>';">

                                    VIEW PRODUCT

                                </button>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            <?php } else { ?>


                <!-- NO PRODUCTS -->

                <div class="no-products">

                    <h3>
                        No products found
                    </h3>

                    <p>
                        Try changing your search or filters.
                    </p>

                    <a
                        href="search.php"
                        class="btn btn-dark">

                        View All Products

                    </a>

                </div>


            <?php } ?>


        </main>

    </div>

</div>


<script>

/* =========================================
   MOBILE FILTER
========================================= */

function toggleFilters()
{
    const sidebar = document.getElementById("filtersSidebar");

    sidebar.classList.toggle("active");
}


/* =========================================
   SORT FROM SIDEBAR
========================================= */

function changeSort(sort)
{
    const url = new URL(window.location.href);

    url.searchParams.set("sort", sort);

    window.location.href = url.toString();
}

</script>


<?php include "includes/footer.php"; ?>