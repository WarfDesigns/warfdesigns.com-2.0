<?php
session_start();

$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "my_website";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, stripe_customer_id FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  $stmt->bind_result($id, $hashed_password, $stripe_customer_id);
  $stmt->fetch();

  if (password_verify($pass, $hashed_password)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $user;
    $_SESSION['stripe_customer_id'] = $stripe_customer_id;
    echo "Login success!";
  } else {
    echo "Invalid password.";
  }
} else {
  echo "User not found.";
}

$stmt->close();
$conn->close();
?>
