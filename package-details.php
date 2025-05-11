<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('razorpay-config.php');

if(isset($_POST['submit2']))
{
    $pid=intval($_GET['pkgid']);
    $useremail=$_SESSION['login'];
    $fromdate=$_POST['fromdate'];
    $todate=$_POST['todate'];
    $comment=$_POST['comment'];
    $mealplan=$_POST['meal_plan'];
    $meal_cost=$_POST['meal_cost'];
    $status=0;
    
    // Get meal plan ID based on selected plan
    $mealPlanSql = "SELECT MealPlanId, MealPlanPrice FROM tblmealplans WHERE MealPlanName = :mealplan";
    $mealPlanQuery = $dbh->prepare($mealPlanSql);
    $mealPlanQuery->bindParam(':mealplan', $mealplan, PDO::PARAM_STR);
    $mealPlanQuery->execute();
    $mealPlanResult = $mealPlanQuery->fetch(PDO::FETCH_ASSOC);
    
    $mealPlanId = $mealPlanResult['MealPlanId'];
    $mealPlanPrice = $mealPlanResult['MealPlanPrice'];
    
    // Debug information
    error_log("Booking attempt - Package ID: $pid, User Email: $useremail, From Date: $fromdate, To Date: $todate, Meal Plan: $mealplan, Meal Cost: $meal_cost");
    
    $sql="INSERT INTO tblbooking(PackageId, UserEmail, FromDate, ToDate, Comment, MealPlanId, MealPlanPrice, status, PaymentId) VALUES(:pid,:useremail,:fromdate,:todate,:comment,:mealplanid,:mealplanprice,:status,:payment_id)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid', $pid, PDO::PARAM_INT);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $query->bindParam(':todate', $todate, PDO::PARAM_STR);
    $query->bindParam(':comment', $comment, PDO::PARAM_STR);
    $query->bindParam(':mealplanid', $mealPlanId, PDO::PARAM_INT);
    $query->bindParam(':mealplanprice', $mealPlanPrice, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->bindParam(':payment_id', $razorpay_payment_id, PDO::PARAM_STR);
    
    if($query->execute()) {
        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId) {
            $msg="Booked Successfully";
        } else {
            $error="Something went wrong. Please try again";
        }
    } else {
        $error="Database error: " . implode(", ", $query->errorInfo());
    }
}

if(isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
    $useremail = $_SESSION['login'];
    $pid = intval($_GET['pkgid']);
    
    // Get user's name from tblusers
    $userSql = "SELECT FullName FROM tblusers WHERE EmailId=:email";
    $userQuery = $dbh->prepare($userSql);
    $userQuery->bindParam(':email', $useremail, PDO::PARAM_STR);
    $userQuery->execute();
    $userResult = $userQuery->fetch(PDO::FETCH_OBJ);
    $userName = $userResult->FullName;
    
    $reviewSql = "INSERT INTO tblreviews (PackageId, UserName, Rating, ReviewText) VALUES (:pid, :username, :rating, :reviewtext)";
    $reviewQuery = $dbh->prepare($reviewSql);
    $reviewQuery->bindParam(':pid', $pid, PDO::PARAM_INT);
    $reviewQuery->bindParam(':username', $userName, PDO::PARAM_STR);
    $reviewQuery->bindParam(':rating', $rating, PDO::PARAM_INT);
    $reviewQuery->bindParam(':reviewtext', $review_text, PDO::PARAM_STR);
    
    if($reviewQuery->execute()) {
        $msg = "Review submitted successfully!";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}

// Get package details
$pid = intval($_GET['pkgid']);
$sql = "SELECT * from tbltourpackages where PackageId=:pid";
$query = $dbh->prepare($sql);
$query->bindParam(':pid', $pid, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TMS | Package Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link href="css/font-awesome.css" rel="stylesheet">
<!-- Custom Theme files -->
<script src="js/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />
<script>
    new WOW().init();
</script>
<script src="js/jquery-ui.js"></script>
<style>
    .errorWrap {
        padding: 10px;
        margin: 0 0 20px 0;
        background: #fff;
        border-left: 4px solid #dd3d36;
        -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    }
    .succWrap {
        padding: 10px;
        margin: 0 0 20px 0;
        background: #fff;
        border-left: 4px solid #5cb85c;
        -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    }
</style>
<!-- Add Razorpay script in the head section -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- Add this script to initialize Razorpay -->
<script>
// Store Razorpay key ID in a JavaScript variable
const razorpayKeyId = "<?php echo $razorpay_key_id; ?>";
</script>
</head>
<body>
<!-- top-header -->
<?php include('includes/header.php');?>
<div class="banner-3">
    <div class="container">
        <h1 class="wow zoomIn animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: zoomIn;"> TMS -Package Details</h1>
    </div>
</div>
<!--- /banner ---->
<!--- selectroom ---->
<div class="selectroom">
    <div class="container">    
        <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
        else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
        <?php 
        $pid=intval($_GET['pkgid']);
        $sql = "SELECT PackageId, PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails, PackageImage from tbltourpackages where PackageId=:pid";
        $query = $dbh->prepare($sql);
        $query -> bindParam(':pid', $pid, PDO::PARAM_INT);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        $cnt=1;
        if($query->rowCount() > 0)
        {
            foreach($results as $result)
            { ?>
                <div class="selectroom_top">
                    <div class="col-md-4 selectroom_left wow fadeInLeft animated" data-wow-delay=".5s">
                        <img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage);?>" class="img-responsive" alt="">
                    </div>
                    <div class="col-md-8 selectroom_right wow fadeInRight animated" data-wow-delay=".5s">
                        <h2><?php echo htmlentities($result->PackageName);?></h2>
                        <p class="dow">#PKG-<?php echo htmlentities($result->PackageId);?></p>
                        
                        <!-- Package Details moved here -->
                        <h3>Package Details</h3>
                        <p style="padding-top: 1%"><?php echo htmlentities($result->PackageDetails);?> </p>    
                        <div class="clearfix"></div>
                        
                        <p><b>Package Type :</b> <?php echo htmlentities($result->PackageType);?></p>
                        <p><b>Package Location :</b> <?php echo htmlentities($result->PackageLocation);?></p>
                        <p><b>Features</b> <?php echo htmlentities($result->PackageFetures);?></p>
                        
                        <!-- Meal Selection Section -->
                        <div class="meal-selection" style="margin-top: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                            <h3 style="margin-bottom: 15px;">Meal Plans</h3>
                            <p>Select your preferred meal plan for your stay:</p>
                            
                            <div class="meal-options">
                                <div class="meal-option" style="margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                                    <label style="display: block; margin-bottom: 5px;">
                                        <input type="radio" name="meal_plan" value="Classic Plan" class="meal-radio" data-price="500"> 
                                        <strong>Classic Plan</strong> - Rs. 500 per day
                                    </label>
                                    <p style="margin-left: 25px; color: #666;">Includes: Breakfast only</p>
                                </div>
                                
                                <div class="meal-option" style="margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                                    <label style="display: block; margin-bottom: 5px;">
                                        <input type="radio" name="meal_plan" value="Premium Plan" class="meal-radio" data-price="1000"> 
                                        <strong>Premium Plan</strong> - Rs. 1000 per day
                                    </label>
                                    <p style="margin-left: 25px; color: #666;">Includes: Breakfast and Lunch</p>
                                </div>
                                
                                <div class="meal-option" style="margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                                    <label style="display: block; margin-bottom: 5px;">
                                        <input type="radio" name="meal_plan" value="Deluxe Plan" class="meal-radio" data-price="1500"> 
                                        <strong>Deluxe Plan</strong> - Rs. 1500 per day
                                    </label>
                                    <p style="margin-left: 25px; color: #666;">Includes: Breakfast, Lunch, and Dinner</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reviews Section -->
                        <div class="reviews-section" style="margin-top: 20px;">
                            <h3 style="margin-bottom: 15px;">Customer Reviews</h3>
                            <?php
                            // Fetch reviews for this package
                            $reviewSql = "SELECT * FROM tblreviews WHERE PackageId=:pid ORDER BY ReviewDate DESC";
                            $reviewQuery = $dbh->prepare($reviewSql);
                            $reviewQuery->bindParam(':pid', $pid, PDO::PARAM_INT);
                            $reviewQuery->execute();
                            $reviews = $reviewQuery->fetchAll(PDO::FETCH_OBJ);
                            
                            if($reviewQuery->rowCount() > 0) {
                                foreach($reviews as $review) {
                            ?>
                                <div class="review-box" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                                    <div class="review-header" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                        <span style="font-weight: bold;"><?php echo htmlentities($review->UserName); ?></span>
                                        <span style="color: #666;"><?php echo date('d M Y', strtotime($review->ReviewDate)); ?></span>
                                    </div>
                                    <div class="review-rating" style="margin-bottom: 10px;">
                                        <?php
                                        // Display stars based on rating
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= $review->Rating) {
                                                echo '<span style="color: #ffd700;">★</span>';
                                            } else {
                                                echo '<span style="color: #ddd;">★</span>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="review-content">
                                        <p><?php echo htmlentities($review->ReviewText); ?></p>
                                    </div>
                                </div>
                            <?php
                                }
                            } else {
                                echo '<p style="color: #666; font-style: italic;">No reviews yet. Be the first to review this package!</p>';
                            }
                            ?>
                        </div>
                        
                        <!-- Review Submission Form -->
                        <?php if(isset($_SESSION['login'])) { ?>
                        <div class="review-form" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
                            <h3 style="margin-bottom: 15px;">Write a Review</h3>
                            <form method="post" action="">
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 5px;">Your Rating:</label>
                                    <div class="rating" style="font-size: 24px;">
                                        <?php for($i = 1; $i <= 5; $i++) { ?>
                                            <span class="star" data-rating="<?php echo $i; ?>" style="cursor: pointer; color: #ddd;">★</span>
                                        <?php } ?>
                                    </div>
                                    <input type="hidden" name="rating" id="rating" value="0">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; margin-bottom: 5px;">Your Review:</label>
                                    <textarea name="review_text" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" rows="4" required></textarea>
                                </div>
                                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                        <?php } else { ?>
                            <div style="margin-top: 20px; text-align: center;">
                                <p>Please <a href="#" data-toggle="modal" data-target="#myModal4">login</a> to write a review.</p>
                            </div>
                        <?php } ?>
                        
                        <!-- Booking Form -->
                        <div class="selectroom_top">
                            <form name="book" method="post">
                                <div class="ban-bottom">
                                    <div class="bnr-right">
                                        <label class="inputLabel">Start Date</label>
                                        <input class="date" id="datepicker" type="text" placeholder="dd-mm-yyyy" name="fromdate" required="">
                                    </div>
                                    <div class="bnr-right">
                                        <label class="inputLabel">End Date</label>
                                        <input class="date" id="datepicker1" type="text" placeholder="dd-mm-yyyy" name="todate" required="">
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="grand">
                                    <p>Grand Total</p>
                                    <h3>Rs. <?php echo htmlentities($result->PackagePrice);?></h3>
                                </div>
                                <div class="selectroom-info animated wow fadeInUp animated" data-wow-duration="1200ms" data-wow-delay="500ms" style="visibility: visible; animation-duration: 1200ms; animation-delay: 500ms; animation-name: fadeInUp;">
                                    <ul>
                                        <li class="spe">
                                            <label class="inputLabel">Comment</label>
                                            <input class="special" type="text" name="comment" required="">
                                        </li>
                                        <?php if($_SESSION['login']) { ?>
                                            <li class="spe" align="center">
                                                <button type="button" id="rzp-button" class="btn-primary btn">Pay & Book</button>
                                            </li>
                                        <?php } else { ?>
                                            <li class="sigi" align="center" style="margin-top: 1%">
                                                <a href="#" data-toggle="modal" data-target="#myModal4" class="btn-primary btn"> Book</a>
                                            </li>
                                        <?php } ?>
                                        <div class="clearfix"></div>
                                    </ul>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php }
        } ?>
    </div>
</div>
<!--- /selectroom ---->
<!--- /footer-top ---->
<?php include('includes/footer.php');?>
<!-- signup -->
<?php include('includes/signup.php');?>            
<!-- //signu -->
<!-- signin -->
<?php include('includes/signin.php');?>            
<!-- //signin -->
<!-- write us -->
<?php include('includes/write-us.php');?>
<script>
$(function() {
    // Get current date and set time to midnight
    var currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0);
    
    // Calculate next month's last day
    var nextMonth = new Date();
    nextMonth.setMonth(nextMonth.getMonth() + 1); // Set to next month
    nextMonth.setDate(1); // Set to first day of next month
    nextMonth.setHours(0, 0, 0, 0);
    
    // Initialize datepicker with custom settings
    $("#datepicker,#datepicker1").datepicker({
        minDate: currentDate,  // Set to current date
        maxDate: nextMonth,    // Set to first day of next month
        dateFormat: 'dd-mm-yy',
        changeMonth: true,    // Allow month selection
        changeYear: false,    // Disable year selection
        yearRange: 'c:c+1',   // Only show current year
        showButtonPanel: true, // Show button panel
        showAnim: 'slideDown', // Animation effect
        showOtherMonths: true, // Show dates from other months
        selectOtherMonths: true, // Allow selecting dates from other months
        beforeShowDay: function(date) {
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var isSelectable = date >= today && date <= nextMonth; // Allow selection only for current and next month
            return [isSelectable, isSelectable ? '' : 'ui-state-disabled'];
        }
    });
    
    // Add custom CSS for disabled dates and month navigation
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .ui-state-disabled {
                cursor: not-allowed !important;
                opacity: 0.5;
                color: #999;
            }
            .ui-datepicker-calendar td:not(.ui-state-disabled) {
                cursor: pointer;
            }
            .ui-datepicker-calendar td.ui-state-disabled a {
                color: #999;
            }
            .ui-datepicker-month {
                pointer-events: none;
            }
            .ui-datepicker-year {
                display: none;
            }
            .ui-datepicker-prev, .ui-datepicker-next {
                display: none !important;
            }
            .ui-datepicker-title {
                text-align: center;
                font-weight: bold;
            }
            .ui-datepicker-current-day {
                background-color: #007bff;
                color: white;
            }
            .ui-datepicker-current-day a {
                color: white !important;
            }
        `)
        .appendTo('head');
    
    // Function to calculate days between two dates
    function calculateDays() {
        var fromDate = $("#datepicker").val();
        var toDate = $("#datepicker1").val();
        
        if(fromDate && toDate) {
            var start = new Date(fromDate.split("-").reverse().join("-"));
            var end = new Date(toDate.split("-").reverse().join("-"));
            var diffTime = Math.abs(end - start);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            // Store total days in a variable
            var totalDays = diffDays;
            
            // Get the package price
            var packagePrice = <?php echo htmlentities($result->PackagePrice); ?>;
            
            // Get the meal cost
            var mealCost = 0;
            var selectedMealPlan = $('input[name="meal_plan"]:checked').val();
            if (selectedMealPlan) {
                mealCost = $('input[name="meal_plan"]:checked').data('price');
            }
            
            // Calculate the total cost
            var mealTotal = mealCost * totalDays;
            var grandTotal = packagePrice + mealTotal;
            
            // Display the total days and updated grand total
            $(".grand p").html("Total Days: " + totalDays + " | Package Price: Rs. " + packagePrice + " | Meal Cost: Rs. " + mealTotal + " | Grand Total");
            $(".grand h3").html("Rs. " + grandTotal);
            
            // Update the total amount display
            $("#total-amount").text("₹" + grandTotal);
        }
    }
    
    // Calculate days when either date changes
    $("#datepicker, #datepicker1").change(function() {
        calculateDays();
    });
    
    // Handle meal plan selection
    $('input[name="meal_plan"]').change(function() {
        calculateDays();
    });
});

$(document).ready(function() {
    // Star rating functionality
    $('.star').click(function() {
        var rating = $(this).data('rating');
        $('#rating').val(rating);
        
        // Update star colors
        $('.star').each(function() {
            if($(this).data('rating') <= rating) {
                $(this).css('color', '#ffd700');
            } else {
                $(this).css('color', '#ddd');
            }
        });
    });

    // Update the payment button click handler
    $('#rzp-button').click(function() {
        var fromDate = $('#datepicker').val();
        var toDate = $('#datepicker1').val();
        var comment = $('input[name="comment"]').val();
        var mealPlan = $('input[name="meal_plan"]:checked').val();
        var mealCost = $('input[name="meal_plan"]:checked').data('price');
        
        if (!fromDate || !toDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        if (!mealPlan) {
            alert('Please select a meal plan');
            return;
        }
        
        // Calculate total days and meal cost
        var start = new Date(fromDate.split("-").reverse().join("-"));
        var end = new Date(toDate.split("-").reverse().join("-"));
        var diffTime = Math.abs(end - start);
        var totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        var totalMealCost = mealCost * totalDays;
        
        // Create order on your server
        $.ajax({
            url: 'create-razorpay-order.php',
            type: 'POST',
            data: {
                amount: <?php echo htmlentities($result->PackagePrice); ?> + totalMealCost,
                fromdate: fromDate,
                todate: toDate,
                comment: comment,
                package_id: <?php echo htmlentities($result->PackageId); ?>,
                meal_plan: mealPlan,
                meal_cost: totalMealCost
            },
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    return;
                }
                
                var options = {
                    "key": razorpayKeyId,
                    "amount": response.amount,
                    "currency": "INR",
                    "name": "Tour Management System",
                    "description": "Package Booking Payment",
                    "image": "images/logo.png",
                    "order_id": response.id,
                    "handler": function (response) {
                        // Handle successful payment
                        $.ajax({
                            url: 'verify-payment.php',
                            type: 'POST',
                            data: {
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,
                                fromdate: fromDate,
                                todate: toDate,
                                comment: comment,
                                package_id: <?php echo htmlentities($result->PackageId); ?>,
                                meal_plan: mealPlan,
                                meal_cost: totalMealCost
                            },
                            success: function(result) {
                                if(result.success) {
                                    alert('Booking successful! Your booking ID is: ' + result.booking_id);
                                    window.location.href = 'tour-history.php';
                                } else {
                                    alert('Payment verification failed: ' + (result.error || 'Unknown error'));
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Verification Error:', error);
                                alert('Payment verification failed. Please contact support.');
                            }
                        });
                    },
                    "prefill": {
                        "name": "<?php echo $_SESSION['login']; ?>",
                        "email": "<?php echo $_SESSION['login']; ?>"
                    },
                    "theme": {
                        "color": "#F37254"
                    }
                };
                
                var rzp = new Razorpay(options);
                rzp.on('payment.failed', function (response) {
                    console.error('Payment Failed:', response);
                    alert('Payment failed. Please try again.');
                });
                rzp.open();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while creating the order. Please try again.');
            }
        });
    });
});
</script>
</body>
</html>