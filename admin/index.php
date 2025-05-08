<?php

//require_once './security.php'; // security headers & nonce
require_once './db.php';       // $pdo setup

// CSRF Token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$username = '';
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

$MAX_ATTEMPTS = 5;
$LOCKOUT_DURATION = 300; // 5 minutes

// Initialize tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = null;
}

$error = '';
$remaining_attempts = $MAX_ATTEMPTS - $_SESSION['login_attempts'];

// Lockout check
if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) {
    $remaining = $_SESSION['lockout_time'] - time();
    if ($remaining > 0) {
        $error = "Too many failed attempts. Try again in " . ceil($remaining / 60) . " minute(s).";
    } else {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lockout_time'] = null;
        $remaining_attempts = $MAX_ATTEMPTS;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid CSRF token. Please refresh the page and try again.';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Regenerate token after failure
    } else {
        $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
        $password = $_POST['password'];

        if (!empty($username) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admin && password_verify($password, $admin['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['full_name'];

                    $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['lockout_time'] = null;

                    header('Location: dashboard.php');
                    exit();
                } else {
                    $_SESSION['login_attempts']++;
                    $remaining_attempts = $MAX_ATTEMPTS - $_SESSION['login_attempts'];

                    if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) {
                        $_SESSION['lockout_time'] = time() + $LOCKOUT_DURATION;
                        $error = "Too many failed attempts. Try again in " . ceil($LOCKOUT_DURATION / 60) . " minute(s).";
                    } else {
                        $error = "Invalid username or password. Attempts left: $remaining_attempts";
                    }
                }
            } catch (PDOException $e) {
                $error = "Server error. Please try again later.";
            }
        } else {
            $error = "Please enter both username and password.";
        }
    }
}
include 'load.html';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon Configuration -->
    <!-- Standard ICO format (fallback) -->
    <link rel="icon" href="img/pdb.png" type="image/x-icon">
    
    <!-- Modern browsers (PNG format) -->
    <link rel="icon" type="image/png" sizes="32x32" href="../img/pdb.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/pdb.png">
    
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    
    <!-- Windows Metro -->
    <meta name="msapplication-TileColor" content="#0d265c">
    <meta name="msapplication-TileImage" content="img/pdb.png">
    
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="./css/index.css" rel="stylesheet">
    <style>

    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="logo">
            <img src="./img/pdb.png" alt="Logo">
            <h2>Welcome Back!</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required value="<?= htmlspecialchars($username ?? '') ?>">
            </div>

            <div class="form-group password-container">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>

            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
    <script src="./js/index.js"></script>
</body>
</html>