<?php
require 'vendor/autoload.php';
$apiKey = 'AIzaSyCIqfnaB5FH34_7JFgm3MV-MWvM94AUY-k';
$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=5.6037,-0.1870&radius=5000&type=night_club&key=" . $apiKey;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Error: " . $error . "\n";
echo "Response: " . substr($response, 0, 1000) . "\n";
