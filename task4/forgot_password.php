<?php
require_once 'config/db.php';
require_once 'inc/functions.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/customer/index.php');
}

// Ensure required columns exist
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL DEFAULT NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expires DATETIME NULL DEFAULT NULL");
} catch (PDOException $e) {
    // ignore
}

// ----- Mailtrap SMTP Configuration (your credentials) -----
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_PORT', 2525);
define('SMTP_USER', '1bd59799a95c2d');
define('SMTP_PASS', '49f02d9a55223f');   // ✅ your actual password

// Custom SMTP sender (works on any platform, no extra libraries)
function sendSMTPmail($to, $subject, $body, $from = 'noreply@foodiedash.com') {
    $smtp = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 30);
    if (!$smtp) {
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }
    
    $read = fgets($smtp);
    if (substr($read, 0, 3) != '220') return false;
    
    fputs($smtp, "EHLO " . gethostname() . "\r\n");
    $read = fgets($smtp);
    while (substr($read, 3, 1) == '-') { $read = fgets($smtp); } // eat multiline
    
    // Authenticate
    fputs($smtp, "AUTH LOGIN\r\n");
    $read = fgets($smtp);
    fputs($smtp, base64_encode(SMTP_USER) . "\r\n");
    $read = fgets($smtp);
    fputs($smtp, base64_encode(SMTP_PASS) . "\r\n");
    $read = fgets($smtp);
    if (substr($read, 0, 3) != '235') {
        error_log("SMTP auth failed: $read");
        return false;
    }
    
    fputs($smtp, "MAIL FROM: <$from>\r\n");
    $read = fgets($smtp);
    fputs($smtp, "RCPT TO: <$to>\r\n");
    $read = fgets($smtp);
    fputs($smtp, "DATA\r\n");
    $read = fgets($smtp);
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $from\r\n";
    $message = $headers . "\r\n" . $body;
    
    fputs($smtp, "$message\r\n.\r\n");
    $read = fgets($smtp);
    fputs($smtp, "QUIT\r\n");
    fclose($smtp);
    
    return substr($read, 0, 3) == '250';
}

$step = $_GET['step'] ?? 'request';
$message = '';
$error = '';
$valid_user = null;

// --- Request reset link ---
if ($step === 'request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expiry, $user['id']]);
            
            // Generate absolute reset link (for email)
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $resetLink = $protocol . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . "/forgot_password.php?step=reset&token=$token";
            
            // Professional HTML email template
            $emailBody = "
            <html>
            <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>
                    <h2 style='color: #333; text-align: center; margin-bottom: 30px;'>Password Reset Request</h2>
                    
                    <p style='color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 20px;'>
                        Hi <strong>" . htmlspecialchars($user['email']) . "</strong>,
                    </p>
                    
                    <p style='color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 20px;'>
                        We received a request to reset the password for your account. If you didn't make this request, please ignore this email and your password will remain unchanged.
                    </p>
                    
                    <p style='color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 30px;'>
                        To reset your password, click the button below:
                    </p>
                    
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <a href='$resetLink' style='background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;'>Reset Password</a>
                    </div>
                    
                    <p style='color: #999; font-size: 14px; line-height: 1.6; margin-bottom: 20px;'>
                        Or copy and paste this link in your browser:
                    </p>
                    
                    <p style='color: #007bff; font-size: 13px; word-break: break-all; margin-bottom: 30px; padding: 10px; background-color: #f9f9f9; border-left: 4px solid #007bff;'>
                        $resetLink
                    </p>
                    
                    <p style='color: #ff6b6b; font-size: 14px; line-height: 1.6; margin-bottom: 30px;'>
                        <strong>⏰ Important:</strong> This reset link will expire in 1 hour for security reasons.
                    </p>
                    
                    <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                    
                    <p style='color: #999; font-size: 12px; text-align: center;'>
                        If you have any questions, please contact our support team.<br>
                        © " . date('Y') . " Foodie Dash. All rights reserved.
                    </p>
                </div>
            </body>
            </html>";
            
            $sent = sendSMTPmail($user['email'], 'Password Reset Request - Foodie Dash', $emailBody);
            if ($sent) {
                $message = "✅ <strong>Reset link sent successfully!</strong><br>A password reset link has been sent to <strong>" . htmlspecialchars($email) . "</strong>. Please check your inbox (and spam folder) for the email. The link will expire in 1 hour.";
            } else {
                $error = "⚠️ Could not send email. Please try again later or contact support.";
            }
        } else {
            $error = "If an account exists with this email, you will receive a password reset link shortly.";
            // Don't reveal if email exists or not (security best practice)
        }
    }
}

// --- Validate token and reset password ---
if ($step === 'reset' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $valid_user = $stmt->fetch();
    
    if (!$valid_user) {
        $error = "Invalid or expired reset link. Please request a new one.";
        $step = 'request';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (empty($password) || empty($confirm)) {
            $error = "Please fill in all fields.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed, $valid_user['id']]);
            $message = "✅ Password reset successful! <a href='" . BASE_URL . "/login.php' class='btn btn-sm btn-success mt-2'>Login Now</a>";
            $step = 'done';
            $valid_user = null;
        }
    }
}

include 'inc/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-key me-2"></i> 
                    <?= $step === 'request' ? 'Forgot Password' : ($step === 'reset' ? 'Reset Password' : 'Password Reset') ?>
                </h4>
            </div>
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($step === 'request'): ?>
                    <form method="POST">
                        <p class="text-muted mb-3"><i class="fas fa-info-circle"></i> Enter the email address associated with your account and we'll send you a link to reset your password.</p>
                        <div class="mb-3">
                            <label class="form-label"><strong>Email Address</strong></label>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com" required autofocus>
                            <small class="form-text text-muted d-block mt-2">Check your spam/junk folder if you don't see the email within a few minutes.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-envelope me-2"></i>Send Reset Link</button>
                        <div class="text-center mt-3">
                            <small><a href="login.php" class="text-decoration-none">← Back to Login</a> | <a href="register.php" class="text-decoration-none">Create New Account →</a></small>
                        </div>
                    </form>
                <?php elseif ($step === 'reset' && $valid_user): ?>
                    <form method="POST">
                        <p class="mb-3 text-muted"><i class="fas fa-lock me-2"></i>Resetting password for <strong><?= htmlspecialchars($valid_user['email']) ?></strong></p>
                        <div class="mb-3">
                            <label class="form-label"><strong>New Password</strong></label>
                            <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6" autocomplete="new-password">
                            <small class="form-text text-muted d-block mt-2">Use a strong password with letters, numbers, and symbols.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Confirm Password</strong></label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2"><i class="fas fa-check me-2"></i>Reset Password</button>
                        <div class="text-center mt-3">
                            <small><a href="forgot_password.php" class="text-decoration-none">Request another reset link →</a></small>
                        </div>
                    </form>
                <?php elseif ($step === 'done'): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                        <h4>Password Reset Successful! ✓</h4>
                        <p class="text-muted mt-3">Your password has been successfully updated. You can now log in with your new password.</p>
                        <a href="login.php" class="btn btn-success mt-3"><i class="fas fa-sign-in-alt me-2"></i>Go to Login</a>
                    </div>
                <?php elseif ($step === 'reset' && !$valid_user): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-times-circle fa-5x text-danger mb-3"></i>
                        <h4>Invalid or Expired Link</h4>
                        <p class="text-muted mt-3">The password reset link has expired or is invalid. Links expire after 1 hour for security reasons.</p>
                        <a href="forgot_password.php" class="btn btn-primary mt-3"><i class="fas fa-redo me-2"></i>Request New Link</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>