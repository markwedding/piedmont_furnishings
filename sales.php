<?
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
	<title>Itemized Sales Report</title>
	<link type="text/css" rel="stylesheet" href="sales.css"/>
</head>
<body>
	<h1>Itemized Sales Report</h1>
	<? if(isset($_POST['submitdates'])){
		
		$startdate = date_create($_POST['smonth'] . ' ' . $_POST['sday'] . ', ' . $_POST['syear']);
		$startdate = date_format($startdate, 'Y-m-d');
		$enddate = date_create($_POST['emonth'] . ' ' . $_POST['eday'] . ', ' . $_POST['eyear']);
		$enddate = date_format($enddate, 'Y-m-d');
		
		connectDB();
		
		$sqlSales = "SELECT i.order_id, s.cust_id, s.emp_id, s.order_date, o.status_type, i.product_id, 
			i.item_quantity, i.item_unitprice, (i.item_quantity * i.item_unitprice) AS item_totalprice 
			FROM orderitem AS i INNER JOIN salesorder AS s ON i.order_id=s.order_id INNER JOIN orderstatus AS o ON s.status_id=o.status_id 
			WHERE s.order_date BETWEEN '" . $startdate . "' AND '" . $enddate . "';";
		
		try {
			$rsSales = @mysqli_query($db, $sqlSales); //or die("SQL error: " . mysqli_error($db));
		
			if(!$rsSales) {
				throw new Exception(mysqli_error($db));  
			}
		
			//create XML file
		
			$outputFile = "SalesReport_" . date('Y-m-d_H-i-s') . ".xml";
		
			$fh = fopen($outputFile,'w'); //or die("Cannot open file");
			
			if(!$fh) {
				throw new Exception(mysqli_error($db));  
			}
		}
		catch (Exception $e) {
			header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
		}
		
		$strOut = "<?xml version=\"1.0\"?>\n<!DOCTYPE salesreport SYSTEM \"salesreport.dtd\">\n\n<salesreport>\n";
		
		$order = '';
		$sum = 0;
		$saleInProgress = false;
		
		while($row = mysqli_fetch_array($rsSales)){
			if($order <> $row['order_id']){
				if($saleInProgress){$strOut .= "\t</sale>\n";}
				$strOut .= "\t<sale>\n";
				$strOut .= "\t\t<ordernumber>" . $row['order_id'] . "</ordernumber>\n";
				$strOut .= "\t\t<customerid>" . $row['cust_id'] . "</customerid>\n";
				$strOut .= "\t\t<agentid>" . $row['emp_id'] . "</agentid>\n";
				$date = date_create($row['order_date']);
				$strOut .= "\t\t<date>" . date_format($date,'n/j/y') . "</date>\n";
				$strOut .= "\t\t<status>" . $row['status_type'] . "</status>\n";
			}
			$strOut .= "\t\t<product>\n";
			$strOut .= "\t\t\t<itemnumber>" . $row['product_id'] . "</itemnumber>\n";
			$strOut .= "\t\t\t<quantity>" . $row['item_quantity'] . "</quantity>\n";
			$strOut .= "\t\t\t<price>$" . number_format($row['item_unitprice'],2) . "</price>\n";
			$strOut .= "\t\t\t<total>$" . number_format($row['item_totalprice'],2) . "</total>\n";
			$strOut .= "\t\t</product>\n";
			
			$sum += $row['item_totalprice'];
			$order = $row['order_id'];
			$saleInProgress = true;
		}
		mysqli_data_seek($rsSales, 0);
		
		if($saleInProgress){$strOut .= "\t</sale>\n";}
		$strOut .= "\t<totalprice>$" . number_format($sum,2) . "</totalprice>\n";
		$strOut .= "</salesreport>";
		
		fwrite($fh, $strOut);
		fclose($fh);
		
		$dir = dirname($_SERVER['PHP_SELF']);
		if($dir=="\\"){$dir="";}
		$address = "http://" . $_SERVER["HTTP_HOST"] . $dir . "/" . $outputFile;
		$dtdaddress = "http://" . $_SERVER["HTTP_HOST"] . $dir . "/salesreport.dtd";
		?>
		<div class="menu"><ul><li><a href="mgrhome.php">Home</a></li></ul></div>
		
		<p>XML file:<a href="<?=$address?>"><?=$address?></a></p>
		<p>Document Type Definition file:<a href="<?=$dtdaddress?>"><?=$dtdaddress?></a></p>
				
		<table>
			<tr>
				<th>Order#</th><th>CustomerID</th><th>AgentID</th><th>Date</th><th>Status</th><th>Item#</th><th>Quantity</th><th>Price</th><th>Total</th>
			</tr>
			<?
			$total = 0;
			
			while($row = mysqli_fetch_array($rsSales)){?>
				<tr>
					<td><?=$row['order_id']?></td>
					<td><?=$row['cust_id']?></td>
					<td><?=$row['emp_id']?></td>
					<td><?$date = date_create($row['order_date']); echo date_format($date,'n/j/y');?></td>
					<td><?=$row['status_type']?></td>
					<td><?=$row['product_id']?></td>
					<td><?=$row['item_quantity']?></td>
					<td><?='$' . number_format($row['item_unitprice'],2)?></td>
					<td><?='$' . number_format($row['item_totalprice'],2)?></td>
				</tr>
				<?$total += $row['item_totalprice'];
			}?>
			<tr>
				<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
				<td>Total:</td>
				<td><?='$' . number_format($total,2)?></td>
			</tr>
		</table>
		
		<?
		mysqli_free_result($rsSales);
		mysqli_close($db);
		?>
		
		<!--Debugging purposes only-->
		<!--
		<p>SQL statement used to populate the report: <?=$sqlSales?></p>
		-->
	
	<?
	} else{?>
		<p>Please indicate the date range for the Itemized Sales Report.</p>
		<form id="daterange" method="post" action="sales.php">
			<?/*<table>
				<tr>
					<td>Start Date:</td>
					<td><input type="text" name="startdate" value="<?
						$date=date_create(date('Y-m-d'));
						date_sub($date, date_interval_create_from_date_string("30 days"));
						echo date_format($date,'Y-m-d');?>"/></td>
				</tr>
				<tr>
					<td>End Date:</td>
					<td><input type="text" name="enddate" value="<?=date('Y-m-d');?>"/></td>
				</tr>
			</table>*/?>
			
			<?$months = array('January','February','March','April','May','June','July','August','September','October','November','December');?>
			
			<table>
				<tr>
					<td>Start Date</td>
					<td><select name="smonth">
						<? for($i = 0; $i < count($months); ++$i){?>
							<option><?=$months[$i]?></option> <?="\n";
						}?>
						</select>
					</td>
					<td><input type="text" name="sday" size="3" value="1"/></td>
					<td><select name="syear">
						<? for($i = (date('Y')-10); $i <= date('Y'); ++$i){?>
							<option><?=$i?></option> <?="\n";
						}?>
					</td>
				</tr>
				<tr>
					<td>End Date</td>
					<td><select name="emonth">
						<? for($i = 0; $i < count($months); ++$i){?>
							<option><?=$months[$i]?></option> <?="\n";
						}?>
						</select>
					</td>
					<td><input type="text" name="eday" size="3" value="1"/></td>
					<td><select name="eyear">
						<? for($i = (date('Y')-10); $i <= date('Y'); ++$i){?>
							<option><?=$i?></option> <?="\n";
						}?>
					</td>
				</tr>
			</table>
			
			<input type="submit" name="submitdates" value="Submit"/>
		</form>
	<?
	}?>
	
</body>
</html>