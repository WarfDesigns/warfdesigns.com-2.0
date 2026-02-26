<?php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "my_website";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get data from request
$user = trim($_POST['username']);
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hashing

$stripeSecret = getenv('STRIPE_SECRET_KEY');
if (!$stripeSecret) {
  http_response_code(500);
  echo "Stripe configuration missing.";
  exit;
}

function createStripeCustomer($email, $stripeSecret) {
  $payload = http_build_query([
    "email" => $email,
    "metadata" => [
      "source" => "website_registration"
    ]
  ]);

  $ch = curl_init("https://api.stripe.com/v1/customers");
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERPWD, $stripeSecret . ":");

  $response = curl_exec($ch);
  if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    return ["error" => $error];
  }

  $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  $data = json_decode($response, true);
  if ($statusCode < 200 || $statusCode >= 300 || !isset($data["id"])) {
    $message = $data["error"]["message"] ?? "Stripe customer creation failed.";
    return ["error" => $message];
  }

  return ["id" => $data["id"]];
}

$stripeResult = createStripeCustomer($user, $stripeSecret);
if (isset($stripeResult["error"])) {
  http_response_code(500);
  echo "Stripe error: " . $stripeResult["error"];
  exit;
}

$stripeCustomerId = $stripeResult["id"];

// Insert user into DB
$hasStripeColumn = false;
$columnCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'stripe_customer_id'");
if ($columnCheck && $columnCheck->num_rows > 0) {
  $hasStripeColumn = true;
}

if ($hasStripeColumn) {
  $stmt = $conn->prepare("INSERT INTO users (username, password, stripe_customer_id) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $user, $pass, $stripeCustomerId);
} else {
  $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $stmt->bind_param("ss", $user, $pass);
}

if ($stmt->execute()) {
  echo "User registered successfully!";
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
