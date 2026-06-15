<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// SECRET TOKEN
$valid_token = "MY_SECRET_TOKEN_123";

// GET HEADERS
$headers = getallheaders();

// CHECK AUTHORIZATION
if (!isset($headers['Authorization'])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Authorization token missing"
    ]);

    exit;
}

// TOKEN
$token = trim($headers['Authorization']);

// VALIDATE TOKEN
if ($token !== $valid_token) {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Invalid token"
    ]);

    exit;
}
?>
