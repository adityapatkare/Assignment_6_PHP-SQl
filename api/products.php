<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include "../db.php";
include "auth.php";

// FETCH PRODUCTS
$sql = "
SELECT 
    p.id,
    p.name,
    p.description,
    p.price,
    p.stock,
    p.image,
    c.name as category,
    v.name as vendor
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN vendors v ON p.vendor_id = v.id
ORDER BY p.id DESC
";

$result = $conn->query($sql);

$products = [];

while ($row = $result->fetch_assoc()) {

    // IMAGE URL
    $row['image'] = "http://localhost/ecommerce/uploads/" . $row['image'];

    $products[] = $row;
}

echo json_encode([
    "success" => true,
    "count" => count($products),
    "products" => $products
]);
?>
