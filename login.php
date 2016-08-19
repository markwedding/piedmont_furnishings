<?ob_start();?>
<?require_once("connect_to_DB.php");?>
<? 
	set_error_handler('errorHandler6');

	function errorHandler6( $errno, $errstr, $errfile, $errline, $errcontext)
	{
  		print "<br />error number:".$errno." line:".$errline.": ".$errstr;
  	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?	connectDB();  // connect to the database server   

	$strSQL = "SELECT emp_id, emp_lname, emp_fname,emp_username, job_id, emp_pword FROM employee";

	try {
		$rs = @mysqli_query($db, $strSQL);  //or die("Error in SQL statement: " . mysqli_error($db));
	
		if (!$rs) {
			throw new Exception(mysqli_error($db));
		}
	}
	catch (Exception $e) {
		header("Location: error.php?msg=" . $e->getMessage() . "&line=" . $e->getLine());
	}
	
	$empExists = False;
	$job_id = 0;
	$emp_id = 0;
	$fname = "";
	$lname = "";
	?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" href="piedmont.css"/>
		<title>Login</title>
	</head>
	<body>
		<h1>Piedmont Furnishings Employee Login</h1>
		<form name="form1" method="post" action="login.php">
		<? if (isset($_REQUEST['page1_submit'])) 
		{
		// __________________________________________________ DISPLAY PAGE 2  ?>
			<?
			while($emp = mysqli_fetch_array($rs))
			{
					
				if ($_POST['uname'] == $emp['emp_username'] and $_POST['pword'] == $emp['emp_pword'])
				{
					if (($_POST['jobList'] == "Sales Agent" and $emp['job_id'] > 2) or ($_POST['jobList'] == "Management" and $emp['job_id'] < 3)) {
						$empExists = True;
						$job_id = $emp['job_id'];
						$emp_id = $emp['emp_id'];
						$fname = $emp['emp_fname'];
						$lname = $emp['emp_lname'];
					}
				}
			}
			if ($empExists)
			{
				session_start();
				$_SESSION['job_id'] = $job_id;
				$_SESSION['emp_id'] = $emp_id;
				$_SESSION['fname'] = $fname;
				$_SESSION['lname'] = $lname;
				if ($_POST['jobList']=="Sales Agent") {
					mysqli_free_result($rs);  
					mysqli_close($db);
					header('Location: home.php');
				} else {
					mysqli_free_result($rs);  
					mysqli_close($db);
					header('Location: mgrhome.php');
				}
			?>
			<div class="info">
				<h5>You entered the correct password on the first try!</h5>
			</div>
			<? 
			}
			else 
			{ ?>
			<div class="info">
			<div class="group-login">
				<h5>Wrong Username or Password! Try again. 
			</div>
				<form method="POST" action="login.php">
				<div class="group-login">
				<p>
					Username:
				</p>
				<input type="text" NAME="uname" /><br /><br />
				<p>
					Password: 
				</p>
				<input type="password" NAME="pword" /><br /><br />
					<input type="hidden" name="job" value="<?print $_POST['jobList'];?>"/>
					</div>
					
					<p class="button">
					<input type="submit" name="page2_submit" value="Submit" />
					<input type="reset" value="Reset" /><br />
					</p>
				</form>
				</h5>	
				</div>
				
			<? 
			} ?>
		<? 
		} elseif (isset($_REQUEST['page2_submit'])) 
		{
		// __________________________________________________ DISPLAY PAGE 3  ?>
	
			<? 
			while($emp = mysqli_fetch_array($rs))
			{
				if ($_POST['uname'] == $emp['emp_username'] and $_POST['pword'] == $emp['emp_pword'])
				{
					if (($_POST['job'] == "Sales Agent" and $emp['job_id'] > 2) or ($_POST['job'] == "Management" and $emp['job_id'] < 3)) {
						$empExists = True;
						$job_id = $emp['job_id'];
						$emp_id = $emp['emp_id'];
						$fname = $emp['emp_fname'];
						$lname = $emp['emp_lname'];
					}
				}
			}
			if ($empExists)
			{
				session_start();
				$_SESSION['job_id'] = $job_id;
				$_SESSION['emp_id'] = $emp_id;
				$_SESSION['fname'] = $fname;
				$_SESSION['lname'] = $lname;
				if ($_POST['jobList']=="Sales Agent") {
					mysqli_free_result($rs);  
					mysqli_close($db);
					header('Location: home.php');
				} else {
					mysqli_free_result($rs);  
					mysqli_close($db);
					header('Location: mgrhome.php');
				}
			?>
		<h5>You entered the correct password!  But it took you two tries. </h5>
			<? 
			} 
			else 
			{ ?>
			<div class="info">
			<div class="group-login">
			<h5>Wrong Username and Password Again! This was your second try. 
			</div>
			<div class="group-login">
			<form method="POST" action="password.php">
				<input type="hidden" name="job" value="<?print $_POST['job'];?>"/>
				<p>
				Username:
				</p>
				<input type = "text" name="uname" /><br />
				<p>
				Password: 
				</p>
				<input type = "password" name="pword" /><br />
				</div>
				<p class="button">
					<input type="submit" name="page3_submit" value="Submit" />
					<input type="reset" value="Reset" /><br />
				</p>	
			</form>
			</h5>
			</div>
			<? } ?>
		
		<? } 
		elseif (isset($_REQUEST['page3_submit'])) 
		{
		// ________________________________________________ DISPLAY FINAL PAGE  ?>

			<? 
			while($emp = mysqli_fetch_array($rs))
			{
				if ($_POST['uname'] == $emp['emp_username'] and $_POST['pword'] == $emp['emp_pword'])
				{
					if (($_POST['job'] == "Sales Agent" and $emp['job_id'] > 2) or ($_POST['job'] == "Management" and $emp['job_id'] < 3)) {
						$empExists = True;
						$job_id = $emp['job_id'];
						$emp_id = $emp['emp_id'];
						$fname = $emp['emp_fname'];
						$lname = $emp['emp_lname'];
					}
				}
			}
			if ($empExists)
			{
				session_start();
				$_SESSION['job_id'] = $job_id;
				$_SESSION['emp_id'] = $emp_id;
				$_SESSION['fname'] = $fname;
				$_SESSION['lname'] = $lname;
				if ($_POST['jobList']=="Sales Agent") {
					mysqli_free_result($rs);  
					mysqli_close($db);
					header('Location: home.php');
				} else {
					mysqli_free_result($rs);  
					mysqli_close($db);
					header('Location: mgrhome.php');
				}
			?>
			<div class="group-login">
			<h5>You entered the correct password!   But it took you three tries.</h5>
			</div>
			<? } 
			else 
			{ ?>
			<div class="info">	
			<div class="group-login">
			<h5>Wrong username and password again! You're out of luck. </h5>
			</div>
			</div>
			<? } ?>

		<? }
		else 
		{
		// ____________________________________________________________ DEFAULT  ?>
		
		<!-- This is the first pass for the user – page 1 -->
		
			<div class="info">
			<div class="group-login">
				<p>
					Please select your occupation
					<br></br>
					
					<select name="jobList">
						<option>Management</option>
						<option>Sales Agent</option>
					</select>
				</p>
				</div>
				<div class="group-login">
				<p id="password">
					Please enter your username and password
					<br></br>
					Username: <input type="text" name="uname" size="16" value=""/>
					<br></br>
					Password: <input type="password" name="pword" size="16" value=""/>
					<br></br>
					<?//print $strSQL;?>
				</p>
				
			</div>
			<p class="button">
				<input type="submit" name="page1_submit" value="Submit" />
				<input type="reset" value="Reset" />
			</p>
			</div>
		</form>
		<? }
		//________________________________________________________________________?>
	</body>
</html>