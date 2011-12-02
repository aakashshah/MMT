#!/usr/local/bin/php

<?php session_start(); ?>

<html>
<head>
<title>Welcome to MMT</title>
<link rel = "stylesheet" href = "mmt.css">
<script type = "text/javascript" src = "js/validations.js"></script>
</head>
<body>

<table>
<tr>
<td>
<img src = "images/logo.jpg" />
</td>
<td width = "100%">
<?php
	if (isset($_SESSION['email']))
	{
		header("Location:home.php");
	}
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

		/* For a valid user, the monthly_budget should always be >= 0. if it is < 0, it means that the users has not joined MMT yet */
		$query = "select name, monthly_budget from users where email_add = '".$usrname."' and password = '".$pwd."' and monthly_budget >= 0";
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
			$_SESSION['mbudget'] = $row->MONTHLY_BUDGET;
			header("Location:home.php");
		}
	}
	else if(isset($_POST['signup'])) // if the signup button is clicked, redirect to signup.php page
	{
		header("Location:signUp.php");
	}
?>

<form name = 'loginform' action = 'index.php' onsubmit = 'return validateSignIn()' method = 'post'>
	<table align = 'center'>
		<tr>
			<td>Username:</td><td><input name = 'username' type = 'text' /></td>
		</tr>
		<tr>
			<td>Password:</td><td><input name = 'password' type = 'password' /></td>
		</tr>
		<tr>
			<td align = 'center' colspan = '2'>
				<input class = 'mainButton' name = 'login' type = 'submit' value = 'Sign In' />
			</td>
		</tr>
</form>
<form name = 'signUpform' action = 'index.php' method = 'post'>
		<tr>
			<td align = 'center' colspan = '2'>Do not have any account yet?</td>
		</tr>
		<tr>
			<td align = 'center' colspan = '2'>
				<input class = 'mainButton' name = 'signup' type = 'submit' value = 'Sign Up Now!' />
			</td>
		</tr>
	</table>
</form>
</td>
</tr>
<table>

</body>
</html>
