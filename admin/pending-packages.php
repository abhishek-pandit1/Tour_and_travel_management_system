<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else{
    if(isset($_GET['approve'])){
        $id=intval($_GET['approve']);
        
        // Get pending package details
        $sql = "SELECT * FROM tblpendingpackages WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id',$id,PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        
        if($result){
            // Insert into main packages table
            $sql = "INSERT INTO tbltourpackages(PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails) 
                    VALUES(:pname, :ptype, :plocation, :pprice, :pfeatures, :pdetails)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':pname', $result->PackageName, PDO::PARAM_STR);
            $query->bindParam(':ptype', $result->PackageType, PDO::PARAM_STR);
            $query->bindParam(':plocation', $result->PackageLocation, PDO::PARAM_STR);
            $query->bindParam(':pprice', $result->PackagePrice, PDO::PARAM_STR);
            $query->bindParam(':pfeatures', $result->PackageFetures, PDO::PARAM_STR);
            $query->bindParam(':pdetails', $result->PackageDetails, PDO::PARAM_STR);
            $query->execute();
            
            // Delete from pending table
            $sql = "DELETE FROM tblpendingpackages WHERE id=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id',$id,PDO::PARAM_STR);
            $query->execute();
            
            $msg = "Package approved successfully";
        }
    }
    
    if(isset($_GET['reject'])){
        $id=intval($_GET['reject']);
        $sql = "DELETE FROM tblpendingpackages WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id',$id,PDO::PARAM_STR);
        $query->execute();
        $msg = "Package rejected successfully";
    }
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TMS | Pending Packages</title>
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
</head>
<body>
<?php include('includes/header.php');?>
<div class="banner-1">
    <div class="container">
        <h1 class="wow zoomIn animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: zoomIn;">Pending Packages</h1>
    </div>
</div>

<div class="container">
    <div class="pending-packages">
        <h3>Pending Package Submissions</h3>
        <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
        
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Package Name</th>
                    <th>Package Type</th>
                    <th>Package Location</th>
                    <th>Package Price</th>
                    <th>Features</th>
                    <th>Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT * FROM tblpendingpackages";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt=1;
                if($query->rowCount() > 0) {
                    foreach($results as $result) { ?>
                        <tr>
                            <td><?php echo htmlentities($cnt);?></td>
                            <td><?php echo htmlentities($result->PackageName);?></td>
                            <td><?php echo htmlentities($result->PackageType);?></td>
                            <td><?php echo htmlentities($result->PackageLocation);?></td>
                            <td>$<?php echo htmlentities($result->PackagePrice);?></td>
                            <td><?php echo htmlentities($result->PackageFetures);?></td>
                            <td><?php echo htmlentities($result->PackageDetails);?></td>
                            <td>
                                <a href="pending-packages.php?approve=<?php echo $result->id;?>" onclick="return confirm('Do you want to approve this package?')">
                                    <button class="btn btn-primary">Approve</button>
                                </a>
                                <a href="pending-packages.php?reject=<?php echo $result->id;?>" onclick="return confirm('Do you want to reject this package?')">
                                    <button class="btn btn-danger">Reject</button>
                                </a>
                            </td>
                        </tr>
                    <?php $cnt++; }
                } else { ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No pending packages found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('includes/footer.php');?>
</body>
</html>
<?php } ?> 