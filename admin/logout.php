<?php
// logout.php
// Strict session handling with proper output buffering

// Start output buffering
ob_start();

// Secure session configuration
session_start([
    'use_strict_mode' => true,
    'use_only_cookies' => 1,
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'cookie_samesite' => 'Strict',
    'use_trans_sid' => false
]);

// Regenerate session ID and destroy old one
session_regenerate_id(true);

// Unset all session variables
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite']
        ]
    );
}

// Destroy session
session_destroy();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Include logout screen
include 'logout.html';

// Flush output buffer
ob_end_flush();

// Exit immediately - the HTML will handle the redirect
exit();
?>