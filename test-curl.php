<?php
// Check if cURL is enabled
if (function_exists('curl_version')) {
    echo "cURL is enabled on your server.<br>";
    
    // Test a simple cURL request
    $ch = curl_init('https://www.google.com');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Test request to Google - HTTP Code: " . $httpCode . "<br>";
    echo "If you see HTTP Code 200, cURL is working properly.";
} else {
    echo "cURL is not enabled on your server. Please enable it in your PHP configuration.";
}
?> 