#!/usr/local/bin/php

<?php session_start();

	if (!isset($_SESSION['email']))
	{
		header("Location:index.php");
	}
?>

<html>
<head><title>Home - MMT</title></head>
<body>
	This is the home page <br /></br >
	<a href = "modFriends.php">Modify Friends</a> <br />
	<a href = "addTransaction.php">Add Transaction</a> <br />
</body>
</html>
