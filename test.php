<?php
	require_once("connect_to_DB.php");
	set_error_handler('errorHandler3');

	function errorHandler3( $errno, $errstr, $errfile, $errline, $errcontext)
	{
		print "<br />error number:".$errno." line:".$errline.": ".$errstr;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Order Form</title>
	<link type="text/css" rel="stylesheet" href="piedmont.css"/>
	<script src="jquery.js"></script>
	<script type="text/javascript">
		/* Validation */
		function validateForm()
		{
			var jAmount = <?php echo json_encode($_POST['amount']) ?>;
			//var orderNumberConfirmation = document.forms["orderform"]["ordernumber"].value;
			var orderDateConfirmation = document.forms["orderform"]["orderdate"].value;
			var totalCost = document.forms["orderform"]["totalorder"].value;
			var alertMessage = "";

			/*if (orderNumberConfirmation == null || orderNumberConfirmation == "")
			{
				alertMessage += "The order number needs to be filled out!\n";
			{}
			else
			{
				if (isNaN(orderNumberConfirmation))
				{
					alertMessage += "The order number must be a number!\n";
				}
			}*/
			if (orderDateConfirmation == null || orderDateConfirmation == "")
			{
				alertMessage += "The order date needs to be filled out!\n";
			}
			if (totalCost == null || totalCost == "")
			{
				alertMessage += "The total order field must be filled out!\n";
			}
			else
			{
				if (isNaN(totalCost))
				{
					alertMessage += "The total cost must be a number!\n";
				}
			}
			for (i = 1; i <= jAmount; i++)
			{
				var quantityConfirmation = document.forms["orderform"]["quantity" + i].value;
				var unitPriceConfirmation = document.forms["orderform"]["unitprice" + i].value;
				var totalPriceConfirmation = document.forms["orderform"]["totalprice" + i].value;

				if (quantityConfirmation == null || quantityConfirmation == "")
				{
					alertMessage += "The quantity for Product " + i + " must be filled out!\n";
				}
				else
				{
					if (isNaN(quantityConfirmation))
					{
						alertMessage += "The quantity for Product " + i + " should be a number!\n";
					}
				}
				if (unitPriceConfirmation == null || unitPriceConfirmation == "")
				{
					alertMessage += "The unit price for Product " + i + " must be filled out!\n";
				}
				else
				{
					if (isNaN(unitPriceConfirmation))
					{
						alertMessage += "The unit price for Product " + i + " should be a number!\n";
					}
				}
				if (totalPriceConfirmation == null || totalPriceConfirmation == "")
				{
					alertMessage += "The total price for Product " + i + " must be filled out!\n";
				}
				else
				{
					if (isNaN(totalPriceConfirmation))
					{
						alertMessage += "The total price for Product " + i + " should be a number!\n";
					}
				}
			}

			if (alertMessage != "")
			{
				alert(alertMessage);
				return false;
			}
		}

		/* AJAX */
		$(document).ready(function(){
			$(".product").change(function(){

				var x = $("#"+$(this).attr("rel"));

				if ($(this).val()=="")
				{
					x.val("");
					return;
				}
				else{
					x.load("getprice.php?q="+$(this).val(), function(responseTxt){
						x.val(responseTxt);
					});
				};

			});
		});

	</script>
</head>
<body>
  <h1>Still running</h1>

  <?php
  // ***** Database access *****
  connectDB();
  $sql0 = "SELECT cust_id, cust_fname, cust_lname FROM customer";
  $sql1 = "SELECT emp_id, emp_fname, emp_lname FROM employee";
  $sql2 = "SELECT product_id, product_name, product_cost FROM product";
  $sql3 = "SELECT status_id, status_type FROM orderstatus";
  $sql4 = "SELECT COUNT(*) FROM salesorder;";

  try {
  	$resultCust = @mysqli_query($db, $sql0); //or die("SQL error: " . mysqli_error());
  	if (!$resultCust) {
  		throw new Exception(mysqli_error($db));
  	}
  	$resultEmp = @mysqli_query($db, $sql1); //or die("SQL error: " . mysqli_error());
  	if (!$resultEmp) {
  		throw new Exception(mysqli_error($db));
  	}
  	$resultProd = @mysqli_query($db, $sql2); //or die("SQL error: " . mysqli_error());
  	if (!$resultProd) {
  		throw new Exception(mysqli_error($db));
  	}
  	$resultStat = @mysqli_query($db, $sql3); //or die("SQL error: " . mysqli_error());
  	if (!$resultStat) {
  		throw new Exception(mysqli_error($db));
  	}
  	$resultOrderID = @mysqli_query($db, $sql4); //or die("SQL error: " . mysqli_error($db));
  	if (!$resultOrderID) {
  		throw new Exception(mysqli_error($db));
  	}
  } catch (Exception $e) {
  	header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
  }
  ?>


  <?php
  if($_GET['action']=="create") {
  ?>

  <h1>New Order Form</h1>

  	<?php
  	if(isset($_POST['amount'])) {
  	?>
  		<form id="orderform" method="post" action="orderconfirmation.php" onsubmit="return validateForm()">
  			<?php
  			$row=mysqli_fetch_array($resultOrderID);
  			$ordernumber = $row[0] + 101;
  			?>
  			<input type="hidden" name="ordernumber" value="<?php echo $ordernumber?>"/>
  			<table>
  				<tr>
  					<td>Order Number:</td>
  					<td><input type="text" name="ordernumberd" value="<?php echo $ordernumber?>" disabled="disabled"/></td>
  					<td>Order Date:</td>
  					<td><input type="text" name="orderdate" value="<?php echo date('Y-m-d')?>"/></td>
  				</tr>
  				<tr>
  					<td>Customer:</td>
  					<td><select name="customer" >
  					<?php while($row = mysqli_fetch_array($resultCust))
  					{  // retrieve each row of the recordset in turn ?>
  						<option value="<?php echo  $row["cust_id"]; ?>">
  						<?php echo  "(" . $row["cust_id"] . ") " . $row["cust_lname"] . ", " . $row["cust_fname"] ?>
  						</option>
  					<?php } ?>
  					</select>
  					</td>
  				</tr>
  				<tr>
  					<td>Sales Agent:</td>
  					<td><select name="salesagent" >
  					<?php while($row = mysqli_fetch_array($resultEmp))
  					{  // retrieve each row of the recordset in turn ?>
  						<option value="<?php echo  $row["emp_id"]; ?>">
  						<?php echo  $row["emp_lname"] . ", " . $row["emp_fname"] ?>
  						</option>
  					<?php } ?>
  					</select>
  					</td>
  					<td>Order status:</td>
  					<td>
  					<select name="status" >
  					<?php while($row = mysqli_fetch_array($resultStat))
  					{  // retrieve each row of the recordset in turn ?>
  						<option value="<?php echo  $row['status_id'];?>">
  						<?php echo  $row['status_type']?>
  						</option>
  					<?php } ?>
  					</select>
  					</td>
  				</tr>
  			</table>

  			<table>
  				<tr>
  					<th>Product</th>
  					<th>Quantity</th>
  					<th>Unit Price</th>
  					<th>Total Price</th>
  				</tr>

  				<?php for ($i = 1; $i <= $_POST['amount']; $i++)
  				{ ?>
  				<tr>
  					<td>
  						<select name="product<?php echo $i?>" class="product" rel="unitprice<?php echo $i?>">
  							<?php while($row = mysqli_fetch_array($resultProd))
  							{  // retrieve each row of the recordset in turn ?>
  								<option value="<?php echo  $row["product_id"]; ?>">
  								<?php echo  $row["product_id"] . ": " . $row["product_name"] ?>
  								</option>
  							<?php } mysqli_data_seek($resultProd,0); ?>
  						</select>
  					</td>
  					<td><input type="text" name="quantity<?php echo $i?>"/></td>
  					<td><input type="text" name="unitprice<?php echo $i?>" id="unitprice<?php echo $i?>"/ value="250.00"></td>
  					<td><input type="text" name="totalprice<?php echo $i?>"/></td>
  				</tr>
  				<?php
  				} ?>
  				<tr>
  					<td colspan="3" align="right">Total Order:</td>
  					<td><input type="text" name="totalorder"/></td>
  				</tr>
  			</table>
  			<div class="submit">
  				<input type="submit" value="Submit"/>
  			</div>

  			<?php//send the count of the products in the order to the confirmation page?>
  			<input type="hidden" name="productcount" value="<?php echo $_POST['amount']?>"/>
  			<input type="hidden" name="action" value="create"/>
  		</form>

  		<!--Debugging purposes only-->
  		<!--
  		<p>SQL statement used to populate order ID field: </p>
  		<p>SQL statement used to populate customer list box: </p>
  		<p>SQL statement used to populate employee list box: </p>
  		<p>SQL statement used to populate order status list box: </p>
  		<p>SQL statement used to populate product list box: </p>
  		-->

  	<?php
  } else {?>



    <?php }
  }?>


</body>
