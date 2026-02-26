<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "brent@warfdesigns.com";

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $date = trim($_POST["date"] ?? "");
    $notes = trim($_POST["notes"] ?? "");

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === "" || $date === "" || $notes === "") {
        header("Location: /pages/error.html");
        exit();
    }

    $subject = "New Appointment Request";
    $headers = "From: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
    $headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";

    $message = "You have a new appointment request:\n\n";
    $message .= "Name: " . htmlspecialchars($name) . "\n";
    $message .= "Email: " . htmlspecialchars($email) . "\n";
    $message .= "Phone: " . htmlspecialchars($phone) . "\n";
    $message .= "Preferred Date: " . htmlspecialchars($date) . "\n";
    $message .= "Notes:\n" . htmlspecialchars($notes) . "\n";

    // Create formatted record
    $record = "Name: $name | Email: $email | Phone: $phone | Date: $date" . PHP_EOL;

    // Save to appointments.txt (create if it doesn’t exist)
    $resultsDir = dirname(__DIR__) . "/results";
    if (!is_dir($resultsDir)) {
        mkdir($resultsDir, 0755, true);
    }
    $file = $resultsDir . "/appointment-request.txt";
    file_put_contents($file, $record, FILE_APPEND);

    if (mail($to, $subject, $message, $headers)) {
        // Redirect to thank-you page
        header("Location: /pages/thank-you.html");
        exit();
    } else {
        // Redirect to an error page
        header("Location: /pages/error.html");
        exit();
    }
}
?>