<?php
// Temporarily set error reporting to show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define the environment for error reporting (production or development)
define('ENVIRONMENT', 'production');

// Set error reporting based on the environment
if (ENVIRONMENT === 'production') {
    error_reporting(0);           // Suppress errors in production
    ini_set('display_errors', 0);  // Don't display errors in production
} else {
    error_reporting(E_ALL);       // Show all errors in development
    ini_set('display_errors', 1);  // Display errors in development
    header('Content-Type: text/plain');  // Optional: displays errors as plain text for debugging
}

// Database connection configuration (for local XAMPP development)
$dbConfig = [
    'DB_HOST' => 'localhost',          // Use 'localhost' for local XAMPP setup
    'DB_NAME' => 'pdb', // Replace with your local DB name
    'DB_USER' => 'root',               // Default XAMPP user is 'root'
    'DB_PASS' => '',                   // Default XAMPP password is empty
];

// Prepare the DSN string for PDO connection
$dsn = "mysql:host={$dbConfig['DB_HOST']};dbname={$dbConfig['DB_NAME']};charset=utf8mb4";

// PDO connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,            // Use real prepared statements
    PDO::ATTR_TIMEOUT => 3,                          // Set a timeout for the connection
];

// Prepare the DSN string for PDO connection
$dsn = "mysql:host={$dbConfig['DB_HOST']};dbname={$dbConfig['DB_NAME']};charset=utf8mb4";

// PDO connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,            // Use real prepared statements
    PDO::ATTR_TIMEOUT => 3,                          // Set a timeout for the connection
];

try {
    // Attempt to establish a PDO connection
    $pdo = new PDO($dsn, $dbConfig['DB_USER'], $dbConfig['DB_PASS'], $options);
    
    // Set time zone and charset for consistency
    $pdo->exec("SET time_zone='+00:00'");  // UTC time zone
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");  // Use utf8mb4 charset and collation

} catch (PDOException $e) {
    // Log database connection errors
    error_log("Database connection failed: " . $e->getMessage());
    
    // Send a 503 Service Unavailable status and display a message
    http_response_code(503);
    die("Service unavailable (DB error)");
}
?>