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
	<table class = "transactions" align = "center" border = "1" width = "50%">
		<tr>
			<td>Transactions</td>
			<td>Other</td>
		</tr>
		<tr>
			<td valign = "top">
				<a href = "addTransaction.php">Add Transaction</a> <br />
				<a href = "viewTransactions.php">View Transactions</a> <br />
				<a href = "reportPayment.php">Report Payment</a> <br />
			</td>
			<td valign = "top">
				<a href = "modFriends.php">Modify Friends</a> <br />
				<a href = "group.php">Modify Groups</a> <br />
			</td>
		</tr>
	</table>
</body>
</html>
