<?session_start();?>
<?require_once("connect_to_DB.php");?>
<? 
	set_error_handler('errorHandler8');

	function errorHandler8( $errno, $errstr, $errfile, $errline, $errcontext)
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
			var jAmount = <? echo json_encode($_POST['amount']) ?>;
			//var orderNumberConfirmation = document.forms["orderform"]["ordernumber"].value;
			var orderDateConfirmation = document.forms["orderform"]["orderdate"].value;
			var totalCost = document.forms["orderform"]["totalorder"].value;
			var alertMessage = "";
			
			/*if (orderNumberConfirmation == null || orderNumberConfirmation == "")
			{
				alertMessage += "The order number needs to be filled out!\n";
			}
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
				return false;;
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
<?
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

<?
if($_GET['action']=="create"){
?>

<h1>New Order Form</h1>
	
	<? 
	if(isset($_POST['amount'])) {
	?>		
		<form id="orderform" method="post" action="orderconfirmation.php" onsubmit="return validateForm()">
			<?
			$row=mysqli_fetch_array($resultOrderID);
			$ordernumber = $row[0] + 101;
			?>
			<input type="hidden" name="ordernumber" value="<?=$ordernumber?>"/>
			<table>
				<tr>
					<td>Order Number:</td>
					<td><input type="text" name="ordernumberd" value="<?=$ordernumber?>" disabled="disabled"/></td>
					<td>Order Date:</td>
					<td><input type="text" name="orderdate" value="<?=date('Y-m-d')?>"/></td>
				</tr>
				<tr>
					<td>Customer:</td>
					<td><select name="customer" >
					<? while($row = mysqli_fetch_array($resultCust))
					{  // retrieve each row of the recordset in turn ?>
						<option value="<?= $row["cust_id"]; ?>"> 
						<?= "(" . $row["cust_id"] . ") " . $row["cust_lname"] . ", " . $row["cust_fname"] ?>
						</option>
					<? } ?>
					</select>
					</td>
				</tr>
				<tr>
					<td>Sales Agent:</td>
					<td><select name="salesagent" >
					<? while($row = mysqli_fetch_array($resultEmp))
					{  // retrieve each row of the recordset in turn ?>
						<option value="<?= $row["emp_id"]; ?>"> 
						<?= $row["emp_lname"] . ", " . $row["emp_fname"] ?>
						</option>
					<? } ?>
					</select>
					</td>
					<td>Order status:</td>
					<td>		
					<select name="status" >
					<? while($row = mysqli_fetch_array($resultStat))
					{  // retrieve each row of the recordset in turn ?>
						<option value="<?= $row['status_id'];?>"> 
						<?= $row['status_type']?>
						</option>
					<? } ?>
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
				
				<? for ($i = 1; $i <= $_POST['amount']; $i++)
				{ ?>		
				<tr>
					<td>
						<select name="product<?=$i?>" class="product" rel="unitprice<?=$i?>">
							<? while($row = mysqli_fetch_array($resultProd))
							{  // retrieve each row of the recordset in turn ?>
								<option value="<?= $row["product_id"]; ?>"> 
								<?= $row["product_id"] . ": " . $row["product_name"] ?>
								</option>
							<? } mysqli_data_seek($resultProd,0); ?>
						</select> 
					</td>
					<td><input type="text" name="quantity<?=$i?>"/></td>
					<td><input type="text" name="unitprice<?=$i?>" id="unitprice<?=$i?>"/ value="250.00"></td>
					<td><input type="text" name="totalprice<?=$i?>"/></td>
				</tr>
				<?
				} ?>
				<tr>
					<td colspan="3" align="right">Total Order:</td>
					<td><input type="text" name="totalorder"/></td>
				</tr>
			</table>
			<div class="submit">
				<input type="submit" value="Submit"/>
			</div>	
			
			<?//send the count of the products in the order to the confirmation page?>
			<input type="hidden" name="productcount" value="<?=$_POST['amount']?>"/>
			<input type="hidden" name="action" value="create"/>
		</form>
		
		<!--Debugging purposes only-->
		<!--
		<p>SQL statement used to populate order ID field: <?=$sql4?></p>
		<p>SQL statement used to populate customer list box: <?=$sql0?></p>
		<p>SQL statement used to populate employee list box: <?=$sql1?></p>
		<p>SQL statement used to populate order status list box: <?=$sql3?></p>
		<p>SQL statement used to populate product list box: <?=$sql2?></p>
		-->

	<?
	} else {
	?>
	<form method= "post" action="orderform.php?action=create">
		<table>
			<tr>
				<td>
					Number of distinct products: </td><td><input type="text" name="amount" value="1"/>
				</td>
			</tr>
		</table>
	<div class="submit"><p><input type="submit" value="Submit" /></p></div>
	</form>
	<?}?>
<?
} else {
?>
	<h1>Edit Order Form</h1>
	
	<?
	if(isset($_POST['submitOrderNum'])){
	?>
		<?
		$sqlOrderData1="SELECT s.order_id,s.order_date,s.cust_id,s.emp_id,os.status_type 
			FROM salesorder AS s INNER JOIN orderstatus AS os ON s.status_id=os.status_id WHERE s.order_id=".$_POST['order']." ORDER BY s.order_id;";
		$sqlOrderData2="SELECT o.product_id,p.product_name,o.item_quantity,o.item_unitprice,(o.item_quantity*o.item_unitprice) AS total_price 
			FROM orderitem AS o INNER JOIN product AS p ON o.product_id=p.product_id WHERE o.order_id=".$_POST['order'].";";
			
			try {
				$rsOrderData1=@mysqli_query($db,$sqlOrderData1); //or die("SQL error: " . mysqli_error($db));
				if (!$rsOrderData1) {
					throw new Exception(mysqli_error($db));  
				}
				$rsOrderData2=@mysqli_query($db,$sqlOrderData2); //or die("SQL error: " . mysqli_error($db));
				if (!$rsOrderData2) {
					throw new Exception(mysqli_error($db));  
				}
			} catch (Exception $e) {
				header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
			}
		?>
		
		<?$datarow=mysqli_fetch_array($rsOrderData1);?>
		<form id="orderform" method="post" action="orderconfirmation.php" onsubmit="return validateForm()">
			<input type="hidden" name="ordernumber" value="<?=$datarow[0]?>"/>
			<table>
				<tr>
					<td>Order Number:</td>
					<td><input type="text" name="ordernumberd" value="<?=$datarow[0]?>" disabled="disabled"/></td>
					<td>Order Date:</td>
					<td><input type="text" name="orderdate" value="<?=$datarow[1]?>"/></td>
				</tr>
				<tr>
					<td>Customer:</td>
					<td><select name="customer" >
					<? while($row = mysqli_fetch_array($resultCust))
					{  // retrieve each row of the recordset in turn ?>
						<option value="<?= $row["cust_id"]; ?>" <?if($row["cust_id"]==$datarow[2]){?>selected="selected"<?}?>> 
						<?= "(" . $row["cust_id"] . ") " . $row["cust_lname"] . ", " . $row["cust_fname"] ?>
						</option>
					<? } ?>
					</select>
					</td>
				</tr>
				<tr>
					<td>Sales Agent:</td>
					<td><select name="salesagent" >
					<? while($row = mysqli_fetch_array($resultEmp))
					{  // retrieve each row of the recordset in turn ?>
						<option value="<?= $row["emp_id"]; ?>" <?if($row["emp_id"]==$datarow[3]){?>selected="selected"<?}?>> 
						<?= $row["emp_lname"] . ", " . $row["emp_fname"] ?>
						</option>
					<? } ?>
					</select>
					</td>
					<td>Order status:</td>
					<td>		
					<select name="status" >
					<? while($row = mysqli_fetch_array($resultStat))
					{  // retrieve each row of the recordset in turn ?>
						<option value="<?= $row['status_id'];?>" <?if($row['status_type']==$datarow[4]){?>selected="selected"<?}?>> 
						<?= $row['status_type']?>
						</option>
					<? } ?>
					</select>
					</td>
				</tr>
			</table>	
			
			<?mysqli_free_result($rsOrderData1);?>
			
			<table>
				<tr>
					<th>Product</th>
					<th>Quantity</th>
					<th>Unit Price</th>
					<th>Total Price</th>
				</tr>
				
				<?$rowcount=mysqli_num_rows($rsOrderData2);?>
				
				<? for ($i = 1; $i <= $rowcount; $i++){
					$datarow=mysqli_fetch_array($rsOrderData2);
				?>		
				<tr>
					<td>
						<select name="product<?=$i?>">
							<? while($row = mysqli_fetch_array($resultProd))
							{  // retrieve each row of the recordset in turn ?>
								<option value="<?= $row["product_id"]; ?>" <?if($row["product_id"]==$datarow[0]){?>selected="selected"<?}?>> 
								<?= $row["product_id"] . ": " . $row["product_name"] ?>
								</option>
							<? } mysqli_data_seek($resultProd,0); ?>
						</select> 
					</td>
					<td><input type="text" name="quantity<?=$i?>" value="<?=$datarow[2]?>"/></td>
					<td><input type="text" name="unitprice<?=$i?>" value="<?=$datarow[3]?>"/></td>
					<td><input type="text" name="totalprice<?=$i?>" value="<?=$datarow[4]?>"/></td>
				</tr>
				<?
				} ?>
				
				<? mysqli_data_seek($rsOrderData2,0);?>
				<tr>
					<td colspan="3" align="right">Total Order:</td>
					<td><input type="text" name="totalorder" value="<?
							$sum = 0;
							while($datarow=mysqli_fetch_array($rsOrderData2)){
								$sum = $sum + $datarow[4];}
							echo $sum;
					?>"/></td>
				</tr>
			</table>
			
			<? mysqli_free_result($rsOrderData2);?>
			
			<div class="submit">
				<input type="submit" value="Submit"/>
			</div>	
			
			<?//send the count of the products in the order to the confirmation page?>
			<input type="hidden" name="productcount" value="<?=$rowcount?>"/>
			<input type="hidden" name="action" value="edit"/>
		</form>
		
		<?/*
		<p>SQL statement used to populate order information: <?=$sqlOrderData1?></p>
		<p>SQL statement used to populate product information: <?=$sqlOrderData2?></p>
		*/?>
		
	<?
	} else {
	?>
		<?
		if($_SESSION['job_id'] >= 3){
			$sqlOrderNum="SELECT s.order_id,s.order_date,s.cust_id,c.cust_lname,c.cust_fname,e.emp_lname,e.emp_fname,os.status_type 
				FROM salesorder AS s INNER JOIN customer AS c ON s.cust_id=c.cust_id INNER JOIN employee AS e ON s.emp_id=e.emp_id 
				INNER JOIN orderstatus AS os ON s.status_id=os.status_id WHERE s.emp_id=".$_SESSION['emp_id']." ORDER BY s.order_id;";}
		else {
			$sqlOrderNum="SELECT s.order_id,s.order_date,s.cust_id,c.cust_lname,c.cust_fname,e.emp_lname,e.emp_fname,os.status_type 
				FROM salesorder AS s INNER JOIN customer AS c ON s.cust_id=c.cust_id INNER JOIN employee AS e ON s.emp_id=e.emp_id 
				INNER JOIN orderstatus AS os ON s.status_id=os.status_id ORDER BY s.order_id;";}
			
			try {
				$rsOrderNum=@mysqli_query($db,$sqlOrderNum); //or die("SQL error: " . mysqli_error($db));
				if (!$rsOrderNum) {
					throw new Exception(mysqli_error($db));  
				}
			} catch (Exception $e) {
				header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
			}
		?>
		
		<div style='text-align:center;'>
			<p>Please select which order you would like to edit:</p>
			<form name="editorderform" method="post" action="orderform.php?action=edit">
				<select name="order" size="25">
				<? $k = true;
				while($datarow=mysqli_fetch_array($rsOrderNum)){?>
					<option value="<?=$datarow[0]?>" <?if($k){?>selected="selected"<?}?>><?="Order Number: ".$datarow[0]." &nbsp &nbsp Order Date: ".$datarow[1]." &nbsp &nbsp Customer: (".$datarow[2].") ".$datarow[3].", ".$datarow[4]." &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Sales Agent: ".$datarow[5].", ".$datarow[6]." &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Order Status: ".$datarow[7]?></option><?echo "\n"; $k = false;}?>
				</select>
				<?mysqli_free_result($rsOrderNum);?>
				<br/>
				<div class="submit"><input type="submit" name="submitOrderNum" value="Submit"/></div>
				
				<!--Debugging purposes only-->
				<!--
				<p>SQL statement used to populate order number list box: <?=$sqlOrderNum?></p>
				-->
			</form>
		</div>
	
	<? } ?>
<? } ?>

<?
mysqli_free_result($resultCust);
mysqli_free_result($resultEmp);
mysqli_free_result($resultProd);
mysqli_free_result($resultStat);
mysqli_free_result($resultOrderID);
mysqli_close($db);
?>

</body>
</html>