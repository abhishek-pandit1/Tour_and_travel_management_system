<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['submit'])) {
    $pname = $_POST['packagename'];
    $ptype = $_POST['packagetype'];
    $plocation = $_POST['packagelocation'];
    $pprice = $_POST['packageprice'];
    $pfeatures = $_POST['packagefeatures'];
    $pdetails = $_POST['packagedetails'];
    
    // Insert package details into pending table
    $sql = "INSERT INTO tblpendingpackages(PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails) 
            VALUES(:pname, :ptype, :plocation, :pprice, :pfeatures, :pdetails)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pname', $pname, PDO::PARAM_STR);
    $query->bindParam(':ptype', $ptype, PDO::PARAM_STR);
    $query->bindParam(':plocation', $plocation, PDO::PARAM_STR);
    $query->bindParam(':pprice', $pprice, PDO::PARAM_STR);
    $query->bindParam(':pfeatures', $pfeatures, PDO::PARAM_STR);
    $query->bindParam(':pdetails', $pdetails, PDO::PARAM_STR);
    
    if($query->execute()) {
        $msg = "Package submitted successfully! Our team will review it shortly.";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TMS | Submit Package</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link href="css/font-awesome.css" rel="stylesheet">
<script src="js/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
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
</head>
<body>
<?php include('includes/header.php');?>
<div class="banner-1">
    <div class="container">
        <h1 class="wow zoomIn animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: zoomIn;">Submit Your Package</h1>
    </div>
</div>

<div class="container">
    <div class="submit-package">
        <h3>Package Details</h3>
        <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
        else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
        
        <form method="post">
            <div class="form-group">
                <label>Package Name</label>
                <input type="text" class="form-control" name="packagename" required>
            </div>
            
            <div class="form-group">
                <label>Package Type</label>
                <select class="form-control" name="packagetype" required>
                    <option value="">Select Package Type</option>
                    <option value="Group Package">Group Package</option>
                    <option value="Family Package">Family Package</option>
                    <option value="Couple Package">Couple Package</option>
                    <option value="Domestic Packages">Domestic Packages</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Package Location</label>
                <input type="text" class="form-control" name="packagelocation" required>
            </div>
            
            <div class="form-group">
                <label>Package Price</label>
                <input type="number" class="form-control" name="packageprice" required>
            </div>
            
            <div class="form-group">
                <label>Package Features</label>
                <textarea class="form-control" name="packagefeatures" rows="3" required></textarea>
                <small class="form-text text-muted">List the main features of your package (e.g., Free Wi-fi, Free Breakfast, etc.)</small>
            </div>
            
            <div class="form-group">
                <label>Package Details</label>
                <textarea class="form-control" name="packagedetails" rows="5" required></textarea>
                <small class="form-text text-muted">Provide a detailed description of your package</small>
            </div>
            
            <button type="submit" name="submit" class="btn btn-primary">Submit Package</button>
        </form>
    </div>
</div>

<?php include('includes/footer.php');?>
</body>
</html> 