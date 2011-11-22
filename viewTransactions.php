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
	
	/* Admin Case */
	$whereClause = " and u.email_add = '".$_SESSION['email']."'";
	if ($_SESSION['email'] == 'admin@mmt.com')
	{
		$whereClause = "";
	}
	
	$query = "select name, txn_date, txn_desc, tot_amt, shared_amt from shares s, transaction t, users u where u.email_add = s.email_add and s.trans_id = t.trans_id".$whereClause." order by txn_date desc";

	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	
	$firstRow = 1;
	
?>
	<br /><br />
	<table class = "transactions" align = "center">
<?php
	$myTotal = 0; $overallTotal = 0;
	while($row = oci_fetch_object($statement))
	{
		$displayName = "";
		$displayNameColumn = "";
		$colspan = 2;
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			$displayNameColumn = "<td class = 'transactions'>Name</td>";
			$displayName = "<td class = 'transactions'>".$row->NAME."</td>";
			$colspan = 3;
		}
		
		if (1 == $firstRow)
		{
			echo "<tr>";
			
			echo $displayNameColumn."<td class = 'transactions'>Date</td><td class = 'transactions'>Description</td><td class = 'transactions'>Your Share</td><td class = 'transactions'>Total Amount</td>";
			
			echo "</tr>";
			$firstRow = 0;
		}
		
		echo "<tr>";
		echo $displayName."<td class = 'transactions'>".$row->TXN_DATE."</td><td class = 'transactions'>".$row->TXN_DESC."</td><td class = 'transactions'>".$row->SHARED_AMT."</td><td class = 'transactions'>".$row->TOT_AMT."</td>";
		echo "</tr>";
		$myTotal = $myTotal + $row->SHARED_AMT;
		$overallTotal = $overallTotal + $row->TOT_AMT;
	}
	
	/* Display totals only if there were rows */
	if (0 == $firstRow)
	{
		echo "<tr><td class = 'transactions' colspan = '".$colspan."' align = 'center'>Totals</td><td class = 'transactions'>".$myTotal."</td><td class = 'transactions'>".$overallTotal."</td></tr>";
	}
	else
	{
		echo "<center>No transactions found!</center>";
	}
?>
	</table>

<html>
<head><title>View Transactions - MMT</title>
</head>

<body>
</body>
</html>
