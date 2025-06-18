<?php
require 'functions.php';

$emailPrefill = '';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = generateVerificationCode();
        saveVerificationCode($email, $code);
        sendVerificationEmail($email, $code);
        $message = "Verification code sent to your email.";
        $messageType = "success";
        $emailPrefill = htmlspecialchars($email);
    } elseif (isset($_POST['verification_code'], $_POST['verify_email'])) {
        $email = trim($_POST['verify_email']);
        $code = trim($_POST['verification_code']);
        $emailPrefill = htmlspecialchars($email);
        if (verifyCode($email, $code)) {
            registerEmail($email);
            $message = "Email verified and registered successfully!";
            $messageType = "success";
            $emailPrefill = '';
        } else {
            $message = "Invalid verification code.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subscribe to GitHub Updates</title>
    <link rel="stylesheet" href="styles/subscribe.css">
</head>
<body>

<?php if ($message): ?>
    <div class="popup <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<h2>Subscribe To GitHub Timeline Updates</h2>
<form method="POST">
    <input type="email" name="email" required placeholder="Enter your email">
    <button id="submit-verification" type="submit">Submit</button>
</form>

<h2>Verify Email</h2>
<form method="POST">
    <input type="email" name="verify_email" required placeholder="Enter your email" value="<?= $emailPrefill ?>">
    <input type="text" name="verification_code" maxlength="6" required placeholder="Verification code">
    <button id="submit-verification" type="submit">Verify</button>
</form>

</body>
</html>
