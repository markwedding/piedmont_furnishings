<?php
	ob_start();
	include("connect_to_DB.php");
	connectDB();?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php

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
	$failed_login = FALSE;
	?>

	<?php if(isset($_POST['page1_submit']))
	{
	// __________________________________________________ DISPLAY PAGE 2  ?>
		<?php
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
		?><?php
		}
		else
		{
			$failed_login = TRUE;
		}
	}?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" href="piedmont.css"/>
		<title>Login</title>
	</head>
	<body>
		<h1>Piedmont Furnishings Employee Login</h1>
		<form name="form1" method="post" action="login.php">
			<?php if($failed_login){echo "FAILED";}?>
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
					Username: <input type="text" name="uname" value=""/>
					<br></br>
					Password: <input type="password" name="pword" value=""/>
					<br></br>
					<?php//print $strSQL;?>
				</p>

			</div>
			<p class="button">
				<input type="submit" name="page1_submit" id="page1_submit" value="Submit" />
				<input type="reset" value="Reset" />
			</p>
			</div>
		</form>
	</body>
</html>
