<?php
require_once __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;

// Razorpay Configuration
$razorpay_key_id = 'rzp_test_tHnh7a16oE7xq6'; // Your test key ID
$razorpay_key_secret = 'q4yHubexBzNslbC6S56Dj0L7'; // Your test key secret

// Function to create a Razorpay order
function createRazorpayOrder($amount, $currency = 'INR') {
    global $razorpay_key_id, $razorpay_key_secret;
    
    try {
        // Initialize cURL
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => $amount * 100, // Convert to paise
            'currency' => $currency,
            'payment_capture' => 1
        ]));
        curl_setopt($ch, CURLOPT_USERPWD, $razorpay_key_id . ':' . $razorpay_key_secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("Razorpay API Error: " . $response);
            return false;
        }
        
        return json_decode($response);
    } catch (Exception $e) {
        error_log("Razorpay Order Creation Error: " . $e->getMessage());
        return false;
    }
}

// Function to verify Razorpay payment
function verifyRazorpayPayment($payment_id, $order_id, $signature) {
    global $razorpay_key_secret;
    
    try {
        $generated_signature = hash_hmac('sha256', $order_id . "|" . $payment_id, $razorpay_key_secret);
        return hash_equals($generated_signature, $signature);
    } catch (Exception $e) {
        error_log("Razorpay Payment Verification Error: " . $e->getMessage());
        return false;
    }
}
?> 