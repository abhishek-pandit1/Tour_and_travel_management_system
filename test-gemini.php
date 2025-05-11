<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set custom error log path for XAMPP
ini_set('error_log', 'C:\xampp\apache\logs\error.log');

include('includes/gemini-ai.php');
include('includes/gemini-config.php');

// Initialize Gemini AI
$gemini = new GeminiAI(GEMINI_API_KEY);

// Test prompt
$testPrompt = "Explain how AI works";

try {
    // Log the API key (first few characters only)
    error_log("Testing Gemini AI - API Key: " . substr(GEMINI_API_KEY, 0, 5) . "...");
    
    $response = $gemini->generateResponse($testPrompt);
    
    echo "<h2>Test Results:</h2>";
    echo "<p><strong>Test Prompt:</strong> " . htmlspecialchars($testPrompt) . "</p>";
    echo "<p><strong>Response:</strong> " . htmlspecialchars($response) . "</p>";
    
    // Show PHP configuration
    echo "<h2>PHP Configuration:</h2>";
    echo "<pre>";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "cURL Enabled: " . (function_exists('curl_version') ? 'Yes' : 'No') . "\n";
    echo "SSL Enabled: " . (extension_loaded('openssl') ? 'Yes' : 'No') . "\n";
    echo "cURL Version: " . print_r(curl_version(), true) . "\n";
    echo "Error Log Path: " . ini_get('error_log') . "\n";
    echo "</pre>";
    
    // Test direct API call
    echo "<h2>Direct API Test:</h2>";
    $testUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . GEMINI_API_KEY;
    
    $testData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $testPrompt]
                ]
            ]
        ]
    ];
    
    $ch = curl_init($testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $testResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    fclose($verbose);
    
    curl_close($ch);
    
    echo "<p>API Test Response Code: " . $httpCode . "</p>";
    echo "<p>API Test Response: " . htmlspecialchars(substr($testResponse, 0, 200)) . "...</p>";
    echo "<h3>Verbose Log:</h3>";
    echo "<pre>" . htmlspecialchars($verboseLog) . "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?> 