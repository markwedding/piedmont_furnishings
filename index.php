<?php
	ob_start();
	include("connect_to_DB.php");
  include("components.php");
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
	<?php
  # Add the HTML head element
  html_head("Piedmont Furnishings");
  ?>
	</head>
	<body>

    <?php
    # Add the navbar
    html_navbar("plain", "index.php");
    ?>

    <div class="container">
      <form class="form-horizontal thin-window" action="index.php" method="post" name="form1">
        <fieldset>
          <legend>Employee Login</legend>
          <div class="form-group">
            <label for="jobList" class="col-lg-4 control-label">Select your occupation</label>
            <div class="col-lg-8">
              <select class="form-control" name="jobList" id="jobList">
                <option>Management</option>
                <option>Sales Agent</option>
              </select>
            </div>
          </div>
          <div class="form-group<?php if($failed_login){echo " has-error";}?>">
            <label for="uname" class="col-lg-4 control-label">Username</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="uname" name="uname" data-toggle="tooltip" data-placement="top" title="Enter RBSenior for Management account and Kramer for Sales Agent account">
            </div>
          </div>
          <div class="form-group<?php if($failed_login){echo " has-error";}?>">
            <label for="pword" class="col-lg-4 control-label">Password</label>
            <div class="col-lg-8">
              <input type="password" class="form-control" id="pword" name="pword" data-toggle="tooltip" data-placement="bottom" title="Enter benefactor for Management account and cosmo for Sales Agent account">
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-8 col-lg-offset-4">
              <button type="submit" class="btn btn-primary" name="page1_submit" id="page1_submit">Submit</button>
            </div>
          </div>
        </fieldset>
      </form>
    </div>
	</body>
</html>
