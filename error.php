<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Error</title>
	<link type="text/css" rel="stylesheet" href="sales.css"/>
</head>
<body>
	
	<h1>An error has occurred.</h1>
<?

	print "Error message: " . $_GET["msg"];
	if (isset($_GET["line"]))
	{
		print " - line number: " . $_GET["line"];
	}

?>
</body>
</html>