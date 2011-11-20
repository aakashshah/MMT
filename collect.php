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
<head><title>Collect Details - MMT</title>
</head>

<body>
	<br /></br >
	<table class = "transactions">
	<tr>
	<?php
		$firstRow = 1;
		$query = "select with_username as friend, sum (with_amt) as amt from participates where email_add = '".$_SESSION['email']."' group by with_username order by amt";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			die($query);
		}

		while ($row = oci_fetch_object($statement))
		{
			if ($firstRow == 1)
			{
				echo "<tr><td class = 'transactions'>Friend</td><td class = 'transactions'>Amount</td></tr>";
				$firstRow = 0;
			}
			echo "<tr><td class = 'transactions'>".$row->FRIEND."</td><td class = 'transactions'>".$row->AMT."</td></tr>";
		}
	?>
	</tr>
	</table>
</body>
</html>
