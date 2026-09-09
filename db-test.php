<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>Step 1: PHP is working</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

include "db.php";

echo "<h2 style='color:green;'>Step 2: Database connection successful!</h2>";

$result = $conn->query("SHOW TABLES");

if (!$result) {
    die("Could not retrieve tables: " . $conn->error);
}

echo "<h2>Step 3: Available Tables</h2>";

if ($result->num_rows == 0) {
    echo "<p style='color:red;'>No tables found in the database.</p>";
} else {

    while ($row = $result->fetch_array()) {
        echo "<div style='margin:5px 0;'>";
        echo "✓ " . htmlspecialchars($row[0]);
        echo "</div>";
    }
}

?>