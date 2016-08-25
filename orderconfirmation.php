<?php
	ob_start();
	session_start();
	include("connect_to_DB.php");
	include("components.php");

	set_error_handler('errorHandler7');

	function errorHandler7( $errno, $errstr, $errfile, $errline, $errcontext)
	{
  		print "<br />error number:".$errno." line:".$errline.": ".$errstr;
  	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php html_head("Order Form Confirmation");?>
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
	?>

	<?php
	$count = $_POST['productcount'];
	$orderNum = $_POST['ordernumber'];
	$orderDate = $_POST['orderdate'];
	$customer = $_POST['customer'];
	$salesagent = $_POST['salesagent'];
	$status = $_POST['status'];

	$product = array();
	$quantity = array();
	$unitprice = array();
	$totalprice = array();

	for($i = 0; $i < $count; ++$i){
		$product[$i] = $_POST['product' . ($i + 1)];
		$quantity[$i] = $_POST['quantity' . ($i + 1)];
		$unitprice[$i] = $_POST['unitprice' . ($i + 1)];
		$totalprice[$i] = $_POST['totalprice' . ($i + 1)];
	}

	connectDB();

	if($_POST['action']=="create"){
		$sqlOrder = "INSERT INTO salesorder (order_id, cust_id, order_date, emp_id, status_id)
			VALUES (" . $orderNum . ", " . $customer . ", '" . $orderDate . "', " . $salesagent . ", " . $status . ");";

		$sqlItem = array();

		for($i = 0; $i < $count; ++$i){
			$sqlItem[$i] = "INSERT INTO orderitem (order_id, item_linenum, product_id, item_quantity, item_unitprice)
				VALUES (" . $orderNum . ", " . ($i + 1) . ", '" . $product[$i] . "', " . $quantity[$i] . ", " . $unitprice[$i] . ");";
		}
	} else{
		$sqlOrder = "UPDATE salesorder SET cust_id=" . $customer . ", order_date='" . $orderDate . "', emp_id=" . $salesagent . ", status_id=" . $status . "
			WHERE order_id=" . $orderNum . ";";

		$sqlItem = array();

		for($i = 0; $i < $count; ++$i){
			$sqlItem[$i] = "UPDATE orderitem SET product_id='" . $product[$i] . "', item_quantity=" . $quantity[$i] . ", item_unitprice=" . $unitprice[$i] . "
				WHERE order_id=" . $orderNum . " AND item_linenum=" . ($i + 1) . ";";
		}

	}

	try {
		$orderResult = @mysqli_query($db, $sqlOrder); //or die("SQL error: " . mysqli_error($db));

		if (!$orderResult) {
			throw new Exception(mysqli_error($db));
		}

		$itemResult = array();

		for($i = 0; $i < $count; ++$i){
			$itemResult[$i] = mysqli_query($db, $sqlItem[$i]); //or die("SQL error: " . mysqli_error($db));
			if (!$itemResult) {
				throw new Exception(mysqli_error($db));
			}
		}
	} catch (Exception $e) {
		header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
	}


	$totalItemResult = true;
	for($i = 0; $i < $count; ++$i){
		if($itemResult[$i]==false){
			$totalItemResult = false;
			break;
		}
	}?>

	<div class="container">
		<div class="row" style="text-align:center">

	<?php
	if($orderResult and $totalItemResult){?>
		<p>The order form data was successfully entered into the database. Here is a summary of the order:</p>
	<?php } else {?>
		<p>Here is a summary of the order:</p>
	<?php }

	mysqli_close($db);

	?>
		</div>
	<div class="row">
		<div class="col-lg-4">
			<table class="order-confirm table">
				<tbody>
					<tr>
						<td class="heading">Order Number:</td>
						<td><?php print $orderNum;?></td>
					</tr>
					<tr>
						<td class="heading">Order Date:</td>
						<td><?php print $orderDate;?></td>
					</tr>
					<tr>
						<td class="heading">Customer:</td>
						<td><?php print $customer;?></td>
					</tr>
					<tr>
						<td class="heading">Sales Agent:</td>
						<td><?php print $salesagent;?></td>
					</tr>
					<tr>
						<td class="heading">Order status:</td>
						<td><?php print $status;?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-lg-8">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Product</th>
						<th>Quantity</th>
						<th>Unit Price</th>
						<th>Total Price</th>
					</tr>
				</thead>

				<tbody>
					<?php for($i = 0; $i < $count; ++$i){?>
						<tr>
							<td><?php echo $product[$i];?></td>
							<td><?php echo $quantity[$i];?></td>
							<td><?php echo $unitprice[$i];?></td>
							<td><?php echo $totalprice[$i];?></td>
						</tr>
					<?php }?>

					<tr>
						<td></td>
						<td></td>
						<td class="total-confirm">Total Order:</td>
						<td><?php print $_POST['totalorder'];?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row" style="text-align:center">
		<a href="<?php echo $home_page;?>">Return to the home page</a>
	</div>

	</div>

<!--Debugging purposes only-->
<!--
<p>SQL statement used insert/update salesorder table: <?php echo $sqlOrder?></p>
-->
<?php for($i = 0; $i < $count; ++$i){?>
	<!--
	<p>SQL statement <?php echo ($i + 1);?> used to insert/update orderitem table: <?php echo $sqlItem[$i];?></p>
	-->
<?php }?>

</body>
</html>
