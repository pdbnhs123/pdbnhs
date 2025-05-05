<?php

require_once 'security.php'; // security headers & nonce
require_once 'config.php';   // session & common setup
require_once 'db.php';       // $pdo setup

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
    <link rel="icon" type="image/png" sizes="32x32" href="img/pdb.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/pdb.png">
    
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    
    <!-- Windows Metro -->
    <meta name="msapplication-TileColor" content="#0d265c">
    <meta name="msapplication-TileImage" content="img/pdb.png">
    
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-blue: #0d265c;
            --sidebar-dark: #293fad;
            --sidebar-text: rgba(255, 255, 255, 0.95);
            --sidebar-active: rgba(255, 255, 255, 0.15);
            --content-bg: #f5f7ff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --transition-speed: 0.3s;
            --primary: #2745a5;
            --success: #28a745;
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* LOGIN PAGE STYLES */
        body.login-page {
            display: flex;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
            background-color: var(--content-bg);
            background-image: url('https://cache.1ms.net/1920x1200/abstract-blue-1920x1200_101920.jpg');
            background-size: cover;
            background-position: center;
            padding: 20px;
        }

        body.login-page::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(13, 38, 92, 0.7);
            z-index: 1;
        }

        .login-container {
            max-width: 420px;
            width: 100%;
            padding: 2.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .login-container .logo {
            margin-bottom: 2rem;
        }

        .login-container .logo img {
            width: 80px;
            height: auto;
            margin-bottom: 1rem;
        }

        .login-container h2 {
            color: var(--sidebar-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.15);
        }

        .password-container {
            position: relative;
        }

        .password-container #togglePassword {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-light);
            top: 70%;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--sidebar-dark));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
            }
        }
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
    <script>
        // Password visibility toggle
        document.querySelector('#togglePassword').addEventListener('click', function() {
            const passwordField = document.querySelector('#password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>