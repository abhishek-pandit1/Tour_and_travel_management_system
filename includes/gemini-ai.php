<?php
// Set custom error log path for XAMPP
ini_set('error_log', 'C:\xampp\apache\logs\error.log');

// Gemini AI Integration
class GeminiAI {
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    private $dbh;

    public function __construct($apiKey) {
        if (empty($apiKey)) {
            throw new Exception("API key is required");
        }
        $this->apiKey = $apiKey;
        
        // Initialize database connection
        try {
            $this->dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
        
        // Log API key status (first few characters only for security)
        error_log("Gemini AI Initialized - API Key: " . substr($apiKey, 0, 5) . "...");
    }

    private function getTourPackages() {
        $sql = "SELECT PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails FROM tbltourpackages";
        $query = $this->dbh->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function searchPackage($searchTerm) {
        $sql = "SELECT PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails 
                FROM tbltourpackages 
                WHERE PackageName LIKE :search 
                OR PackageType LIKE :search 
                OR PackageLocation LIKE :search";
        $query = $this->dbh->prepare($sql);
        $searchParam = "%$searchTerm%";
        $query->bindParam(':search', $searchParam, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getBookingInfo($email) {
        $sql = "SELECT b.BookingId, b.FromDate, b.ToDate, b.status, p.PackageName 
                FROM tblbooking b 
                JOIN tbltourpackages p ON b.PackageId = p.PackageId 
                WHERE b.UserEmail = :email";
        $query = $this->dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getIssueInfo($email) {
        $sql = "SELECT Issue, Description, PostingDate FROM tblissues WHERE UserEmail = :email";
        $query = $this->dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateResponse($prompt, $userEmail = null) {
        try {
            if (empty($prompt)) {
                return "Please provide a question or message.";
            }

            // Get context from database
            $context = "";
            if ($userEmail) {
                $bookings = $this->getBookingInfo($userEmail);
                $issues = $this->getIssueInfo($userEmail);
                
                if (!empty($bookings)) {
                    $context .= "User's Bookings:\n";
                    foreach ($bookings as $booking) {
                        $context .= "- Package: {$booking['PackageName']}, From: {$booking['FromDate']}, To: {$booking['ToDate']}, Status: {$booking['status']}\n";
                    }
                }
                
                if (!empty($issues)) {
                    $context .= "User's Issues:\n";
                    foreach ($issues as $issue) {
                        $context .= "- Issue: {$issue['Issue']}, Description: {$issue['Description']}, Posted: {$issue['PostingDate']}\n";
                    }
                }
            }

            // Get available tour packages
            $packages = $this->getTourPackages();
            if (!empty($packages)) {
                $context .= "Available Tour Packages:\n";
                foreach ($packages as $package) {
                    $context .= "- {$package['PackageName']} ({$package['PackageType']}) at {$package['PackageLocation']} for \${$package['PackagePrice']}\n";
                }
            }

            // Prepare the prompt with context
            $fullPrompt = "Context:\n" . $context . "\n\nUser Question: " . $prompt;
            
            $url = $this->apiUrl . '?key=' . $this->apiKey;
            
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $fullPrompt]
                        ]
                    ]
                ]
            ];

            error_log("Gemini AI Request - URL: " . $url);
            error_log("Gemini AI Request - Prompt: " . $fullPrompt);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $info = curl_getinfo($ch);

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            fclose($verbose);

            curl_close($ch);

            error_log("Gemini AI Response - HTTP Code: " . $httpCode);
            error_log("Gemini AI Response - Info: " . print_r($info, true));
            error_log("Gemini AI Response - Verbose Log: " . $verboseLog);
            error_log("Gemini AI Response - Raw: " . $response);
            
            if ($error) {
                error_log("Gemini AI Error: " . $error);
                return "I apologize, but I'm having trouble connecting to the AI service right now. Please try again later.";
            }

            if ($httpCode !== 200) {
                error_log("Gemini AI Error - HTTP Code: " . $httpCode);
                error_log("Gemini AI Error - Response: " . $response);
                return "I apologize, but I'm having trouble processing your request right now. Please try again later.";
            }

            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Gemini AI Error - JSON Decode: " . json_last_error_msg());
                return "I'm not sure how to respond to that. Could you please rephrase your question?";
            }

            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }

            error_log("Gemini AI Error - Invalid response format: " . print_r($result, true));
            return "I'm not sure how to respond to that. Could you please rephrase your question?";
        } catch (Exception $e) {
            error_log("Gemini AI Error: " . $e->getMessage());
            return "I'm not sure how to respond to that. Could you please rephrase your question?";
        }
    }
}
?> 