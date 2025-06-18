<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = generateVerificationCode();
        saveVerificationCode($email, $code);
        sendUnsubscribeVerificationEmail($email, $code);
        echo "<p class='message success'>Verification code sent to your email.</p>";
    } elseif (isset($_POST['unsubscribe_verification_code'], $_POST['unsubscribe_email_verify'])) {
        $email = trim($_POST['unsubscribe_email_verify']);
        $code = trim($_POST['unsubscribe_verification_code']);
        if (verifyCode($email, $code)) {
            unsubscribeEmail($email);
            echo "<p class='message success'>Email unsubscribed successfully.</p>";
        } else {
            echo "<p class='message error'>Invalid verification code.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe from GitHub Updates</title>
    <link rel="stylesheet" href="styles/unsubscribe.css">
</head>
<body>

<h2>Unsubscribe from GitHub Updates</h2>
<form method="POST">
    <input type="email" name="unsubscribe_email" required placeholder="Enter your email">
    <button id="submit-unsubscribe" type="submit">Unsubscribe</button>
</form>

<h2>Verify Unsubscription</h2>
<form method="POST">
    <input type="email" name="unsubscribe_email_verify" required placeholder="Enter your email">
    <input type="text" name="unsubscribe_verification_code" required placeholder="Verification code">
    <button id="verify-unsubscribe" type="submit">Verify</button>
</form>

</body>
</html>
