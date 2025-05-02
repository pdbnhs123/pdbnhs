<?php
require_once 'security.php'; // security headers & nonce
require_once 'config.php';   // session & common setup
require_once 'db.php';       // $pdo setup

// CSRF Token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
            :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray: #6b7280;
            --danger: #ef4444;
            --success: #10b981;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --rounded: 0.5rem;
            --rounded-lg: 0.75rem;
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .login-container {
            background-color: var(--light);
            padding: 3rem;
            border-radius: var(--rounded-lg);
            box-shadow: var(--shadow-md);
            width: 100%;
            max-width: 28rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0.5rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            z-index: -1;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .logo img {
            width: 5rem;
            height: auto;
            margin-bottom: 1.25rem;
        }
        
        .logo h2 {
            color: var(--dark);
            font-weight: 600;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .logo p {
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.75rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--dark);
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 1px solid #e5e7eb;
            border-radius: var(--rounded);
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
            box-shadow: var(--shadow-sm);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .password-container {
    position: relative;
}

#togglePassword {
    position: absolute;
    right: 10px;  /* Adjust this based on your design */
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    cursor: pointer;
    font-size: 1.2rem;
    transition: 0.2s;
}

#togglePassword:hover {
    color: #333;
}

        .password-container i:hover {
            color: var(--dark);
        }
        
        .btn {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: var(--rounded);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(67, 97, 238, 0.3), 0 2px 4px -1px rgba(67, 97, 238, 0.1);
        }
        
        .btn:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(67, 97, 238, 0.3), 0 4px 6px -2px rgba(67, 97, 238, 0.1);
        }

        .btn:active {
            transform: translateY(0);
        }
        
        .error {
            color: var(--danger);
            margin-bottom: 1.5rem;
            text-align: center;
            padding: 1rem;
            background-color: rgba(239, 68, 68, 0.05);
            border-radius: var(--rounded);
            font-weight: 500;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .forgot-password {
            text-align: right;
            margin: -1rem 0 1.5rem;
        }

        .forgot-password a {
            color: var(--gray);
            font-size: 0.875rem;
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .forgot-password a:hover {
            color: var(--primary);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2.5rem 1.5rem;
                margin: 0 1rem;
                border-radius: var(--rounded);
            }
        }
        </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="./img/pdb.png" alt="Logo">
            <h2>Welcome Back</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"  required value="<?= htmlspecialchars($username ?? '') ?>">
            </div>

            <div class="form-group password-container">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
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
    });</script>


</body>
</html>
