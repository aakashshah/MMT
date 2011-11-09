#!/usr/local/bin/php

<?php session_start() ?>

<html>
<head>
<title>Profile Settings - MMT</title>
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
		
		/*
		echo $usrname;
		echo $pwd;
		echo $_POST['name'];
		echo $bankbal;
		echo $_POST['phno'];
		echo $_POST['mbudget'];*/

		$query = "update users set password='".$pwd."', bank_balance=".$bankbal.", ph_no='".$phno."', monthly_budget=".$mbudget." where email_add = '".$_SESSION['email']."'";

		// Insert values into the table to store the user details
		$stmt = oci_parse($connection, $query);

		if (!oci_execute($stmt))
		{
			echo $query;
			die("Failed to execute query");
		}

		// after updating the values goto main page again
		header("Location:home.php");
	}
?>

<form name = 'changeSettings' action = 'profileSettings.php' method = 'post'>
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
			<td>Monthly Budget:</td><td><input name = 'mbudget' type = 'text' /></td>
		</tr>
		<tr>
			<td>Phone Number:</td><td><input name = 'phno' type = 'text' /></td>
		</tr>
		<tr align = 'center'>
			<td>
				<input name = 'changeNow' type = 'submit' value = 'Update' />
			</td>
			<td>
				<INPUT type = 'button' value = 'Back' onclick = 'history.go(-1)'>
			</td>
		</tr>
	</table>
</form>

</body>
</html>
