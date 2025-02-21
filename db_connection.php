<?php
// db_connection.php

// Database connection parameters
$host = 'localhost';      // Your host, typically 'localhost'
$dbname = 'robins_hms';    // Your database name
$username = 'root';        // Your database username
$password = '';            // Your database password (set accordingly)

// Establishing a connection using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, an error message is shown
    die("Connection failed: " . $e->getMessage());
}
$conn = mysqli_connect($host, $username, $password, $dbname);

?>
