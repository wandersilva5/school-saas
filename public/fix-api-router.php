<?php
// Simple API test script using file_get_contents instead of cURL
$url = 'http://localhost/api/auth/login';
$data = json_encode([
    'email' => 'wandersilva5@gmail.com',
    'password' => '44xmax01'
]);

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n" .
                    "Content-Length: " . strlen($data) . "\r\n",
        'content' => $data,
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

// Get response status
$status_line = $http_response_header[0];
preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
$status = $match[1];

echo "Status: " . $status . "\n";
echo "Response: " . $response . "\n";