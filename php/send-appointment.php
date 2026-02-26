<?php
// Basic hardening
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method Not Allowed'); }
if (!empty($_POST['company'])) { http_response_code(400); exit('Bad Request'); } // honeypot

// Collect & sanitize
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$date    = trim($_POST['date'] ?? '');
$time    = trim($_POST['time'] ?? '');
$service = trim($_POST['service'] ?? '');
$notes   = trim($_POST['notes'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $date === '' || $time === '' || $service === '') {
    http_response_code(422);
    exit('Please complete all required fields with valid information.');
}

// Compose email
$to      = 'brent.warf.cp@gmail.com'; // <- change to your address
$subject = 'New Lead - Appointment Request';
$body    = "New appointment request:\n\n"
        . "Name: $name\n"
        . "Email: $email\n"
        . "Phone: $phone\n"
        . "Preferred Date: $date\n"
        . "Preferred Time: $time\n"
        . "Service: $service\n"
        . "Notes:\n$notes\n";

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: Website <no-reply@warfdesigns.com>'; // use a domain you own
$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    echo 'Thanks! Your request was sent. We’ll be in touch shortly.';
} else {
    http_response_code(500);
    echo 'Sorry, something went wrong sending your message.';
}
