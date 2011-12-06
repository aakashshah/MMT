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
	
	if (isset($_POST['date']))
	{
		$query = "select name, txn_date, txn_desc, tot_amt, shared_amt from shares s, transaction t, users u where u.email_add = s.email_add and t.txn_date between '".$_POST['start_date']."' and '".$_POST['end_date']."' and s.trans_id = t.trans_id".$whereClause." order by txn_date desc";
		
		$payQuery = "((select with_username as nm , txn_date, type, txn_desc, tot_amt, (-with_amt) as with_amt from participates p, transaction t, users u where p.email_add = '".$_SESSION['email']."' and u.email_add = p.email_add and t.type = 'PY' and t.txn_date between '".$_POST['start_date']."' and '".$_POST['end_date']."' and p.trans_id = t.trans_id) union (select p.email_add as nm, txn_date, type, txn_desc, tot_amt, with_amt from participates p, transaction t, users u where p.with_username = '".$_SESSION['email']."' and u.email_add = p.email_add and type = 'PY' and p.trans_id = t.trans_id and t.txn_date between '".$_POST['start_date']."' and '".$_POST['end_date']."'))";
		
		$loanQuery = "((select with_username as nm, txn_date, type, txn_desc, tot_amt, (-with_amt) as with_amt from participates p, transaction t, users u where p.email_add = '".$_SESSION['email']."' and u.email_add = p.email_add and t.txn_date between '".$_POST['start_date']."' and '".$_POST['end_date']."' and t.type = 'LN' and p.trans_id = t.trans_id) union (select p.email_add as nm, txn_date, type, txn_desc, tot_amt, with_amt from participates p, transaction t, users u where p.with_username = '".$_SESSION['email']."' and u.email_add = p.email_add and type = 'LN' and p.trans_id = t.trans_id and t.txn_date between '".$_POST['start_date']."' and '".$_POST['end_date']."'))";
	}
	else
	{
		$query = "select name, txn_date, txn_desc, tot_amt, shared_amt from shares s, transaction t, users u where u.email_add = s.email_add and s.trans_id = t.trans_id".$whereClause." order by txn_date desc";
		
		$payQuery = "((select with_username as nm, txn_date, type, txn_desc, tot_amt, (-with_amt) as with_amt from participates p, transaction t, users u where p.email_add = '".$_SESSION['email']."' and u.email_add = p.email_add and t.type = 'PY' and p.trans_id = t.trans_id) union (select p.email_add as nm , txn_date, type, txn_desc, tot_amt, with_amt from participates p, transaction t, users u where p.with_username = '".$_SESSION['email']."' and u.email_add = p.email_add and type = 'PY' and p.trans_id = t.trans_id))";
		
		$loanQuery = "((select with_username as nm, txn_date, type, txn_desc, tot_amt, (-with_amt) as with_amt from participates p, transaction t, users u where p.email_add = '".$_SESSION['email']."' and u.email_add = p.email_add and t.type = 'LN' and p.trans_id = t.trans_id) union (select p.email_add as nm, txn_date, type, txn_desc, tot_amt, with_amt from participates p, transaction t, users u where p.with_username = '".$_SESSION['email']."' and u.email_add = p.email_add and type = 'LN' and p.trans_id = t.trans_id))";
	}

	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	
	$firstRow = 1;
	
?>
	<br /><br />
	<table class = "transactions" align = "center">
	<tr>
		<td colspan = "5" align = "center">
			<form name = 'viewtransaction' action='viewTransactions.php' method='post'>
			Start Date: <input type="text" name="start_date" />
			End Date: <input type="text" name="end_date" /><br />
			<input name="date" type="submit" value="Filter" />
			<input name="reset" type="submit" value="View All" />
			</form>
		</td>
	</tr>
<?php
	$myTotal = 0; $overallTotal = 0;
	while($row = oci_fetch_object($statement))
	{
		$displayName = "";
		$displayNameColumn = "";
		$colspan = 3;
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			$displayNameColumn = "<td class = 'transactions'>Name</td>";
			$displayName = "<td class = 'transactions'>".$row->NAME."</td>";
			$colspan = 4;
		}
		
		if (1 == $firstRow)
		{
			echo "<tr>";
			echo "<td colspan='".($colspan+2)."'><i>Transactions</i></td>";
			echo "</tr>";
			
			echo "<tr>";			
			echo $displayNameColumn."<td class = 'transactions'>User</td> <td class = 'transactions'>Date</td><td class = 'transactions'>Description</td><td class = 'transactions'>Your Share</td><td class = 'transactions'>Total Amount</td>";			
			echo "</tr>";
			$firstRow = 0;
		}
		
		echo "<tr>";
		echo $displayName."<td class = 'transactions'>".$_SESSION['email']."<td class = 'transactions'>".$row->TXN_DATE."</td><td class = 'transactions'>".$row->TXN_DESC."</td><td class = 'transactions'>".$row->SHARED_AMT."</td><td class = 'transactions'>".$row->TOT_AMT."</td>";
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
		echo "<center>No transactions found for 'expenses'!</center>";
	}
	
	/************************** PAYMENTS **********************************/
	
	$firstRow = 1;
	
	$statement = oci_parse($connection, $payQuery);
	if (!oci_execute($statement))
	{
		die($payQuery);
	}
	
	$myTotal = 0; $overallTotal = 0;
	while($row = oci_fetch_object($statement))
	{
		$displayName = "";
		$displayNameColumn = "";
		$colspan = 3;
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			$displayNameColumn = "<td class = 'transactions'>Name</td>";
			$displayName = "<td class = 'transactions'>".$row->NAME."</td>";
			$colspan = 4;
		}
		
		if (1 == $firstRow)
		{
			echo "<tr>";
			echo "<td colspan='".($colspan+2)."'><i>Payments</i></td>";
			echo "</tr>";
			
			echo "<tr>";			
			echo $displayNameColumn."<td class = 'transactions'>User</td><td class = 'transactions'>Date</td><td class = 'transactions'>Description</td><td class = 'transactions'>Mode</td><td class = 'transactions'>Total Amount</td>";			
			echo "</tr>";
			$firstRow = 0;
		}
		
		if ($row->WITH_AMT > 0)
		{
			//$withAmt = $row->WITH_AMT;
			$mode = "Received";
		}
		else
		{
			//$withAmt = (-$row->WITH_AMT);
			$mode = "Paid";
		}
		
		echo "<tr>";
		echo $displayName."<td class = 'transactions'>".$row->NM."</td><td class = 'transactions'>".$row->TXN_DATE."</td><td class = 'transactions'>".$row->TXN_DESC."</td><td class = 'transactions'>".$mode."</td><td class = 'transactions'>".$row->TOT_AMT."</td>";
		echo "</tr>";
		$myTotal = $myTotal + $row->WITH_AMT;
	}
	
	if ($myTotal > 0)
	{
		$mode = "Received";
	}
	else if ($myTotal < 0)
	{
		$mode = "Paid";
	}
	else
	{
		$mode = "--";
	}
	
	/* Display totals only if there were rows */
	if (0 == $firstRow)
	{
		echo "<tr><td class = 'transactions' colspan = '".$colspan."' align = 'center'>Totals</td><td class = 'transactions'>".$mode."</td><td class = 'transactions'>".abs($myTotal)."</td></tr>";
	}
	else
	{
		echo "<center>No transactions found for 'payments'!</center>";
	}
	
	/************************** LOANS **********************************/
	
	$firstRow = 1;
	
	$statement = oci_parse($connection, $loanQuery);
	if (!oci_execute($statement))
	{
		die($loanQuery);
	}
	
	$myTotal = 0; $overallTotal = 0;
	while($row = oci_fetch_object($statement))
	{
		$displayName = "";
		$displayNameColumn = "";
		$colspan = 3;
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			$displayNameColumn = "<td class = 'transactions'>Name</td>";
			$displayName = "<td class = 'transactions'>".$row->NAME."</td>";
			$colspan = 4;
		}
		
		if (1 == $firstRow)
		{
			echo "<tr>";
			echo "<td colspan='".($colspan+2)."'><i>Loans</i></td>";
			echo "</tr>";
			
			echo "<tr>";			
			echo $displayNameColumn."<td class = 'transactions'>User</td><td class = 'transactions'>Date</td><td class = 'transactions'>Description</td><td class = 'transactions'>Mode</td><td class = 'transactions'>Total Amount</td>";			
			echo "</tr>";
			$firstRow = 0;
		}
		
		if ($row->WITH_AMT > 0)
		{
			//$withAmt = $row->WITH_AMT;
			$mode = "Received";
		}
		else
		{
			//$withAmt = (-$row->WITH_AMT);
			$mode = "Paid";
		}
		
		echo "<tr>";
		echo $displayName."<td class = 'transactions'>".$row->NM."</td><td class = 'transactions'>".$row->TXN_DATE."</td><td class = 'transactions'>".$row->TXN_DESC."</td><td class = 'transactions'>".$mode."</td><td class = 'transactions'>".$row->TOT_AMT."</td>";
		echo "</tr>";
		$myTotal = $myTotal + $row->WITH_AMT;
	}
	
	if ($myTotal > 0)
	{
		$mode = "Received";
	}
	else if ($myTotal < 0)
	{
		$mode = "Paid";
	}
	else
	{
		$mode = "--";
	}
	
	/* Display totals only if there were rows */
	if (0 == $firstRow)
	{
		echo "<tr><td class = 'transactions' colspan = '".$colspan."' align = 'center'>Totals</td><td class = 'transactions'>".$mode."</td><td class = 'transactions'>".abs($myTotal)."</td></tr>";
	}
	else
	{
		echo "<center>No transactions found for 'loans'!</center>";
	}
?>
	</table>

<html>
<head><title>View Transactions - MMT</title>
</head>

<body>

</body>
</html>
