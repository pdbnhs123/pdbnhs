<?php
// db.php - Improved version with error handling
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'pdb';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log("Database error: " . $e->getMessage());
    die("System unavailable. Please try again later.");
}
