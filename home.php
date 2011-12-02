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
	<?php
	if ('admin@mmt.com' != $_SESSION['email'])
	{
	?>
		<table class = "transactions" align = "center" border = "1" width = "50%">
	<?php
	}
	else
	{
	?>
		<table class = "transactions" align = "center" border = "1" width = "30%">
	<?php
	}
	?>
		<tr>
			<?php
				if ('admin@mmt.com' != $_SESSION['email'])
				{
			?>
				<td>Transactions</td>
				<td>Other</td>
			<?php
				}
			else
				{
			?>
				<td>Perform database modifications</td>
			<?php
				}
			?>
		</tr>
		<tr>
			<?php
				if ('admin@mmt.com' != $_SESSION['email'])
				{
			?>
			<td valign = "top">
				<a href = "addTransaction.php">Add Transaction</a> <br />
				<a href = "viewTransactions.php">View Transactions</a> <br />
				<a href = "reportPayment.php">Report Payment</a> <br />
			</td>
			<td valign = "top">
				<a href = "modFriends.php">Modify Friends</a> <br />
				<a href = "group.php">Modify Groups</a> <br />
			</td>
			<?php
			}
			else
			{
			?>
			<td valign = "top">
				<a href = "delUsers.php">Delete Users</a> <br />
				<a href = "delTransactions.php">Delete Transactions</a> <br />
				<a href = "delGroups.php">Delete Groups</a> <br />
			</td>
			<?php
			}
			?>
		</tr>
	</table>
</body>
</html>
