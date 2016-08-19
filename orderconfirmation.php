<?require_once("connect_to_DB.php");?>
<?session_start();?>
<? 
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
	<title>Order Form Confirmation</title>
	<link type="text/css" rel="stylesheet" href="piedmont.css"/>
</head>
<body>
	<h1>Order Form Results</h1>
	<div class="menu"><ul><li><a href="<?if($_SESSION['job_id'] <= 2){echo "mgr";}?>home.php">Home</a></li></ul></div><br></br>
	<?
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
	}
	
	if($orderResult and $totalItemResult){?>
		<p>Order form data was successfully entered into the database.</p>
	<?} else{?>
		<p>Order form data was unsuccessfully entered into the database.</p>
	<?}
	
	mysqli_close($db);
	
	?>
	<table>
		<tr>
			<td>Order Number:</td>
			<td><? print $orderNum;?></td>
			<td>Order Date:</td>
			<td><? print $orderDate;?></td>
		</tr>
		<tr>
			<td>Customer:</td>
			<td colspan="3"><? print $customer;?></td>
		</tr>
		<tr>
			<td>Sales Agent:</td>
			<td><? print $salesagent;?></td>
			<td>Order status:</td>
			<td><? print $status;?></td>
		</tr>
	</table>
	<table>
			<tr>
				<th>Product</th>
				<th>Quantity</th>
				<th>Unit Price</th>
				<th>Total Price</th>
			</tr>
			
			<?for($i = 0; $i < $count; ++$i){?>
				<tr>
					<td><?=$product[$i];?></td>
					<td><?=$quantity[$i];?></td>
					<td><?=$unitprice[$i];?></td>
					<td><?=$totalprice[$i];?></td>
				</tr>
			<?}?>
				
			<tr>
				<td></td>
				<td></td>
				<td>Total Order:</td>
				<td><? print $_POST['totalorder'];?></td>
			</tr>
		</table>
	
<!--Debugging purposes only-->
<!--
<p>SQL statement used insert/update salesorder table: <?=$sqlOrder?></p>
-->
<? for($i = 0; $i < $count; ++$i){?>
	<!--
	<p>SQL statement <?=($i + 1);?> used to insert/update orderitem table: <?=$sqlItem[$i];?></p>
	-->
<?}?>

</body>
</html>