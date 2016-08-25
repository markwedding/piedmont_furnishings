<?php
	ob_start();
	session_start();
	require_once("connect_to_DB.php");
	include("components.php");
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
	<?php html_head("Customer Confirmation");?>
</head>
<body>
	<?php
	$home_page = "";
	if($_SESSION['job_id'] < 3) {
		$home_page = "mgrhome.php";
	} else {
		$home_page = "home.php";
	}

	html_navbar('plain', $home_page);


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
				VALUES (" . $cid . ",'" . $company . "','" . $lastName . "','" . $firstName . "','" . $address . "','" . $city . "','" . $state . "','" . $zip . "'," .
				$regionID[0] . ",'" . $phone . "','" . $fax . "','" . $email . "');";
		} else {
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
		} ?>

		<div class="container">
			<div class="thin-window">
				<div class="row" style="text-align: center">

					<?php
					if ($result) { ?>
						<p>The customer data was successfully entered into the database. Here is the customer's current information:</p>
					<?php } else { ?>
						<p>Error: The customer data could not be entered into the database. Here is the information you attempted to enter:</p>
					<?php }

					mysqli_free_result($rsRegion);
					mysqli_close($db); ?>

				</div>

				<div class="row">
					<table class="table table-striped table-hover customer-confirm">
						<tr>
							<td><b><?php echo "Customer ID: ";?></b></td>
							<td><?php echo $cid;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Region: ";?></b></td>
							<td><?php echo $region;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Company Name: ";?></b></td>
							<td><?php echo $company;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Last Name: ";?></b></td>
							<td><?php echo $lastName;?></td>
						</tr>
						<tr>
							<td><b><?php echo "First Name: ";?></b></td>
							<td><?php echo $firstName;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Address: ";?></b></td>
							<td><?php echo $address;?></td>
						</tr>
						<tr>
							<td><b><?php echo "City: ";?></b></td>
							<td><?php echo $city;?></td>
						</tr>
						<tr>
							<td><b><?php echo "State: ";?></b></td>
							<td><?php echo $state;?></td>
						</tr>
						<tr>
							<td><b><?php echo "ZIP: ";?></b></td>
							<td><?php echo $zip;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Phone: ";?></b></td>
							<td><?php echo $phone;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Fax: "?></b></td>
							<td><?php echo $fax;?></td>
						</tr>
						<tr>
							<td><b><?php echo "Email: ";?></b></td>
							<td><?php echo $email;?></td>
						</tr>
					</table>
				</div>
				<div class="row" style="text-align:center">
					<a href="<?php echo $home_page;?>">Return to the home page</a>
				</div>

			<!--Debugging purposes only-->
			<!--
			<p>SQL statement used to find the region ID: <?php echo $sqlRegion?></p>
			<p>SQL statement used to insert/update the customer values into the database: <?php echo $sql?></p>
			-->
		</div>
	</div>
</body>
</html>
