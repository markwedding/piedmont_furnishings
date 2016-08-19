<? require_once("connect_to_DB.php");  // connect to furniture database

// ###################### retrieve data from database #################
connectDB();  
$sql = "SELECT product_cost FROM product WHERE product_id='" . $_REQUEST['q']. "'";
$result = mysqli_query($db, $sql) or die("SQL error: " . $sql . " " . mysql_error());  
// ###############################################################

$row = mysqli_fetch_array($result);
print $row[0];

mysqli_close($db);
?>

