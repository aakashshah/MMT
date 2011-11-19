#!/usr/local/bin/php

<?php session_start();

	if (!isset($_SESSION['email']))
	{
		header("Location:index.php");
	}
	
	if (!require("mainBar.php"))
	{
		die("Failed to include mainbar!");
	}
?>

<html>
<head><title>Home - MMT</title>
</head>

<body>
	<br /></br >
	<a href = "modFriends.php">Modify Friends</a> <br />
	<a href = "addTransaction.php">Add Transaction</a> <br />
	<a href = "group.php">Modify Groups</a> <br />
</body>
</html>
