<?require_once("connect_to_DB.php");?>
<? 
	set_error_handler('errorHandler5');

	function errorHandler5( $errno, $errstr, $errfile, $errline, $errcontext)
	{
  		print "<br />error number:".$errno." line:".$errline.": ".$errstr;
  	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="piedmont.css" rel="stylesheet" type="text/css"/>
	<title>Customer Order</title>
	<script>
		function validateForm()
		{
			var alertMessage = "";
			
			var customerIDConfirmation = document.forms["form1"]["cid"].value;
			var companyNameConfirmation = document.forms["form1"]["cname"].value;
			var lastNameConfirmation = document.forms["form1"]["lname"].value;
			var firstNameConfirmation = document.forms["form1"]["fname"].value;
			var streetAddressConfirmation = document.forms["form1"]["address"].value;
			var cityConfirmation = document.forms["form1"]["city"].value;
			var zipConfirmation = document.forms["form1"]["zip"].value;
			var phoneConfirmation = document.forms["form1"]["phone"].value;
			var faxConfirmation = document.forms["form1"]["fax"].value;
			var emailConfirmation = document.forms["form1"]["email"].value;
			
			if (customerIDConfirmation == null || customerIDConfirmation == "") alertMessage += "The Customer ID must be filled!\n";
			else {
				if (isNaN(customerIDConfirmation)) alertMessage += "The Customer ID must be a number!\n";
			}
			if (companyNameConfirmation == null || companyNameConfirmation == "") alertMessage += "The Company Name must be filled!\n";
			if (lastNameConfirmation == null || lastNameConfirmation == "") alertMessage += "The Last Name must be filled!\n";
			if (firstNameConfirmation == null || firstNameConfirmation == "") alertMessage += "The First Name must be filled!\n";
			if (streetAddressConfirmation == null || streetAddressConfirmation == "") alertMessage += "The Street Address must be filled!\n";
			if (cityConfirmation == null || cityConfirmation == "") alertMessage += "The City must be filled!\n";
			if (zipConfirmation == null || zipConfirmation == "") alertMessage += "The Zip Code must be filled!\n";
			else {
				if (isNaN(zipConfirmation)) alertMessage += "The Zip Code must be a number!\n";
			}
			if (phoneConfirmation == null || phoneConfirmation == "") alertMessage += "The Phone Number must be filled!\n";
			
			if (alertMessage != "")
			{
				alert(alertMessage);
				return false;
			}
		}
	</script>
</head>
<body>
	
	<?
	connectDB();
	
	$sqlRegion="SELECT region_name FROM region ORDER BY region_id;";
	try {
		$rsRegion=@mysqli_query($db, $sqlRegion); //or die("SQL error: " . mysqli_error($db));
		if (!$rsRegion) {
			throw new Exception(mysqli_error($db));
		}
	} catch (Exception $e) {
		header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
	}

	$states=array("AL","AK","AZ","AR","CA","CO","CT","DE","FL","GA","HI","ID","IL","IN","IA","KS","KY","LA","ME","MD","MA","MI","MN","MS","MO","MT","NE","NV","NH","NJ","NM","NY","NC","ND","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VT","VA","WA","WV","WI","WY");
	?>


	
<?if($_GET['action']=="edit"){

	if(isset($_POST['submitCust'])){?>
	
	<?//*******************EDIT FORM*********************
		$sqlCustData="SELECT c.cust_id,r.region_name,c.cust_company,c.cust_lname,c.cust_fname,c.cust_address,c.cust_city,c.cust_state,c.cust_zip,c.cust_phone,c.cust_fax,c.cust_email 
			FROM customer AS c INNER JOIN region AS r ON c.region_id=r.region_id WHERE c.cust_id=".$_POST['customer'];
		try {
			$rsCustData=@mysqli_query($db, $sqlCustData); //or die("SQL error: " . mysqli_error($db));
			if (!$rsCustData) {
				throw new Exception(mysqli_error($db));  
			}
		} catch (Exception $e) {
			header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
		}
	?>
		
		<h1>Edit Customer Information</h1>
		<div class="custom-form">
			<div class="group">
				<p>Please provide the following information, then click Submit:</p>
				
				<?$datarow=mysqli_fetch_array($rsCustData);?>
				<form name="form1" method="post" action="customerconfirmation.php" onsubmit="return validateForm()">
					<?
					$customerid = $datarow[0];
					?>
					<input type="hidden" name="cid" value="<?=$customerid?>"/>
					<p>Customer ID: <input name="cidd" size="25" value="<?=$customerid?>" disabled="disabled"/>Region: <select name="region"><?
						while($row=mysqli_fetch_array($rsRegion)){?>
							<option <?if($datarow[1]==$row[0]){?>selected="selected"<?}?>><?=$row[0]?></option><?}?>
						</select></p>
					<p>Company Name <input name="cname" size="48" value="<?=$datarow[2]?>"/></p>
					<p>Contact Information:</p>
					<p>	Last Name: <input name="lname" size="32" value="<?=$datarow[3]?>"/></p>
					<p>	First Name: <input name="fname" size="32" value="<?=$datarow[4]?>"/></p>
					<p>Street Address: <input name="address" size="75" value="<?=$datarow[5]?>"/></p>
					<p>City: <input name="city" size="24" value="<?=$datarow[6]?>"/></p>
					<p>State: <select name="state">
						<?for($i=0; $i<count($states); ++$i)
						{?>
							<option <?if($datarow[7]==$states[$i]){?>selected="selected"<?}?>><?=$states[$i]?></option><?echo "\n";
						}?>
						</select></p> 
					<p>ZIP: <input name="zip" size="12" value="<?=$datarow[8]?>"/></p>
					<p>Phone: <input name="phone" size="25" value="<?=$datarow[9]?>"/></p>
					<p>Fax: <input name="fax" size="25" value="<?=$datarow[10]?>"/></p>
					<p>Email: <input name="email" size="25" value="<?=$datarow[11]?>"/></p>
					<? mysqli_free_result($rsCustData);?>
					<input type="hidden" name="action" value="edit"/>
					<p class="button"><input type="submit" value="Submit" />
					<input type="reset" value="Reset" /></p>
				</form>
			</div>
		</div>
		
		<!--Debugging purposes only-->
		<!--
		<p>SQL statement used to populate the fields with customer data: <?=$sqlCustData?></p>
		<p>SQL statement used to populate the region list box: <?=$sqlRegion?></p>
		<p>No SQL statement was used to populate the state list box. It is stored as an array on the PHP page.</p>
		-->
		
		<?
	//*****************END OF EDIT FORM**********************?>
	
	<?} else {
		$sqlCustList="SELECT cust_id, cust_lname, cust_fname FROM customer ORDER BY cust_id";
		try {
			$rsCustList=@mysqli_query($db, $sqlCustList); //or die("SQL error: " . mysqli_error($db));
			if (!$rsCustList) {
				throw new Exception(mysqli_error($db));  
			}
		} catch (Exception $e) {
			header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
		}
	?>
		<div class="custom-form">
			<p>Please select which customer you would like to edit:</p>
			<form name="customerform" method="post" action="customerform.php?action=edit">
				<select name="customer" size="25">
					<?while($row=mysqli_fetch_array($rsCustList)){?>
						<option value="<?=$row[0]?>" <?if($row[0]=="1"){?>selected="selected"<?}?>><?="(".$row[0].") ".$row[1].", ".$row[2]?></option><?echo "\n";}?>
				</select>
				<? mysqli_free_result($rsCustList);?>
				</br>
				<div><input type="submit" value="Submit" name="submitCust"/></div>
				
				<!--Debugging purposes only-->
				<!--
				<p>SQL statement used to populate the customer list box: <?=$sqlCustList?></p>
				-->
			</form><?}?>
		</div>
<?} else {?>
	
	
<?//*******************NEW FORM*********************
	//$sqlID="SELECT cust_id FROM customer ORDER BY cust_id DESC LIMIT 1;";
	$sqlID="SELECT COUNT(*) FROM customer;";
	try {
		$rsID=@mysqli_query($db, $sqlID); //or die("SQL error: " . mysqli_error());
		if (!$rsID) {
			throw new Exception(mysqli_error($db));  
		}
	} catch (Exception $e) {
		header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
	}
?>
	
	<h1>New Customer Entry</h1>
	<div class="custom-form">
		
		<p>Please provide the following information, then click Submit:</p>
		
		<form name="form1" method="post" action="customerconfirmation.php" onsubmit="return validateForm()">
			<?
			$row = mysqli_fetch_array($rsID);
			$customerid = $row[0] + 1;
			?>
			<input type="hidden" name="cid" value="<?=$customerid?>"/>
			<div class="group">
				<p>Customer ID: <input name="cidd" size="25" value="<?=$customerid?>" disabled="disabled"/>Region: <select name="region"><? echo "\n";
					while($row=mysqli_fetch_array($rsRegion)){?>
						<option><?=$row[0]?></option><?echo "\n";}?>
					</select></p>
				<p>Company Name <input name="cname" size="48" /></p>
				<p>Contact Information:</p>
				<p>	Last Name: <input name="lname" size="32"/></p>
				<p>	First Name: <input name="fname" size="32"/></p>
				<p>Street Address: <input name="address" size="75"/></p>
				<p>City: <input name="city" size="24"/></p>
				<p>State: <select name="state">
					<?for($i=0;$i<count($states);++$i){?>
						<option><?=$states[$i]?></option><?echo "\n";}?>
					</select></p> 
				<p>ZIP: <input name="zip" size="12"/></p>
				<p>Phone: <input name="phone" size="25"/></p>
				<p>Fax: <input name="fax" size="25"/></p>
				<p>Email: <input name="email" size="25"/></p>
			</div>
			<input type="hidden" name="action" value="create"/>
			<p class="button"><input type="submit" value="Submit" />
			<input type="reset" value="Reset" /></p>
		</form>
		
	</div>
	
	<!--Debugging purposes only-->
	<!--
	<p>SQL statement used to populate the customer ID text box: <?=$sqlID?></p>
	<p>SQL statement used to populate the region list box: <?=$sqlRegion?></p>
	<p>No SQL statement was used to populate the state list box. It is stored as an array on the PHP page.</p>
	-->
	
	<?mysqli_free_result($rsID);
//*****************END OF NEW FORM**********************?>
<?}?>
	
	
	
	
	<?
	mysqli_free_result($rsRegion);
	mysqli_close($db);
	?>
	
</body>
</html>