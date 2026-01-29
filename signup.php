<?php
session_start();
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/assets/includes/AuthService.php';

$signupError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $termsAccepted = isset($_POST['terms']);
    $returnUrl = trim($_POST['return_url'] ?? 'matches.php');

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $signupError = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signupError = 'Please enter a valid email address.';
    } elseif ($password !== $confirmPassword) {
        $signupError = 'Passwords do not match.';
    } elseif (!$termsAccepted) {
        $signupError = 'You must agree to the Terms & Conditions.';
    } else {
        $db = footcast_db();
        $authRepository = new AuthRepository($db);
        $authService = new AuthService($authRepository);

        if ($authService->emailExists($email)) {
            $signupError = 'Email is already registered.';
        }

        if ($signupError === '') {
            $newUserId = $authService->register($name, $email, $password);
            if ($newUserId > 0) {
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $name;
                $db->close();
                echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"></head><body>";
                echo "<script>localStorage.setItem('footcastLoggedIn','1');";
                echo "window.location.href=" . json_encode($returnUrl) . ";</script>";
                echo "</body></html>";
                exit;
            }
            $signupError = 'Unable to create account right now.';
        }

        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up </title>
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="background-grid2"></div>
    
    <div class="login-container">
        <div class="login-card">
            <h1 class="welcome-title">Create Account</h1>
            <p class="welcome-subtitle">Please enter your details to sign up.</p>
            
            <div class="social-login">
                <button class="social-btn apple-btn" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#000000">
                        <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                    </svg>
                </button>
                <button class="social-btn google-btn" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                </button>
               
            </div>
            
            <div class="divider">
                <span>OR</span>
            </div>
            
            <form class="login-form" id="signupForm" action="signup.php" method="post">
                <input type="hidden" id="returnUrl" name="return_url" value="matches.php">
                <?php if ($signupError): ?>
                    <p class="error-message" style="display: block;"><?php echo htmlspecialchars($signupError, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder="Enter your name..." >
                    <span class="error-message" id="nameError"></span>
                </div>
                
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Enter your email..." >
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <div class="form-group">
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Password" >
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <span class="error-message" id="passwordError"></span>
                </div>
                
                <div class="form-group">
                    <div class="password-input-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password">
                        <button type="button" class="password-toggle" id="confirmPasswordToggle">
                            <i class="fas fa-eye" id="confirmEyeIcon"></i>
                        </button>
                    </div>
                    <span class="error-message" id="confirmPasswordError"></span>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                    <input type="checkbox" id="terms" name="terms" value="1">
                    <span>I agree to the Terms & Conditions</span>
                </label>
                <span class="error-message" id="termsError"></span>
            </div>
                
                <button type="submit" class="sign-in-btn">Sign Up</button>
            </form>
            
            <p class="register-link">
                Already have an account? <a href="login.php">Sign In</a>
            </p>
        </div>
    </div>

    <script src="./assets/js/signup.js"></script>
</body>
</html>
