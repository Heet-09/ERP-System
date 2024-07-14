<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$url = "http://dummy.kreonsolutions.in/api/form.php?FormID=1";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if(curl_errno($ch)) {
    echo json_encode(['error' => 'Request Error:' . curl_error($ch)]);
    http_response_code(500);
} else {
    // Check if the response is valid JSON
    json_decode($response);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $response;
    } else {
        echo json_encode(['error' => 'Invalid JSON response from API']);
        http_response_code(500);
    }
}

curl_close($ch);
?>
