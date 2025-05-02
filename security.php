<?php
// Session security
session_set_cookie_params([
    'lifetime' => 0,              // Session expires when the browser is closed
    'path' => '/',                // Cookie is available within the entire site
    'domain' => $_SERVER['HTTP_HOST'],  // For localhost, this is typically "localhost" or "127.0.0.1"
    'secure' => false,            // Set to false since we're not using HTTPS on localhost
    'httponly' => true,           // Restricts the cookie to HTTP(S) only (not accessible via JavaScript)
    'samesite' => 'Lax'           // 'Lax' allows same-site cookies
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Start the session if not already started
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=()');

// Remove the Strict-Transport-Security header on localhost
// Since HTTPS is not used on localhost, we omit this header
// header('Strict-Transport-Security: max-age=31536000; includeSubDomains'); // Commented out for localhost

// CSP with dynamic nonce (Content Security Policy)
$nonce = bin2hex(random_bytes(16));  // Generate nonce for inline scripts
header("Content-Security-Policy: "
    . "default-src 'none'; "
    . "base-uri 'self'; "
    . "form-action 'self'; "
    . "script-src 'self' 'nonce-$nonce' https://cdnjs.cloudflare.com; "
    . "style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; "
    . "img-src 'self' data: https:; "
    . "font-src 'self' https://fonts.gstatic.com; "
    . "connect-src 'self' https://api.iceiy.com; "
    . "frame-src 'none'; "
    . "report-uri /csp-report;");  // You can disable report-uri on localhost if you don't want reports sent

// Localhost host check - allows localhost and XAMPP-related URLs
$allowed_hosts = [
    'localhost',
    '127.0.0.1',
    'xampp',  // if you are using a custom domain for localhost
    'localhost:80',  // Port-specific if you're using something other than the default
];

if (!in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    header('HTTP/1.1 400 Bad Request');
    error_log("Blocked request from invalid host: " . $_SERVER['HTTP_HOST']);
    exit;
}

// CSRF token setup for the session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generate a CSRF token if it doesn't exist
    $_SESSION['csrf_generated'] = time();  // Timestamp for token creation (optional for expiration logic)
}
?>
