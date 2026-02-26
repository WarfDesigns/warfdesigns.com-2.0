<?php
session_start();

header("Content-Type: application/json");

$stripeSecret = getenv("STRIPE_SECRET_KEY");
$customerId = isset($_SESSION['stripe_customer_id']) ? $_SESSION['stripe_customer_id'] : null;

if (!$stripeSecret) {
  echo json_encode([
    "error" => true,
    "message" => "Stripe secret key is not configured."
  ]);
  exit;
}

if (!$customerId) {
  echo json_encode([
    "error" => true,
    "message" => "Customer account is not connected."
  ]);
  exit;
}

function stripe_request($endpoint, $stripeSecret, $query = []) {
  $url = "https://api.stripe.com/v1/" . $endpoint;
  if (!empty($query)) {
    $url .= "?" . http_build_query($query);
  }

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $stripeSecret
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  return [
    "status" => $httpCode,
    "body" => json_decode($response, true)
  ];
}

$customerResponse = stripe_request("customers/" . $customerId, $stripeSecret);
$invoiceResponse = stripe_request("invoices", $stripeSecret, [
  "customer" => $customerId,
  "limit" => 10,
  "expand" => ["data.lines"]
]);
$paymentResponse = stripe_request("charges", $stripeSecret, [
  "customer" => $customerId,
  "limit" => 5
]);

if ($customerResponse["status"] >= 400) {
  echo json_encode([
    "error" => true,
    "message" => "Unable to load customer data from Stripe."
  ]);
  exit;
}

$customer = $customerResponse["body"];
$invoices = isset($invoiceResponse["body"]["data"]) ? $invoiceResponse["body"]["data"] : [];
$payments = isset($paymentResponse["body"]["data"]) ? $paymentResponse["body"]["data"] : [];

$formattedInvoices = array_map(function ($invoice) {
  $lines = isset($invoice["lines"]["data"]) ? $invoice["lines"]["data"] : [];
  return [
    "number" => $invoice["number"] ?: $invoice["id"],
    "date" => $invoice["created"],
    "status" => $invoice["status"],
    "amount_due" => ($invoice["amount_due"] ?? 0) / 100,
    "total" => ($invoice["total"] ?? 0) / 100,
    "amount_paid" => ($invoice["amount_paid"] ?? 0) / 100,
    "amount_remaining" => ($invoice["amount_remaining"] ?? 0) / 100,
    "due_date" => $invoice["due_date"] ?? null,
    "hosted_invoice_url" => $invoice["hosted_invoice_url"] ?? null,
    "invoice_pdf" => $invoice["invoice_pdf"] ?? null,
    "lines" => array_map(function ($line) {
      return [
        "description" => $line["description"] ?: "Line item",
        "amount" => ($line["amount"] ?? 0) / 100,
        "quantity" => $line["quantity"] ?? null
      ];
    }, $lines)
  ];
}, $invoices);

$formattedPayments = array_map(function ($payment) {
  return [
    "id" => $payment["id"],
    "date" => $payment["created"],
    "status" => $payment["status"],
    "amount" => ($payment["amount"] ?? 0) / 100
  ];
}, $payments);

echo json_encode([
  "customer" => [
    "name" => $customer["name"] ?: "Customer",
    "email" => $customer["email"] ?: "",
    "balance" => ($customer["balance"] ?? 0) / 100,
    "status" => $customer["delinquent"] ? "Past due" : "Active"
  ],
  "invoices" => $formattedInvoices,
  "payments" => $formattedPayments
]);