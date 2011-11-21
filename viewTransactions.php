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
	
	$query = "select txn_date, txn_desc, tot_amt, shared_amt from shares s, transaction t, users u where u.email_add = s.email_add and s.trans_id = t.trans_id and u.email_add = '".$_SESSION['email']."'";

	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	
	$firstRow = 1;
	
?>
	<table class = "transactions" align = "center">
<?php
	while($row = oci_fetch_object($statement))
	{
		if (1 == $firstRow)
		{
			echo "<tr><td class = 'transactions'>Date</td><td class = 'transactions'>Description</td><td class = 'transactions'>Your Share</td><td class = 'transactions'>Total Amount</td></tr>";
			$firstRow = 0;
		}
		echo "<tr><td class = 'transactions'>".$row->TXN_DATE."</td><td class = 'transactions'>".$row->TXN_DESC."</td><td class = 'transactions'>".$row->SHARED_AMT."</td><td class = 'transactions'>".$row->TOT_AMT."</td></tr>";
	}
?>
	</table>

<html>
<head><title>View Transactions - MMT</title>
</head>

<body>
</body>
</html>
