<?php
	ob_start();
	session_start();
	include("connect_to_DB.php");
	include("components.php");
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
	<?php html_head("Itemized Sales Report");?>
	<link rel="stylesheet" href="http://cdn.datatables.net/1.10.12/css/jquery.dataTables.css" charset="utf-8">
	<script src="http://cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
	<script>
	$(document).ready( function () {
		$('#isr').DataTable();
	} );
	</script>
</head>
<body>
	<?php
	$home_page = "";
	if($_SESSION['job_id'] < 3) {
		$home_page = "mgrhome.php";
	} else {
		$home_page = "home.php";
	}

	html_navbar('plain', $home_page);?>

	<div class="container">

		<h2>Itemized Sales Report</h2>
		<hr>

		<?php if(isset($_POST['submitdates'])){

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

				$outputFile = "salesReports/SalesReport_" . date('Y-m-d_H-i-s') . ".xml";

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

			<p><strong>XML file: </strong><a href="<?php echo $address?>" target="_blank"><?php echo $address?></a></p>
			<p><strong>Document Type Definition file: </strong><a href="<?php echo $dtdaddress?>" target="_blank"><?php echo $dtdaddress?></a></p>
			<p class="text-danger">
				* The table below uses the <a href="https://datatables.net/" target="_blank">DataTables JavaScript library</a> to add searching, sorting, pagination, etc.
			</p>
			<p><a href="<?php echo $home_page;?>">Return to the home page</a><p>

			<hr>

			<table class="table table-striped table-hover" id="isr">
				<thead>
					<tr>
						<th>Order#</th><th>CustomerID</th><th>AgentID</th><th>Date</th><th>Status</th><th>Item#</th><th>Quantity</th><th>Price</th><th>Total</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total = 0;

					while($row = mysqli_fetch_array($rsSales)){?>
						<tr>
							<td><?php echo $row['order_id']?></td>
							<td><?php echo $row['cust_id']?></td>
							<td><?php echo $row['emp_id']?></td>
							<td><?php $date = date_create($row['order_date']); echo date_format($date,'n/j/y');?></td>
							<td><?php echo $row['status_type']?></td>
							<td><?php echo $row['product_id']?></td>
							<td><?php echo $row['item_quantity']?></td>
							<td><?php echo '$' . number_format($row['item_unitprice'],2)?></td>
							<td><?php echo '$' . number_format($row['item_totalprice'],2)?></td>
						</tr>
						<?php $total += $row['item_totalprice'];
					}?>

				</tbody>
				<tfoot>
					<tr>
						<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
						<td>Total:</td>
						<td><?php echo '$' . number_format($total,2)?></td>
					</tr>
				</tfoot>
			</table>

			<?php
			mysqli_free_result($rsSales);
			mysqli_close($db);
			?>

			<!--Debugging purposes only-->
			<!--
			<p>SQL statement used to populate the report: <?php echo $sqlSales?></p>
			-->

		<?php
		} else {?>
			<p>Please indicate the date range for the Itemized Sales Report.</p>
			<p class="text-danger">
				* Stay within the range of July 10, 2011 and July 10, 2014, as most of the sample data is within this range.
			</p>
			<form id="daterange" method="post" action="sales.php">

				<?php $months = array('January','February','March','April','May','June','July','August','September','October','November','December');?>

				<table class="table isr-dates">
					<tr>
						<td>Start Date:</td>
						<td><select class="form-control" name="smonth">
							<?php for($i = 0; $i < count($months); ++$i){?>
								<option <?php if($months[$i]=='July'){echo 'selected';}?>><?php echo $months[$i]?></option> <?php echo "\n";
							}?>
							</select>
						</td>
						<td><input class="form-control" type="number" min="1" max="31" name="sday" size="3" value="15"/></td>
						<td><select class="form-control" name="syear">
							<?php for($i = (date('Y')-15); $i <= date('Y'); ++$i){?>
								<option <?php if($i==2011){echo 'selected';}?>><?php echo $i?></option> <?php echo "\n";
							}?>
						</td>
					</tr>
					<tr>
						<td>End Date:</td>
						<td><select class="form-control" name="emonth">
							<?php for($i = 0; $i < count($months); ++$i){?>
								<option <?php if($months[$i]=='August'){echo 'selected';}?>><?php echo $months[$i]?></option> <?php echo "\n";
							}?>
							</select>
						</td>
						<td><input class="form-control" type="number" min="1" max="31" name="eday" size="3" value="15"/></td>
						<td><select class="form-control" name="eyear">
							<?php for($i = (date('Y')-15); $i <= date('Y'); ++$i){?>
								<option <?php if($i==2011){echo 'selected';}?>><?php echo $i?></option> <?php echo "\n";
							}?>
						</td>
					</tr>
				</table>

				<button name="submitdates" type="submit" class="btn btn-primary">Submit</button>
			</form>
		<?php
		}?>

	</div>

</body>
</html>
