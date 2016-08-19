<?php require_once("connect_to_DB.php");?>
<?php session_start();?>
<?php
	set_error_handler('errorHandler4');

	function errorHandler4( $errno, $errstr, $errfile, $errline, $errcontext)
	{
		print "<br />error number:".$errno." line:".$errline.": ".$errstr;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Customer Confirmation</title>
	<link href="piedmont.css" rel="stylesheet" type="text/css">
</head>
<body>
<h2>Information Confirmation</h2>
<div class="menu"><ul><li><a href="<?php if($_SESSION['job_id'] <= 2){echo "mgr";}?>home.php">Home</a></li></ul></div><br></br>
<?php

$cid= $_POST['cid'];
$region= $_POST['region'];
$company= $_POST['cname'];
$lastName= $_POST['lname'];
$firstName= $_POST['fname'];
$address = $_POST['address'];
$city= $_POST['city'];
$state= $_POST['state'];
$zip= $_POST['zip'];
$phone= $_POST['phone'];
$fax= $_POST['fax'];
$email= $_POST['email'];

connectDB();

$sqlRegion = "SELECT region_id FROM region WHERE region_name='" . $region . "';";

try {
	$rsRegion = @mysqli_query($db, $sqlRegion); //or die("SQL error: " . mysqli_error($db));
	if (!$rsRegion) {
		throw new Exception(mysqli_error($db));
	}

	$regionID = mysqli_fetch_array($rsRegion);

	if($_POST['action']=='create'){
		$sql = "INSERT INTO customer (cust_id, cust_company, cust_lname, cust_fname, cust_address, cust_city, cust_state, cust_zip, region_id, cust_phone, cust_fax, cust_email)
			VALUES (" . $cid . ",'" . $company . "','" . $lastName . "','" . $firstName . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "'," . $regionID[0] . ",'" . $phone . "','" . $fax . "','" . $email . "');";
	} else
	{
		$sql = "UPDATE customer SET cust_company='" . $company . "', cust_lname='" . $lastName . "', cust_fname='" . $firstName . "', cust_address='" . $address. "',
			cust_city='" . $city . "', cust_state='" . $state . "', cust_zip='" . $zip . "', region_id=" . $regionID[0] . ", cust_phone='" . $phone . "', cust_fax='" . $fax . "', cust_email='" . $email . "'
			WHERE cust_id=" . $cid . ";";
	}

	$result = @mysqli_query($db, $sql); //or die("SQL error: " . mysqli_error($db));

	if (!$result) {
		throw new Exception(mysqli_error($db));
	}

} catch (Exception $e) {
	header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
}
if($result){?>
	<p>The customer data was successfully entered into the database.</p>
<?php } else {?>
	<p>The customer data was not entered into the database.</p>
<?php }
mysqli_free_result($rsRegion);
mysqli_close($db);

?>
<table>
	<tr>
		<td><b><?php print "Customer ID: ";?></b></td>
		<td><?php print $cid;?></td>
	</tr>
	<tr>
		<td><b><?php print "Region: ";?></b></td>
		<td><?php print $region;?></td>
	</tr>
	<tr>
		<td><b><?php print "Company Name: ";?></b></td>
		<td><?php print $company;?></td>
	</tr>
	<tr>
		<td colspan="2"><b><?php print "Contact Information: ";?></b></td>
	</tr>
	<tr>
		<td><b><?php print "Last Name: ";?></b></td>
		<td><?php print $lastName;?></td>
	</tr>
	<tr>
		<td><b><?php print "First Name: ";?></b></td>
		<td><?php print $firstName;?></td>
	</tr>
	<tr>
		<td><b><?php print "Address: ";?></b></td>
		<td><?php print $address;?></td>
	</tr>
	<tr>
		<td><b><?php print "City: ";?></b></td>
		<td><?php print $city;?></td>
	</tr>
	<tr>
		<td><b><?php print "State: ";?></b></td>
		<td><?php print $state;?></td>
	</tr>
	<tr>
		<td><b><?php print "ZIP: ";?></b></td>
		<td><?php print $zip;?></td>
	</tr>
	<tr>
		<td><b><?php print "Phone: ";?></b></td>
		<td><?php print $phone;?></td>
	</tr>
	<tr>
		<td><b><?php print "Fax: "?></b></td>
		<td><?php print $fax;?></td>
	</tr>
	<tr>
		<td><b><?php print "Email: ";?></b></td>
		<td><?php print $email;?></td>
	</tr>
</table>

<!--Debugging purposes only-->
<!--
<p>SQL statement used to find the region ID: <?php echo $sqlRegion?></p>
<p>SQL statement used to insert/update the customer values into the database: <?php echo $sql?></p>
-->
</body>
</html>
