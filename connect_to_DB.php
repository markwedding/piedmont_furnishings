<?
	set_error_handler('errorHandler2');

	function errorHandler2( $errno, $errstr, $errfile, $errline, $errcontext)
	{
		print "<br />error number:".$errno." line:".$errline.": ".$errstr;
	}
	
	function connectDB() 
	{
		global $db;
		try {
			$db = mysqli_connect('database.hosting.vt.edu', 'bit4444u7', 'VGu2SetB53keM5cQ', 'bit444407');
			//or die("Cannot connect to the database: " . mysqli_connect_error());
			if(!$db){
				throw new Exception(mysqli_connect_error());
			}
		} catch (Exception $e) {
		header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
		}
	}
?>