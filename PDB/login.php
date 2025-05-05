<?php
// Secure session settings
include 'security.php';

// Include DB connection securely
require_once 'db.php';

$error = '';

// Redirect if already logged in
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ./admin/dashboard.php');
    exit();
}

// Process POST request securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = trim($_POST['password']); // password shouldn't be filtered in case of special chars

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $admin = $result->fetch_assoc();

                // Use hash_equals for constant-time comparison if you build your own hashes
                if (password_verify($password, $admin['password'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['full_name'];

                    // Update last login
                    $update = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    if ($update) {
                        $update->bind_param('i', $admin['id']);
                        $update->execute();
                        $update->close();
                    }

                    header('Location: ./admin/dashboard.php');
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        } else {
            $error = "Server error. Please try again later.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}

// NEVER echo or var_dump sensitive details like $error directly without escaping if displaying
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Paso De Blas NHS</title>

		<!-- Site favicon -->
		<link
			rel="apple-touch-icon"
			sizes="180x180"
			href="./img/pdb.png"
		/>
		<link
			rel="icon"
			type="image/png"
			sizes="32x32"
			href="./img/pdb.png"
		/>
		<link
			rel="icon"
			type="image/png"
			sizes="8x8"
			href="./img/pdb.png"
		/>

		<!-- Mobile Specific Metas -->
		<meta
			name="viewport"
			content="width=device-width, initial-scale=1, maximum-scale=1"
		/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            background-image: url('./img/it.jpg');
            
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

        .password-container i {
            position: absolute;
            right: 1.25rem;
            top: 3.25rem;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
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
    		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script
			async
			src="https://www.googletagmanager.com/gtag/js?id=G-GBZ3SGGX85"
		></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag() {
				dataLayer.push(arguments);
			}
			gtag("js", new Date());

			gtag("config", "G-GBZ3SGGX85");
		</script>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="./img/pdb.png" alt="Paso De Blas NHS Logo">
            <h2>Welcome Back</h2>
            <p>Enter your credentials to access the admin portal</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="admin@pasodeblas.edu.ph">
            </div>
            
            <div class="form-group password-container">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            <button type="submit" class="btn">
                <span>Sign In</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>