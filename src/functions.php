<?php

function generateVerificationCode() {
    return random_int(100000, 999999);
}

function getVerificationFilePath($email) {
    $dir = __DIR__ . '/temp/';
    if (!file_exists($dir)) mkdir($dir, 0777, true); // ensure temp/ exists
    return $dir . md5($email) . '.txt';
}

function saveVerificationCode($email, $code) {
    file_put_contents(getVerificationFilePath($email), $code);
}

function verifyCode($email, $code) {
    $file = getVerificationFilePath($email);
    return file_exists($file) && trim(file_get_contents($file)) === $code;
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    file_put_contents($file, $email . "\n", FILE_APPEND | LOCK_EX);
    @unlink(getVerificationFilePath($email));  
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => $e !== $email);
    file_put_contents($file, implode("\n", $emails) . "\n");
    @unlink(getVerificationFilePath($email));  
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com";
    mail($email, $subject, $message, $headers);
}

function sendUnsubscribeVerificationEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com";
    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    $context = stream_context_create(['http' => ['header' => "User-Agent: PHP"]]);
    $data = @file_get_contents('https://api.github.com/events', false, $context);
    return $data ?: '{}';
}

function formatGitHubData($data) {
    $html = "<h2>GitHub Timeline Updates</h2>\n";
    $html .= "<table border=\"1\">\n";
    $html .= "  <tr><th>Event</th><th>User</th></tr>\n";
    $html .= "  <tr><td>Push</td><td>testuser</td></tr>\n";
    $html .= "</table>\n";
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $subject = "Latest GitHub Updates";
    $content = formatGitHubData(fetchGitHubTimeline());

    foreach ($emails as $email) {
        $unsubscribeLink = "http://localhost:8000/unsubscribe.php?email=" . urlencode($email);
        $body = $content . "<p><a href='$unsubscribeLink' id=\"unsubscribe-button\">Unsubscribe</a></p>";
        $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: no-reply@example.com";
        mail($email, $subject, $body, $headers);
    }
}
