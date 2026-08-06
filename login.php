<?php
// Technologies Used: Password Hashing (BCrypt), PHP Sessions, MySQL Verification
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loginInput = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Check Admins table (by Username OR Email)
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE Username = ? OR Email = ?");
    $stmt->execute([$loginInput, $loginInput]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['Password'])) {
        $_SESSION['admin_id'] = $admin['Admin_ID'];
        $_SESSION['username'] = $admin['Username'];
        header("Location: admin_dashboard.php");
        exit;
    }

    // 2. Check Users table (by Username OR Email)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Username = ? OR Email = ?");
    $stmt->execute([$loginInput, $loginInput]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['username'] = $user['Username'];
        header("Location: index.php");
        exit;
    }

    $error = "Invalid credentials! Please check your username/email and password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Vault</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #101416;
            --bg-secondary: #141A1D;
            --bg-card: #181E22;
            --bg-card-hover: #232A30;

            --text-main: #F5F7FA;
            --text-secondary: #D6DCE2;
            --text-muted: #CBD5E1;
            --border-color: rgba(76, 175, 80, 0.15);
            --border-strong: rgba(76, 175, 80, 0.30);

            --accent-primary: #4CAF50;
            --accent-secondary: #7CB342;
            --accent-tertiary: #A5D66F;
            --accent-gradient: linear-gradient(135deg, #4CAF50 0%, #66BB6A 33%, #7CB342 66%, #7CB342 100%);

            --accent-success: #43A047;
            --accent-warning: #F9A825;
            --accent-danger: #D32F2F;
            --accent-info: #26A69A;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        .form-label, 
        .auth-card label {
            color: #E2E8F0 !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            margin-bottom: 0.4rem !important;
            display: block;
        }

        .form-control-custom::placeholder {
            color: #94A3B8 !important;
            opacity: 1 !important;
        }

        .bg-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 450px;
            height: 450px;
            background: var(--accent-primary);
            filter: blur(160px);
            opacity: 0.15;
            border-radius: 50%;
            pointer-events: none;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 1;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: var(--accent-gradient);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            margin: 0 auto 1rem;
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.75rem;
            letter-spacing: -0.5px;
            color: #FFF;
        }

        .brand-subtitle {
            color: #94A3B8;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .form-control-custom {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid var(--border-strong) !important;
            color: var(--text-main) !important;
            border-radius: 12px !important;
            padding: 0.8rem 1rem 0.8rem 2.8rem !important;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: var(--accent-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            z-index: 5;
        }

        .btn-gradient {
            background: var(--accent-gradient);
            color: #FFF !important;
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .alert-custom {
            background: rgba(211, 47, 47, 0.12);
            border: 1px solid rgba(211, 47, 47, 0.3);
            color: #EF9A9A;
            border-radius: 12px;
            font-size: 0.875rem;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #94A3B8;
        }

        .auth-footer a {
            color: var(--accent-tertiary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .back-link {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: #94A3B8;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #FFF;
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left me-2"></i>Back to Library</a>

    <div class="bg-glow"></div>

    <div class="auth-card">
        <div class="brand-header">
            <div class="brand-icon">
                <i class="fa-solid fa-gamepad"></i>
            </div>
            <h1 class="brand-title">Welcome Back</h1>
            <p class="brand-subtitle">Enter your credentials to access your account</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-custom p-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username or Email</label>
                <div class="position-relative">
                    <i class="fa-regular fa-user input-icon"></i>
                    <input type="text" name="username" class="form-control form-control-custom" placeholder="Enter username or email" required autocomplete="off">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="position-relative">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control form-control-custom" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-gradient w-100">
                Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create Account</a>
        </div>
    </div>

</body>
</html>