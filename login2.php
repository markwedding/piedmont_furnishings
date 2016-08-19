<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link rel="stylesheet" type="text/css" href="login.css"/>
		<title>Login</title>
	</head>
	<body>
		<h1 class="center">Login page</h1>
		<form name="form1" method="post" action="login.php">
		<? if (isset($_REQUEST['page1_submit'])) 
		{
		// __________________________________________________ DISPLAY PAGE 2  ?>
			<? if (($_POST['jobList']=="Sales Agent" and $_POST['pword'] == "furnish") or ($_POST['jobList']=="Management" and $_POST['pword'] == "manager"))
			{ ?>
				<center><h2>You entered the correct password on the first try!</center></h2>
			<? 
			}
			else 
			{ ?>
				<center><h2>Wrong Password! Try again. 
				<form method="POST" action="login.php">
					Password: <input type="password" NAME="pword" /><br /><br />
					<input type="hidden" name="job" value="<?print $_POST['jobList'];?>"/>
					<input type="submit" name="page2_submit" value="SUBMIT" />
					<input type="reset" value="RESET" /><br />
				</form>
				</h2></center>
			<? 
			} ?>
		<? 
		} elseif (isset($_REQUEST['page2_submit'])) 
		{
		// __________________________________________________ DISPLAY PAGE 3  ?>
	
			<? if (($_POST['pword'] == "furnish" and $_POST['job'] == "Sales Agent") or ($_POST['pword'] == "manager" and $_POST['job'] == "Management")) 
			{ ?>
		<center><h2>You entered the correct password!  But it took you two tries. </h2>
			<? 
			} 
			else 
			{ ?>
			<center><h2>Wrong Password Again! This was your second try. 
			<form method="POST" action="password.php">
				<input type="hidden" name="job" value="<?print $_POST['job'];?>"/>
				Password: <input type = "password" name="pword" /><br /><br />
				<input type="submit" name="page3_submit" value="SUBMIT" />
				<input type="reset" value="RESET" /><br />
			</form>
			</h2></center>
			<? } ?>
		
		<? } 
		elseif (isset($_REQUEST['page3_submit'])) 
		{
		// ________________________________________________ DISPLAY FINAL PAGE  ?>

			<? if (($_POST['pword'] == "furnish" and $_POST['job'] == "Sales Agent") or ($_POST['pword'] == "manager" and $_POST['job'] == "Management")) 
			{ ?>
			<center><h2>You entered the correct password!   But it took you three tries.</h2>
			<? } 
			else 
			{ ?>
		<center><h2>Wrong Password Again! You're out of luck. </h2></center>
			<? } ?>

		<? }
		else 
		{
		// ____________________________________________________________ DEFAULT  ?>
		
		<!-- This is the first pass for the user – page 1 -->
		
			<div class="info">
				<p class="center">
					Please select your occupation
					<br></br>
					<select name="jobList">
						<option>Management</option>
						<option>Sales Agent</option>
					</select>
				</p>
				<p class="center" id="password">
					Please enter your password
					<br></br>
					<input name="pword" size="16"/>
				</p>
			</div>
			<p class="center" id="buttons">
				<input type="submit" name="page1_submit" value="Submit" />
				<input type="reset" value="Reset" />
			</p>
		</form>
		<? }
		//________________________________________________________________________?>
	</body>
</html>