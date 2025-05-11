<?php
session_start();
require_once('includes/config.php');
require_once('razorpay-config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get booking details from POST request
$package_id = $_POST['package_id'] ?? null;
$fromdate = $_POST['fromdate'] ?? null;
$todate = $_POST['todate'] ?? null;
$comment = $_POST['comment'] ?? '';
$useremail = $_SESSION['login'] ?? null;
$meal_plan = $_POST['meal_plan'] ?? null;
$meal_cost = $_POST['meal_cost'] ?? 0;

if (!$package_id || !$fromdate || !$todate || !$useremail) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    // Get package price
    $sql = "SELECT PackagePrice FROM tbltourpackages WHERE PackageId = :pid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid', $package_id, PDO::PARAM_INT);
    $query->execute();
    $package = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$package) {
        echo json_encode(['error' => 'Package not found']);
        exit;
    }
    
    // Calculate total amount including meal cost
    $totalAmount = $package['PackagePrice'] + $meal_cost;
    
    // Create Razorpay order with total amount
    $order = createRazorpayOrder($totalAmount);
    
    if (!$order) {
        echo json_encode(['error' => 'Failed to create payment order']);
        exit;
    }
    
    // Store booking details in session for verification
    $_SESSION['pending_booking'] = [
        'package_id' => $package_id,
        'fromdate' => $fromdate,
        'todate' => $todate,
        'comment' => $comment,
        'useremail' => $useremail,
        'order_id' => $order->id,
        'amount' => $totalAmount,
        'meal_plan' => $meal_plan,
        'meal_cost' => $meal_cost
    ];
    
    // Return the order details in the format Razorpay expects
    echo json_encode([
        'id' => $order->id,
        'amount' => $order->amount,
        'currency' => $order->currency,
        'key_id' => $razorpay_key_id
    ]);
    
} catch (Exception $e) {
    error_log("Razorpay Order Creation Error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?> 