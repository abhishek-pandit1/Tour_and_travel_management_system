<?php
session_start();
require_once('includes/config.php');
require_once('razorpay-config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get payment details from the request
$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? null;
$razorpay_order_id = $_POST['razorpay_order_id'] ?? null;
$razorpay_signature = $_POST['razorpay_signature'] ?? null;
$fromdate = $_POST['fromdate'] ?? null;
$todate = $_POST['todate'] ?? null;
$comment = $_POST['comment'] ?? '';
$package_id = $_POST['package_id'] ?? null;
$useremail = $_SESSION['login'] ?? null;

if (!$razorpay_payment_id || !$razorpay_order_id || !$razorpay_signature || !$fromdate || !$todate || !$package_id || !$useremail) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    // Verify the payment
    $verification = verifyRazorpayPayment($razorpay_payment_id, $razorpay_order_id, $razorpay_signature);
    
    if (!$verification) {
        echo json_encode(['error' => 'Payment verification failed']);
        exit;
    }
    
    // Start a transaction
    $dbh->beginTransaction();
    
    try {
        // Insert the booking into the tblbooking table
        $sql = "INSERT INTO tblbooking (
            PackageId,
            UserEmail,
            FromDate,
            ToDate,
            Comment,
            status,
            RegDate,
            PaymentId
        ) VALUES (:pid, :useremail, :fromdate, :todate, :comment, 1, NOW(), :payment_id)";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':pid', $package_id, PDO::PARAM_INT);
        $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
        $query->bindParam(':todate', $todate, PDO::PARAM_STR);
        $query->bindParam(':comment', $comment, PDO::PARAM_STR);
        $query->bindParam(':payment_id', $razorpay_payment_id, PDO::PARAM_STR);
        
        if (!$query->execute()) {
            throw new Exception("Failed to create booking: " . implode(", ", $query->errorInfo()));
        }
        
        $booking_id = $dbh->lastInsertId();
        
        // Commit the transaction
        $dbh->commit();
        
        // Clear any pending booking from session
        if (isset($_SESSION['pending_booking'])) {
            unset($_SESSION['pending_booking']);
        }
        
        echo json_encode([
            'success' => true,
            'booking_id' => $booking_id,
            'message' => 'Booking confirmed successfully'
        ]);
        
    } catch (Exception $e) {
        // Rollback the transaction on error
        $dbh->rollBack();
        error_log("Booking Creation Error: " . $e->getMessage());
        echo json_encode(['error' => 'Failed to create booking: ' . $e->getMessage()]);
    }
    
} catch (Exception $e) {
    error_log("Payment Verification Error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?> 