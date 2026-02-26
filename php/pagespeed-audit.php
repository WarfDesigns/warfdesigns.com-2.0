<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => ['message' => 'Method not allowed.']]);
    exit;
}

$rawUrl = $_GET['url'] ?? '';
$decodedUrl = trim(rawurldecode($rawUrl));

if ($decodedUrl === '') {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'A website URL is required.']]);
    exit;
}

if (!preg_match('/^https?:\/\//i', $decodedUrl)) {
    $decodedUrl = 'https://' . $decodedUrl;
}

if (!filter_var($decodedUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'Please provide a valid website URL.']]);
    exit;
}

$categories = ['performance', 'accessibility', 'best-practices', 'seo'];
$query = [
    'url' => $decodedUrl,
    'strategy' => 'mobile'
];

foreach ($categories as $category) {
    $query['category'][] = $category;
}

$apiKey = getenv('PAGESPEED_API_KEY');
if ($apiKey) {
    $query['key'] = $apiKey;
}

$endpoint = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?' . http_build_query($query);

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 45,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$responseBody = curl_exec($ch);
$curlError = curl_error($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($responseBody === false) {
    http_response_code(502);
    echo json_encode(['error' => ['message' => 'Unable to reach PageSpeed at the moment.', 'details' => $curlError]]);
    exit;
}

if ($statusCode >= 400) {
    http_response_code($statusCode);
    echo $responseBody;
    exit;
}

http_response_code(200);
echo $responseBody;
