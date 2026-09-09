<?php

$host = "localhost";
$user = "u219431687_sample";
$password = "Media_vew9";
$database = "u219431687_sample";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>