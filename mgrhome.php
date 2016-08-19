<?session_start();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Piedmont Furnishings</title>
	<link type="text/css" rel="stylesheet" href="piedmont.css"/>
</head>
<body>
<h1>Piedmont Furnishings</h1>
		<ul class="menu">
			<li><a href="mgrhome.php">Home</a></li>
			<li><a href="#">Orders</a>
				<ul>
					<li><a href="orderform.php?action=create">Create</a></li>
					<li><a href="orderform.php?action=edit">Edit</a></li>
				</ul>
			</li>
			<li><a href="#">Customers</a>
				<ul>
					<li><a href="customerform.php?action=create">Create</a></li>
					<li><a href="customerform.php?action=edit">Edit</a></li>
				</ul>
			</li>
			<li><a href="#">Reports</a>
				<ul>
					<li><a href="sales.php">Itemized Sales</a></li>
					<li><a href="perf.php">Performance Analysis</a></li>
					<li><a href="bus.php">Business Analysis</a></li>
				</ul>
			</li>
		</ul>
			
	<div class="info-home">
	<div class="group">
	<h4>Welcome <?=$_SESSION['fname']." ".$_SESSION['lname'];?></h4>
	<h4>Project: Sales Order Analysis and Reporting System</h4>
	<h4>Client: Piedmont Furnishings</h4>
	</div>
	<div class="group">
	<h6>Class Time: 8:00 am</h6>
	<h6>[Team 7] Group Members:</h6>
	<h6>Jay Kim</h6>
	<h6>Romico Macatula</h6>
	<h6>Jeff San Pedro</h6>
	<h6>Mark Wedding</h6>
	</div>
	<div class="group">
	<p>Project Description: Piedmont Furnishings is a growing hand-made furniture company based out of High Point, North Carolina. It offers 16 different products in 3 different general categories. While a large number of the company’s sales come from walk-in customers at the store in High Point, it also depends on sales agents who work in different regions outside of North Carolina. There are six different regions that these agents operate in: northwest, southwest, northeast, midwest, southeast, and international.</p>

	<p>Up until this point, the company has relied on paper documents to conduct their business. Sales agents fill out physical order forms and customer forms, and must mail them to the store in High Point. In addition, managers rely upon manually constructed reports to analyze the company. The Sales Order Analysis and Reporting System is a web-based support system that will allow all of these forms and reports to be done through the web. Rather than mailing forms to the physical store, sales agents will be able to simply logon to the system through a web browser and fill out electronic forms. Managers will be able to generate reports based upon the data that is entered into the system.</p>
	</div>
	</div> 
	
	
	
</body>
</html>