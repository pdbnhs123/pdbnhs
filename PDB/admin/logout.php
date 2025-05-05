<?php
// logout.php

// 1. Start session securely
session_start([
    'use_strict_mode' => true,
    'cookie_httponly' => true,
    'cookie_secure' => true, // Only send over HTTPS
    'cookie_samesite' => 'Strict' // Prevent CSRF
]);

// 2. Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// 3. Unset all session variables
$_SESSION = [];

// 4. Destroy the session completely
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

// 5. Destroy the session
session_destroy();

// 6. Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// 7. Redirect to login with anti-caching headers
header("Location: index.php", true, 303); // 303 See Other

// 8. Terminate script execution securely
exit();
?>