<?php
// Session security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => ($_SERVER['HTTPS'] ?? 'off') === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=()');

if (($_SERVER['HTTPS'] ?? 'off') === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Content-Security-Policy header for added security
header("Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
    . "img-src 'self' data: https:; "
    . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
    . "connect-src 'self'; "
    . "frame-ancestors 'self';"
);

// Host whitelist
$allowed_hosts = [
    'pasodeblasnhs.iceiy.com',
    'www.pasodeblasnhs.iceiy.com',
    'm.pasodeblasnhs.iceiy.com',
    'localhost',
    '127.0.0.1'
];
if (!in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    header('HTTP/1.1 400 Bad Request');
    include 'error_400.html'; // adjust the path if needed
    error_log("Blocked request from invalid host: " . $_SERVER['HTTP_HOST']);
    exit;
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_generated'] = time();
}

// Success message
$_SESSION['success_message'] = "Connection successfully established and security measures applied.";

// You can now display the success message in your HTML template if needed

// Optionally: Display the message here for debugging
if (isset($_SESSION['success_message'])) {
    // Clear the success message after displaying it
    unset($_SESSION['success_message']);
}

?>
