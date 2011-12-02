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
		$query = "select name, t.trans_id, txn_date, txn_desc, tot_amt, shared_amt from shares s, transaction t, users u where u.email_add = s.email_add and t.txn_date between '".$_POST['start_date']."' and '".$_POST['end_date']."' and s.trans_id = t.trans_id".$whereClause." order by txn_date desc, trans_id desc";
	}
	/* Delete values as given by the form */
	else if (isset($_POST['delTxns']))
	{
		$selectedTxns = $_POST['checkedtransid'];
		if (!empty($selectedTxns))
		{
			$count = count($selectedTxns);
			$whereClause = "where";
			for ($i = 0; $i < $count; $i++)
			{
				if ($i != 0)
				{
					$whereClause = $whereClause." or";
				}
				$whereClause = $whereClause." trans_id = ".$selectedTxns[$i];
			}
		
			$query = "delete from shares ".$whereClause;
			$statement = oci_parse($connection, $query);
			if (!oci_execute($statement, OCI_NO_AUTO_COMMIT))
			{
				die($query);
			}
			$query = "delete from participates ".$whereClause;
			$statement = oci_parse($connection, $query);
			if (!oci_execute($statement, OCI_NO_AUTO_COMMIT))
			{
				die($query);
			}
			$query = "delete from transaction ".$whereClause;
			$statement = oci_parse($connection, $query);
			if (!oci_execute($statement))
			{
				die($query);
			}
		}
		
		header("Location:delTransactions.php");
	}
	else
	{
		$query = "select name, t.trans_id, txn_date, txn_desc, tot_amt, shared_amt from shares s, transaction t, users u where u.email_add = s.email_add and s.trans_id = t.trans_id".$whereClause." order by txn_date desc, trans_id desc";
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
		<?php
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			echo "<td colspan = '7' align = 'center'>";
		}
		else
		{
			echo "<td colspan = '4' align = 'center'>";
		}
		?>
			<form name = 'filtertransactions' action='delTransactions.php' method='post'>
			Start Date: <input type="text" name="start_date" />
			End Date: <input type="text" name="end_date" /><br />
			<input name="date" type="submit" value="Filter" />
			<input name="reset" type="submit" value="View All" />
			</form>
		</td>
	</tr>
	<!-- If the user is admin, start a new form -->
	<form name = 'deltxns' action = 'delTransactions.php' method = 'post'>
<?php	
	$myTotal = 0; $overallTotal = 0;
	while($row = oci_fetch_object($statement))
	{
		$displayCheckColumn = "";
		$displayCheckBox = "";
		$displayName = "";
		$displayNameColumn = "";
		$colspan = 2;
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			$displayCheckColumn = "<td class = 'transactions'>Select</td><td class = 'transactions'>Txn Id</td>";
			$displayCheckBox = "<td class = 'transactions'><input type = 'checkbox' name = 'checkedtransid[]' value = '".$row->TRANS_ID."'></td><td class = 'transactions'>".$row->TRANS_ID."</td>";
			$displayNameColumn = "<td class = 'transactions'>Name</td>";
			$displayName = "<td class = 'transactions'>".$row->NAME."</td>";
			$colspan = 5;
		}
		
		if (1 == $firstRow)
		{
			echo "<tr>";
			
			echo $displayCheckColumn.$displayNameColumn."<td class = 'transactions'>Date</td><td class = 'transactions'>Description</td><td class = 'transactions'>Your Share</td><td class = 'transactions'>Total Amount</td>";
			
			echo "</tr>";
			$firstRow = 0;
		}
		
		echo "<tr>";
		echo $displayCheckBox.$displayName."<td class = 'transactions'>".$row->TXN_DATE."</td><td class = 'transactions'>".$row->TXN_DESC."</td><td class = 'transactions'>".$row->SHARED_AMT."</td><td class = 'transactions'>".$row->TOT_AMT."</td>";
		echo "</tr>";
		$myTotal = $myTotal + $row->SHARED_AMT;
		$overallTotal = $overallTotal + $row->TOT_AMT;
	}
	
	/* Display totals only if there were rows */
	if (0 == $firstRow)
	{
		echo "<tr><td class = 'transactions' colspan = '".$colspan."' align = 'center'>Totals</td><td class = 'transactions'>".$myTotal."</td><td class = 'transactions'>".$overallTotal."</td></tr>";
		
		if ($_SESSION['email'] == 'admin@mmt.com')
		{
			echo "<tr><td colspan = '7' align = 'center'><input name = 'delTxns' type = 'submit' value = 'Delete' /></td></tr>";
			echo "</form>";
		}
	}
	else
	{
		echo "<center>No transactions found!</center>";
	}
?>
	</table>

<html>
<head><title>Delete Transactions - MMT</title>
</head>

<body>

</body>
</html>
