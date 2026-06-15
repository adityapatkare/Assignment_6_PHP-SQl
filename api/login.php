<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

session_start();
include "../db.php";

// ONLY POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        "success" => false,
        "message" => "POST method required"
    ]);

    exit;
}

// GET DATA
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// VALIDATE
if (empty($email) || empty($password)) {

    echo json_encode([
        "success" => false,
        "message" => "Email and password required"
    ]);

    exit;
}

// FIND USER
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);

    exit;
}

$user = $result->fetch_assoc();

// VERIFY PASSWORD
if (!password_verify($password, $user['password'])) {

    echo json_encode([
        "success" => false,
        "message" => "Wrong password"
    ]);

    exit;
}

// SESSION
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

// RESPONSE
echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "role" => $user['role']
    ],
    "token" => "MY_SECRET_TOKEN_123"
]);
?>
