<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
	{	
header('location:index.php');
}
else{
if(isset($_REQUEST['bkid']))
	{
		$bid=intval($_GET['bkid']);
$email=$_SESSION['login'];
	$sql ="SELECT FromDate FROM tblbooking WHERE UserEmail=:email and BookingId=:bid";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':bid', $bid, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{
	 $fdate=$result->FromDate;

	$a=explode("/",$fdate);
	$val=array_reverse($a);
	 $mydate =implode("/",$val);
	$cdate=date('Y/m/d');
	$date1=date_create("$cdate");
	$date2=date_create("$fdate");
 $diff=date_diff($date1,$date2);
echo $df=$diff->format("%a");
if($df>1)
{
$status=2;
$cancelby='u';

// Get booking details for cancellation record
$sql1 = "SELECT PackageId FROM tblbooking WHERE UserEmail=:email and BookingId=:bid";
$query1 = $dbh->prepare($sql1);
$query1->bindParam(':email', $email, PDO::PARAM_STR);
$query1->bindParam(':bid', $bid, PDO::PARAM_INT);
$query1->execute();
$booking = $query1->fetch(PDO::FETCH_ASSOC);

// Get package price for refund amount
$sql2 = "SELECT PackagePrice FROM tbltourpackages WHERE PackageId=:pid";
$query2 = $dbh->prepare($sql2);
$query2->bindParam(':pid', $booking['PackageId'], PDO::PARAM_INT);
$query2->execute();
$package = $query2->fetch(PDO::FETCH_ASSOC);

// Insert cancellation record
$sql3 = "INSERT INTO tblcancellations (BookingId, RefundAmount, UserEmail) VALUES (:bid, :refund, :email)";
$query3 = $dbh->prepare($sql3);
$query3->bindParam(':bid', $bid, PDO::PARAM_INT);
$query3->bindParam(':refund', $package['PackagePrice'], PDO::PARAM_STR);
$query3->bindParam(':email', $email, PDO::PARAM_STR);
$query3->execute();

// Update booking status
$sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby WHERE UserEmail=:email and BookingId=:bid";
$query = $dbh->prepare($sql);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query -> bindParam(':cancelby',$cancelby , PDO::PARAM_STR);
$query-> bindParam(':email',$email, PDO::PARAM_STR);
$query-> bindParam(':bid',$bid, PDO::PARAM_STR);
$query -> execute();

$msg="Booking Cancelled successfully";
}
else
{
$error="You can't cancel booking before 24 hours";
}
}
}
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>TMS | Tourism Management System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Tourism Management System In PHP" />
<script type="applijewelleryion/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
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
	<script>
		 new WOW().init();
	</script>

  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>
</head>
<body>
<!-- top-header -->
<div class="top-header">
<?php include('includes/header.php');?>
<div class="banner-1 ">
	<div class="container">
		<h1 class="wow zoomIn animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: zoomIn;">TMS-Tourism Management System</h1>
	</div>
</div>
<!--- /banner-1 ---->
<!--- privacy ---->
<div class="privacy">
	<div class="container">
		<h3 class="wow fadeInDown animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInDown;">My Tour History</h3>
		<form name="chngpwd" method="post" onSubmit="return valid();">
		 <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
	<p>
	<table border="1" width="100%" class="table">
<tr align="center">
<th>#</th>
<th>Booking Id</th>
<th>Package Name</th>	
<th>From</th>
<th>To</th>
<th>Comment</th>
<th>Status</th>
<th>Booking Date</th>
<th>Action</th>
<th>Receipt</th>
</tr>
<?php 

$uemail=$_SESSION['login'];;
$sql = "SELECT tblbooking.BookingId as bookid,tblbooking.PackageId as pkgid,tbltourpackages.PackageName as packagename,tblbooking.FromDate as fromdate,tblbooking.ToDate as todate,tblbooking.Comment as comment,tblbooking.status as status,tblbooking.RegDate as regdate,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate,tbltourpackages.PackagePrice as price from tblbooking join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId where UserEmail=:uemail";
$query = $dbh->prepare($sql);
$query -> bindParam(':uemail', $uemail, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{	?>
<tr align="center">
<td><?php echo htmlentities($cnt);?></td>
<td>#BK<?php echo htmlentities($result->bookid);?></td>
<td><a href="package-details.php?pkgid=<?php echo htmlentities($result->pkgid);?>"><?php echo htmlentities($result->packagename);?></a></td>
<td><?php echo htmlentities($result->fromdate);?></td>
<td><?php echo htmlentities($result->todate);?></td>
<td><?php echo htmlentities($result->comment);?></td>
<td><?php 
if($result->status==0) {
echo "Pending";
}
if($result->status==1) {
echo "Confirmed";
}
if($result->status==2 and $result->cancelby=='u') {
    $refundDate = date('d M Y', strtotime('+7 days'));
    echo "Cancelled by you at " . $result->upddate . "<br><span style='color: #28a745; font-size: 0.9em;'>Your refund will be processed within 7 days (by " . $refundDate . ")</span>";
} 
if($result->status==2 and $result->cancelby=='a') {
    $refundDate = date('d M Y', strtotime('+7 days'));
    echo "Cancelled by admin at " . $result->upddate . "<br><span style='color: #28a745; font-size: 0.9em;'>Your refund will be processed within 7 days (by " . $refundDate . ")</span>";
}
?></td>
<td><?php echo htmlentities($result->regdate);?></td>
<?php if($result->status==2)
{
	?><td>Cancelled</td>
<?php } else {?>
<td><a href="tour-history.php?bkid=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Do you really want to cancel booking')" >Cancel</a></td>
<?php }?>
<td>
<?php if($result->status==1) { ?>
    <a href="javascript:void(0)" onclick="viewReceipt(<?php echo htmlentities($result->bookid); ?>, '<?php echo htmlentities($result->packagename); ?>', '<?php echo htmlentities($result->fromdate); ?>', '<?php echo htmlentities($result->todate); ?>', '<?php echo htmlentities($result->price); ?>', '<?php echo htmlentities($result->regdate); ?>')">View Receipt</a>
<?php } else { ?>
    -
<?php } ?>
</td>
</tr>
<?php $cnt=$cnt+1; }} ?>
	</table>
		
			</p>
			</form>

		
	</div>
</div>
<!--- /privacy ---->
<!--- footer-top ---->
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

<!-- Receipt Modal -->
<div id="receiptModal" class="modal" style="display: none;">
    <div class="modal-content" style="background-color: white; padding: 20px; border-radius: 5px; max-width: 600px; margin: 50px auto;">
        <span class="close" onclick="closeReceiptModal()" style="float: right; cursor: pointer; font-size: 24px;">&times;</span>
        <div id="receiptContent" style="padding: 20px;">
            <!-- Receipt content will be inserted here -->
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <button onclick="printReceipt()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Print Receipt</button>
        </div>
    </div>
</div>

<script>
function viewReceipt(bookingId, packageName, fromDate, toDate, price, bookingDate) {
    const receiptContent = `
        <h2 style="text-align: center; color: #333;">Booking Receipt</h2>
        <hr style="border: 1px solid #ddd;">
        <div style="margin: 20px 0;">
            <p><strong>Booking ID:</strong> #BK${bookingId}</p>
            <p><strong>Package Name:</strong> ${packageName}</p>
            <p><strong>Travel Dates:</strong> ${fromDate} to ${toDate}</p>
            <p><strong>Booking Date:</strong> ${bookingDate}</p>
            <p><strong>Total Amount:</strong> Rs. ${price}</p>
        </div>
        <hr style="border: 1px solid #ddd;">
        <div style="text-align: center; margin-top: 20px;">
            <p>Thank you for choosing our services!</p>
            <p>For any queries, please contact our support team.</p>
        </div>
    `;
    
    document.getElementById('receiptContent').innerHTML = receiptContent;
    document.getElementById('receiptModal').style.display = 'block';
}

function closeReceiptModal() {
    document.getElementById('receiptModal').style.display = 'none';
}

function printReceipt() {
    const receiptContent = document.getElementById('receiptContent').innerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Booking Receipt</title>');
    printWindow.document.write('<style>body { font-family: Arial, sans-serif; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(receiptContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('receiptModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 600px;
    margin: 50px auto;
}

.close {
    float: right;
    cursor: pointer;
    font-size: 24px;
}

@media print {
    .modal-content {
        box-shadow: none;
    }
    .close, button {
        display: none;
    }
}
</style>
</body>
</html>
<?php } ?>