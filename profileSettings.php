#!/usr/local/bin/php

<?php session_start() ?>

<html>
<head>
<title>Profile Settings - MMT</title>
<link rel = "stylesheet" href = "mmt.css">
<script type = "text/javascript" src = "js/validations.js"></script>
</head>
<body>

<?php
	if (!isset($_SESSION['email']))
	{
		header("Location:index.php");
	}
	
	if (!require("mainBar.php"))
	{
		die("Failed to include mainbar!");
	}
	
	// process only if the login button is clicked
	if(isset($_POST['changeNow']))
	{
		if (!require("connection.php"))
		{
			// connection failure return error code 1
			exit(1);
		}

		$pwd = md5($_POST['password']);
		$usrname = $_POST['username'];

		if ("" == $_POST['bankbal'])
		{
			$bankbal = 0;
		}
		else
		{
			$bankbal = $_POST['bankbal'];
		}

		if ("" == $_POST['mbudget'])
		{
			$mbudget = 0;
		}
		else
		{
			$mbudget = $_POST['mbudget'];
		}

		if ("" == $_POST['phno'])
		{
			$phno = '0000000000';
		}
		else
		{
			$phno = $_POST['phno'];
		}

		$query = "update users set password='".$pwd."', name='".$_POST['alias']."', bank_balance=".$bankbal.", ph_no='".$phno."', monthly_budget=".$mbudget." where email_add = '".$_SESSION['email']."'";

		// Insert values into the table to store the user details
		$stmt = oci_parse($connection, $query);

		if (!oci_execute($stmt))
		{
			echo $query;
			die("Failed to execute query");
		}
		
		$_SESSION['alias'] = $_POST['alias'];
		$_SESSION['mbudget'] = $mbudget;

		// after updating the values goto main page again
		header("Location:home.php");
	}
	else if (isset($_POST['deleteAccount']))
	{
		if (!require("connection.php"))
		{
			// connection failure return error code 1
			exit(1);
		}
		
		//$query = "delete from users where email_add = '".$_SESSION['email']."'";
		$query = "update users set monthly_budget = -1 where email_add = '".$_SESSION['email']."'";
		
		$stmt = oci_parse($connection, $query);

		if (!oci_execute($stmt))
		{
			echo $query;
			die("Failed to execute query");
		}

		// after updating the values goto main page again
		header("Location:logout.php");
	}
?>

<br /><br />

<form name = 'changeSettings' action = 'profileSettings.php' onsubmit = 'return validateProfileSettings()' method = 'post'>
	<table align = 'center' border = '0'>
		<tr>
			<td>Username (Email Address):</td><td><b><?php echo $_SESSION['email']; ?></b></td>
		</tr>
		<tr>
			<td>New password:</td><td><input name = 'password' type = 'password' /></td>
		</tr>
		<tr>
			<td>Re-enter new password:</td><td><input name = 'repassword' type = 'password' /></td>
		</tr>
		<tr>
			<td>Alias:</td><td><?php echo "<input name = 'alias' type = 'text' value='".$_SESSION['alias']."'/>"; ?></td>
		</tr>
		<tr>
			<td>Bank Balance:</td><td><input name = 'bankbal' type = 'text' /></td>
		</tr>
		<tr>
			<td>Monthly Budget:</td><td><input name = 'mbudget' type = 'text' value="<?=$_SESSION['mbudget'];?>"/></td>
		</tr>
		<tr>
			<td>Phone Number:</td><td><input name = 'phno' type = 'text' /></td>
		</tr>
		<tr align = 'center'>
			<td>
				<input class = 'mainButton' name = 'changeNow' type = 'submit' value = 'Update' />
			</td>
			<td>
				<input class = 'normalButton' type = 'button' value = 'Back' onclick = 'history.go(-1)'>
			</td>
		</tr>
		<tr>
		</tr>
		<tr>
			<td align = 'center' colspan = '2'>
				<input class = 'mainButton' name = 'deleteAccount' type = 'submit' value = 'Deactivate Me' />
			</td>
		</tr>
	</table>
</form>

</body>
</html>
