<?php
require_once 'config/config.php';
echo "<h2>Database Connection Test</h2>";
echo "Connected to DB successfully.<br>";

$result = $conn->query("SHOW TABLES");
if ($result && $result->num_rows > 0) {
    echo "<strong>Tables in database:</strong><ul>";
    while ($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No tables found or query error.<br>";
}

// Check if any users exist
$userCount = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
echo "Number of users: " . $userCount . "<br>";

// Check if any cars exist
$carCount = $conn->query("SELECT COUNT(*) as total FROM cars")->fetch_assoc()['total'];
echo "Number of cars: " . $carCount . "<br>";