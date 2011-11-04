#!/usr/local/bin/php

<?php session_start(); ?>

<html>
<head>
<title>Welcome to MMT</title>
</head>
<body>

<?php
	// process only if the login button is clicked
	if(isset($_POST['login']))
	{
		if (!require("connection.php"))
		{
			// connection failure return error code 1
			exit(1);
		}

		$pwd = md5($_POST['password']);
		$usrname = $_POST['username'];

		$query = "select name from users where email_add = '".$usrname."' and password = '".$pwd."'";
		//echo $query;

		// check for a valid username and password combination
		$stmt = oci_parse($connection, $query);
		if (!oci_execute($stmt))
		{
			die("Failed to execute query");
		}

		// there will be no rows if the combination is not true
		$row = oci_fetch_object($stmt);
		if (!$row)
		{
			echo "Invalid username / password!";
		}
		else
		{
			$_SESSION['email'] = $usrname;
			$_SESSION['alias'] = $row->NAME;
			header("Location:home.php");
		}
	}
	else if(isset($_POST['signup'])) // if the signup button is clicked, redirect to signup.php page
	{
		header("Location:signUp.php");
	}
?>

<form name = 'loginform' action = 'index.php' method = 'post'>
	<table align = 'center' border = '0'>
		<tr>
			<td>Username:</td><td><input name = 'username' type = 'text' /></td>
		</tr>
		<tr>
			<td>Password:</td><td><input name = 'password' type = 'password' /></td>
		</tr>
		<tr>
			<td align = 'center' colspan = '2'>
				<input name = 'login' type = 'submit' value = 'Sign In' />
				<input name = 'signup' type = 'submit' value = 'Sign Up' />
			</td>
		</tr>
	</table>
</form>

</body>
</html>
