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

$sql = "
SELECT 
    o.id,
    o.total_amount,
    o.status,
    o.created_at,
    u.name as customer
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
ORDER BY o.id DESC
";

$result = $conn->query($sql);

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    "success" => true,
    "count" => count($orders),
    "orders" => $orders
]);
?>
