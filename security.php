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

//require_once 'ip-api.php';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=()');

if (($_SERVER['HTTPS'] ?? 'off') === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// CSP with dynamic nonce
header("Content-Security-Policy: "
    . "default-src 'self'; "  // Changed from 'none' to 'self' as base default
    . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
    . "img-src 'self' data: https:; "
    . "font-src 'self' https://fonts.gstatic.com; " 
    . "connect-src 'self' https://api.iceiy.com; "  
    . "frame-src 'none';"  
);
// Host whitelist
$allowed_hosts = [
    'pasodeblasnhs.iceiy.com',
    'www.pasodeblasnhs.iceiy.com',
    'm.pasodeblasnhs.iceiy.com'
];
if (!in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    header('HTTP/1.1 400 Bad Request');
    error_log("Blocked request from invalid host: " . $_SERVER['HTTP_HOST']);
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_generated'] = time();
}
