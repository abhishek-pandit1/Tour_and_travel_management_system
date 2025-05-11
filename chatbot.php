<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/gemini-ai.php');
include('includes/gemini-config.php');

// Initialize Gemini AI with your API key
$gemini = new GeminiAI(GEMINI_API_KEY);

// Database connection
$host = "localhost";
$user = "root";  // Default for XAMPP
$pass = "";  // Default for XAMPP
$dbname = "tms"; // Ensure you imported `tms.sql` into MySQL

try {
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get user input from AJAX
$user_message = $_POST['message'] ?? '';
$response = "I'm not sure I understand. Could you please rephrase your question? I can help you with tour packages, bookings, inquiries, and general questions.";

// Add natural language processing for greetings
if (preg_match('/^(hi|hello|hey|greetings|good (morning|afternoon|evening))/i', $user_message)) {
    $greetings = [
        "Hello! 👋 How can I assist you today?",
        "Hi there! 😊 What can I help you with?",
        "Greetings! 🌟 How may I be of service?",
        "Good day! 🌞 How can I help you plan your next adventure?"
    ];
    $response = $greetings[array_rand($greetings)];
}

// Add handling for "how are you" queries
elseif (preg_match('/(how are you|how\'s it going|how do you do|how are things)/i', $user_message)) {
    $responses = [
        "I'm doing great, thanks for asking! 😊 How can I help you with your travel plans today?",
        "I'm fantastic! Ready to help you plan your next adventure! 🌟 What can I do for you?",
        "I'm doing well, thank you! 🎒 How can I assist you with your travel needs?",
        "I'm excellent! Ready to help you find the perfect travel package! ✈️ What are you looking for?"
    ];
    $response = $responses[array_rand($responses)];
}

// Add natural language processing for thank you
elseif (preg_match('/^(thanks|thank you|thx|appreciate)/i', $user_message)) {
    $thanks = [
        "You're welcome! 😊 Is there anything else I can help you with?",
        "My pleasure! 🌟 Let me know if you need anything else.",
        "Glad I could help! 🎉 Feel free to ask if you have more questions.",
        "Anytime! 😊 Happy to assist with your travel plans."
    ];
    $response = $thanks[array_rand($thanks)];
}

// Add natural language processing for goodbyes
elseif (preg_match('/^(bye|goodbye|see you|farewell)/i', $user_message)) {
    $goodbyes = [
        "Goodbye! 👋 Have a wonderful day!",
        "See you later! 😊 Safe travels!",
        "Take care! 🌟 Come back soon!",
        "Farewell! 🎉 Happy journey!"
    ];
    $response = $goodbyes[array_rand($goodbyes)];
}

// Handle tour packages with more natural language
elseif (preg_match('/(show|list|display|find|search|what are|tell me about|i want to see|i\'m looking for).*(tour|package|trip|vacation|holiday)/i', $user_message)) {
    $sql = "SELECT PackageId, PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails, PackageImage FROM tbltourpackages";
    $result = $dbh->query($sql);
    
    if ($result->rowCount() > 0) {
        $response = "I'd be happy to show you our available tour packages! 🎒 Here's what we have:\n\n";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $response .= "📦 " . $row['PackageName'] . "\n";
            $response .= "📍 Type: " . $row['PackageType'] . "\n";
            $response .= "🗺️ Location: " . $row['PackageLocation'] . "\n";
            $response .= "💰 Price: $" . $row['PackagePrice'] . "\n";
            $response .= "✨ Features: " . $row['PackageFetures'] . "\n";
            $response .= "📝 Details: " . $row['PackageDetails'] . "\n";
            
            // Get reviews for this package
            $review_sql = "SELECT UserName, Rating, ReviewText, ReviewDate FROM tblreviews WHERE PackageId = ? ORDER BY ReviewDate DESC LIMIT 3";
            $review_stmt = $dbh->prepare($review_sql);
            $review_stmt->execute([$row['PackageId']]);
            $review_result = $review_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($review_result) > 0) {
                $response .= "\n📊 Recent Reviews:\n";
                foreach ($review_result as $review) {
                    $response .= "👤 " . $review['UserName'] . " (" . date("d M Y", strtotime($review['ReviewDate'])) . ")\n";
                    $response .= "   Rating: ";
                    for ($i = 0; $i < $review['Rating']; $i++) {
                        $response .= "★";
                    }
                    for ($i = $review['Rating']; $i < 5; $i++) {
                        $response .= "☆";
                    }
                    $response .= "\n   " . $review['ReviewText'] . "\n\n";
                }
            } else {
                $response .= "\n📊 No reviews yet for this package.\n\n";
            }
        }
        $response .= "Would you like to know more about any specific package? Just let me know! 😊";
    } else {
        $response = "I'm sorry, but we don't have any tour packages available at the moment. Please check back later! 😊";
    }
}

// Handle booking status queries with more natural language
elseif (preg_match('/(check|what is|tell me about|status of|my).*(booking|reservation)/i', $user_message)) {
    preg_match('/\d+/', $user_message, $matches);
    $booking_id = $matches[0] ?? '';
    if ($booking_id) {
        $sql = "SELECT status, FromDate, ToDate, PackageId, UserEmail, Comment, MealPlan FROM tblbooking WHERE BookingId = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$booking_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            // Get package name
            $package_sql = "SELECT PackageName FROM tbltourpackages WHERE PackageId = ?";
            $package_stmt = $dbh->prepare($package_sql);
            $package_stmt->execute([$result['PackageId']]);
            $package_result = $package_stmt->fetch(PDO::FETCH_ASSOC);
            $package_name = "Unknown Package";
            if ($package_result) {
                $package_name = $package_result['PackageName'];
            }
            $package_stmt->closeCursor();
            
            // Format dates
            $from_date = date("d M Y", strtotime($result['FromDate']));
            $to_date = date("d M Y", strtotime($result['ToDate']));
            
            // Set status message with more details
            $status_message = "";
            $status_emoji = "";
            if ($result['status'] == 0) {
                $status_message = "PENDING - Your booking is under review by our team";
                $status_emoji = "⏳";
            } else if ($result['status'] == 1) {
                $status_message = "CONFIRMED - Your booking has been approved and is ready for travel";
                $status_emoji = "✅";
            } else if ($result['status'] == 2) {
                $status_message = "CANCELLED - This booking has been cancelled";
                $status_emoji = "❌";
            } else {
                $status_message = "Status: " . $result['status'];
                $status_emoji = "❓";
            }
            
            // Build a more detailed and friendly response
            $response = "I've found your booking details! 📋\n\n";
            $response .= "📦 Package: " . $package_name . "\n";
            $response .= "📅 Travel Dates: " . $from_date . " to " . $to_date . "\n";
            $response .= "🍽️ Meal Plan: " . ($result['MealPlan'] ? $result['MealPlan'] : "Not specified") . "\n";
            $response .= "📝 Comment: " . ($result['Comment'] ? $result['Comment'] : "No comments") . "\n";
            $response .= "📊 Status: " . $status_emoji . " " . $status_message . "\n";
            
            // Add a helpful message based on status
            if ($result['status'] == 0) {
                $response .= "\n💡 Don't worry! We typically process bookings within 24 hours. You'll receive an email confirmation when your booking is confirmed.";
            } else if ($result['status'] == 1) {
                $response .= "\n💡 Great news! Your booking is confirmed! 🎉 Please arrive at the departure point 30 minutes before the scheduled time.";
            } else if ($result['status'] == 2) {
                $response .= "\n💡 If you'd like to make a new booking, I'd be happy to help you find the perfect package!";
            }
        } else {
            $response = "I'm sorry, but I couldn't find a booking with ID " . $booking_id . ". Could you please double-check the ID?";
        }
        $stmt->closeCursor();
    } else {
        $response = "I'd be happy to check your booking status! Could you please provide your Booking ID?";
    }
}

// Handle inquiries with more natural language
elseif (preg_match('/(check|what is|tell me about|status of|my).*(inquiry|enquiry|question)/i', $user_message)) {
    preg_match('/\d+/', $user_message, $matches);
    $inquiry_id = $matches[0] ?? '';
    if ($inquiry_id) {
        $sql = "SELECT * FROM tblenquiry WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$inquiry_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $response = "I've found your inquiry details! 📝\n\n";
            foreach ($result as $key => $value) {
                if ($key != 'id') {
                    $response .= ucfirst($key) . ": " . $value . "\n";
                }
            }
            $response .= "\nIs there anything else you'd like to know?";
        } else {
            $response = "I'm sorry, but I couldn't find an inquiry with ID " . $inquiry_id . ". Could you please double-check the ID?";
        }
        $stmt->closeCursor();
    } else {
        $response = "I'd be happy to check your inquiry! Could you please provide your Inquiry ID?";
    }
}

// Handle reported issues with more natural language
elseif (preg_match('/(check|what is|tell me about|status of|my).*(issue|problem|complaint)/i', $user_message)) {
    preg_match('/\d+/', $user_message, $matches);
    $issue_id = $matches[0] ?? '';
    if ($issue_id) {
        $sql = "SELECT * FROM tblissues WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$issue_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $response = "I've found your reported issue details! 📋\n\n";
            foreach ($result as $key => $value) {
                if ($key != 'id') {
                    $response .= ucfirst($key) . ": " . $value . "\n";
                }
            }
            $response .= "\nIs there anything else you'd like to know about this issue?";
        } else {
            $response = "I'm sorry, but I couldn't find an issue with ID " . $issue_id . ". Could you please double-check the ID?";
        }
        $stmt->closeCursor();
    } else {
        $response = "I'd be happy to check your reported issue! Could you please provide your Issue ID?";
    }
}

// Handle FAQ queries with more natural language
elseif (preg_match('/(faq|frequently asked|help|what can you do|what do you know|what do you help with)/i', $user_message)) {
    $response = "I'd be happy to help! Here are some common questions I can answer: 🤔\n\n";
    $response .= "1. Tour Packages 🎒\n";
    $response .= "   - Show available packages\n";
    $response .= "   - Package details and prices\n";
    $response .= "   - Customer reviews\n\n";
    
    $response .= "2. Bookings 📅\n";
    $response .= "   - Check booking status\n";
    $response .= "   - View booking details\n";
    $response .= "   - Travel dates and meal plans\n\n";
    
    $response .= "3. Inquiries 📝\n";
    $response .= "   - Check inquiry status\n";
    $response .= "   - View inquiry details\n\n";
    
    $response .= "4. Issues 🛠️\n";
    $response .= "   - Check reported issues\n";
    $response .= "   - View issue status\n\n";
    
    $response .= "5. General Information ℹ️\n";
    $response .= "   - Meal plans\n";
    $response .= "   - Booking process\n";
    $response .= "   - Customer support\n\n";
    
    $response .= "You can ask me things like:\n";
    $response .= "• 'Show me available tour packages'\n";
    $response .= "• 'What's the status of my booking? ID: 123'\n";
    $response .= "• 'Tell me about my inquiry ID: 456'\n";
    $response .= "• 'Check my issue ID: 789'\n";
    $response .= "• 'What meal plans are available?'\n\n";
    
    $response .= "What would you like to know more about? 😊";
}

// Handle meal plan queries with more natural language
elseif (preg_match('/(meal plan|mealplans|meal plans|food|dining|meals)/i', $user_message)) {
    $response = "I'd be happy to tell you about our meal plans! 🍽️\n\n";
    $response .= "1. Classic Plan 🍛\n";
    $response .= "   - Basic meals included\n";
    $response .= "   - Standard dining options\n";
    $response .= "   - Perfect for budget-conscious travelers\n\n";
    
    $response .= "2. Premium Plan 🍽️\n";
    $response .= "   - Enhanced dining options\n";
    $response .= "   - More variety in menu\n";
    $response .= "   - Great for food enthusiasts\n\n";
    
    $response .= "3. Deluxe Plan 🍾\n";
    $response .= "   - Luxury dining experience\n";
    $response .= "   - Premium restaurants\n";
    $response .= "   - Perfect for special occasions\n\n";
    
    $response .= "Would you like to know more about any specific meal plan? 😊";
}

// Handle specific package name search
elseif (preg_match('/(is|are|find|search|look for|check|available).*(package|tour).*(available|exist|there)/i', $user_message)) {
    // Extract package name from the message
    preg_match('/(package|tour)\s+([^?]+)/i', $user_message, $matches);
    $package_name = trim($matches[2] ?? '');
    
    if ($package_name) {
        try {
            // First try case-insensitive exact match
            $sql = "SELECT PackageId, PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails, PackageImage 
                    FROM tbltourpackages 
                    WHERE LOWER(PackageName) = LOWER(:pname)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':pname', $package_name, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);
            
            if (count($results) > 0) {
                $response = "Yes, the package '" . $package_name . "' is available! 🎒 Here are the details:\n\n";
                foreach ($results as $row) {
                    $response .= "📦 " . $row->PackageName . "\n";
                    $response .= "📍 Type: " . $row->PackageType . "\n";
                    $response .= "🗺️ Location: " . $row->PackageLocation . "\n";
                    $response .= "💰 Price: $" . $row->PackagePrice . "\n";
                    $response .= "✨ Features: " . $row->PackageFetures . "\n";
                    $response .= "📝 Details: " . $row->PackageDetails . "\n";
                    
                    // Get reviews for this package
                    $review_sql = "SELECT UserName, Rating, ReviewText, ReviewDate FROM tblreviews WHERE PackageId = :pid ORDER BY ReviewDate DESC LIMIT 3";
                    $review_query = $dbh->prepare($review_sql);
                    $review_query->bindParam(':pid', $row->PackageId, PDO::PARAM_INT);
                    $review_query->execute();
                    $review_results = $review_query->fetchAll(PDO::FETCH_OBJ);
                    
                    if (count($review_results) > 0) {
                        $response .= "\n📊 Recent Reviews:\n";
                        foreach ($review_results as $review) {
                            $response .= "👤 " . $review->UserName . " (" . date("d M Y", strtotime($review->ReviewDate)) . ")\n";
                            $response .= "   Rating: ";
                            for ($i = 0; $i < $review->Rating; $i++) {
                                $response .= "★";
                            }
                            for ($i = $review->Rating; $i < 5; $i++) {
                                $response .= "☆";
                            }
                            $response .= "\n   " . $review->ReviewText . "\n\n";
                        }
                    } else {
                        $response .= "\n📊 No reviews yet for this package.\n\n";
                    }
                }
                $response .= "Would you like to know more about this package? Just let me know! 😊";
            } else {
                // If exact match not found, try case-insensitive partial match
                $sql = "SELECT PackageName FROM tbltourpackages WHERE LOWER(PackageName) LIKE LOWER(:pname)";
                $query = $dbh->prepare($sql);
                $search_term = "%" . $package_name . "%";
                $query->bindParam(':pname', $search_term, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                
                if (count($results) > 0) {
                    $response = "Here are some similar packages that might interest you:\n\n";
                    foreach ($results as $row) {
                        $response .= "📦 " . $row->PackageName . "\n";
                    }
                    $response .= "\nWould you like to know more about any of these packages?";
                } else {
                    // Show all available packages
                    $sql = "SELECT PackageName FROM tbltourpackages";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    
                    $response = "Here are our available packages:\n\n";
                    foreach ($results as $row) {
                        $response .= "📦 " . $row->PackageName . "\n";
                    }
                    $response .= "\nWould you like to know more about any of these packages?";
                }
            }
        } catch (PDOException $e) {
            $response = "I'm having trouble accessing the package information right now. Please try again later.";
        }
    } else {
        $response = "I'd be happy to help you find a package! Could you please tell me which package you're looking for?";
    }
}

// Function to check if the message is a general query
function isGeneralQuery($message) {
    $specificPatterns = [
        '/booking/i',
        '/package/i',
        '/tour/i',
        '/status/i',
        '/cancel/i',
        '/price/i',
        '/cost/i',
        '/payment/i',
        '/refund/i',
        '/meal plan/i',
        '/inquiry/i',
        '/issue/i',
        '/problem/i',
        '/complaint/i',
        '/review/i',
        '/rating/i',
        '/feedback/i'
    ];
    
    foreach ($specificPatterns as $pattern) {
        if (preg_match($pattern, $message)) {
            return false;
        }
    }
    return true;
}

// Handle the message
if ($user_message) {
    // Check if it's a general query
    if (isGeneralQuery($user_message)) {
        // Use Gemini AI for general queries
        $response = $gemini->generateResponse($user_message);
    } else {
        // Use existing logic for specific queries
        // ... your existing code for handling specific queries ...
    }
    
    echo json_encode(['response' => $response]);
    exit;
}
