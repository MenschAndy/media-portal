<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $error = '';
    $success = '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if username exists
        $check_stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $check_stmt->execute([$username]);
        
        if ($check_stmt->fetch()) {
            $error = 'Username already exists';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            
            if ($stmt->execute([$username, $hashed_password])) {
                $success = 'Registration successful! Please log in.';
                header('Location: login.php?success=1');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Media Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f1419 0%, #1a2332 100%);
        }
        .register-container {
            background: rgba(20, 30, 50, 0.8);
            border: 1px solid rgba(100, 150, 255, 0.3);
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        .register-container h1 {
            color: #fff;
            margin-bottom: 30px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .register-container svg {
            width: 30px;
            height: 30px;
            color: #6495ff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(100, 150, 255, 0.3);
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6495ff;
            background: rgba(100, 150, 255, 0.1);
        }
        .error-message {
            color: #ff6b6b;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .success-message {
            color: #6bff6b;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #6495ff 0%, #4a7dff 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(100, 150, 255, 0.3);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 14px;
        }
        .login-link a {
            color: #6495ff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Register
        </h1>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-register">Register</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>
</body>
</html>