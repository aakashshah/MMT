#!/usr/local/bin/php

<?php session_start() ?>

<html>
<head>
<title>Sign Up - MMT</title>
</head>
<body>

<?php
	// process only if the login button is clicked
	if(isset($_POST['signupnow']))
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

		$query = "insert into users values ('".$usrname."', '".$pwd."',
			'".$_POST['name']."', ".$bankbal.", '".$phno."', ".$mbudget.")";
		echo $query;

		// Insert values into the table to store the user details
		$stmt = oci_parse($connection, $query);

		if (!oci_execute($stmt))
		{
			die("Failed to execute query");
		}

		// after inserting the values goto main page again
		header("Location:index.php");
	}
?>

<form name = 'signupform' action = 'signUp.php' method = 'post'>
	<table align = 'center' border = '0'>
		<tr>
			<td>Username (Email Address):</td><td><input name = 'username' type = 'text' /></td>
		</tr>
		<tr>
			<td>Password:</td><td><input name = 'password' type = 'password' /></td>
		</tr>
		<tr>
			<td>Re-enter Password:</td><td><input name = 'repassword' type = 'password' /></td>
		</tr>
		<tr>
			<td>Alias:</td><td><input name = 'name' type = 'text' /></td>
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
				<input name = 'signupnow' type = 'submit' value = 'Sign Up' />
			</td>
			<td>
				<input type = 'button' value = 'Back' onclick = 'history.go(-1)'>
			</td>
		</tr>
	</table>
</form>

</body>
</html>
