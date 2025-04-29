<?php
// Set important security HTTP headers
header('X-Content-Type-Options: nosniff'); // Prevent MIME type sniffing
header('X-Frame-Options: SAMEORIGIN');     // Defend against clickjacking
header('Referrer-Policy: no-referrer');     // Do not leak referrer information
header('Permissions-Policy: geolocation=(), microphone=()'); // Block geolocation and microphone access

// Only send Strict-Transport-Security if using HTTPS
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// Basic Content Security Policy (CSP)
// Allow only self-hosted assets + trusted CDNs

// Secure session cookie settings
session_set_cookie_params([
    'lifetime' => 0,                    // Session cookie only (deleted on browser close)
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']), // Only send cookies over HTTPS
    'httponly' => true,                  // JavaScript cannot access cookies
    'samesite' => 'Strict',              // Prevent CSRF attacks
]);

session_start();

// OPTIONAL: Session timeout after inactivity (e.g., 30 minutes)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // Last request was more than 30 minutes ago
    session_unset();     // Unset session variables
    session_destroy();   // Destroy session data
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp
?>
