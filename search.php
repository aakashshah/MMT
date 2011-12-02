#!/usr/local/bin/php

<?php session_start(); 

if (!isset($_SESSION['email']))
	{
		header("Location:index.php");
	}

	if (!require("connection.php"))
	{
		// connection failure return error code 1
		exit(1);
	}

	if (!require("mainBar.php"))
	{
		die("Failed to include mainbar!");
	}
	
	?>
	
	<?php
		$searchString = $_GET['searchString'];
	?>
	
	<br /><br />
	<table border = "0">
	<tr>
		<td>Your search for '<i><?=$searchString?></i>' returned the following results</td>
	</tr>
	</table>
	
	<!----------------------- Display Categories ------------------------------>
	<table class = "transactions">
	<?php
	$query = "select cat_desc from Category where cat_desc like '%".$searchString."%'";
	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	
	$totalRows = 0;
	echo "<tr><td class = 'transactions'>Category Description</td></tr>";
	while ($row = oci_fetch_object($statement))
	{
		$totalRows = $totalRows + 1;		
		echo "<tr><td class = 'transactions'>".$row->CAT_DESC."</td></tr>";
	}
	
	echo "<tr><td class = 'transactions'><i>Total Rows: ".$totalRows."</i></td></tr>";
	?>
	</table>
	<br />
	<!----------------------- Display Transactions ------------------------------>
	<table class = "transactions">
	<?php
	$totalRows = 0;
	echo "<tr><td class = 'transactions' colspan = '3'>Transactions</td></tr>";
	//$query = "select txn_desc, tot_amt, txn_date from transaction where txn_desc like '%".$searchString."%'";
	$query = "select txn_desc, tot_amt, txn_date from transaction t, participates p where p.trans_id = t.trans_id and p.email_add = '".$_SESSION['email']."' and txn_desc like '%".$searchString."%'";
	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	while($row = oci_fetch_object($statement))
	{
		$totalRows = $totalRows + 1;
		/* Add header */
		if (1 == $totalRows)
		{
			echo "<tr>";
			echo "<td class = 'transactions'>Transaction Description</td>";
			echo "<td class = 'transactions'>Total Amount</td>";
			echo "<td class = 'transactions'>Transaction Date</td>";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td class = 'transactions'>".$row->TXN_DESC."</td>";
		echo "<td class = 'transactions'>".$row->TOT_AMT."</td>";
		echo "<td class = 'transactions'>".$row->TXN_DATE."</td>";
		echo "</tr>";
	}
	echo "<tr><td class = 'transactions' colspan = '3'><i>Total Rows: ".$totalRows."<i></td></tr>";
	?>
	</table>
	<br />
	<!----------------------- Display Friends ------------------------------>
	<table class = "transactions">
	<?php
	$totalRows = 0;
	echo "<tr><td class = 'transactions' colspan = '3'>Users</td></tr>";
	
	$query = "select ph_no, name, email_add from users where (name like '%".$searchString."%'  or email_add like '%".$searchString."%' ) and email_add in (select friend_email_add from users, has_friends where users.email_add = has_friends.email_add and users.email_add = '".$_SESSION['email']."')";
	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	while ($row = oci_fetch_object($statement))
	{
		$totalRows = $totalRows + 1;
		/* Add header */
		if (1 == $totalRows)
		{
			echo "<tr>";
			echo "<td class = 'transactions'>Email Address</td>";
			echo "<td class = 'transactions'>Name</td>";
			echo "<td class = 'transactions'>Phone Number</td>";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td class = 'transactions'>".$row->EMAIL_ADD."</td>";
		echo "<td class = 'transactions'>".$row->NAME."</td>";
		echo "<td class = 'transactions'>".$row->PH_NO."</td>";
		echo "</tr>";
	}
	echo "<tr><td class = 'transactions' colspan = '3'><i>Total Rows: ".$totalRows."</i></td></tr>";
	?>
	</table>
	<br />
	<!----------------------- Display Groups ------------------------------>
	<table class = "transactions">
	<?php
	$totalRows = 0;
	echo "<tr><td class = 'transactions'>User Group</td></tr>";
	
	//$query = "select group_name from usergroup where group_name like '%".$searchString."%'" ;
	$query = "select group_name from usergroup u, belongs_to b where b.group_id = u.group_id and b.email_add = '".$_SESSION['email']."' and group_name like '%".$searchString."%'";
	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		die($query);
	}
	while($row = oci_fetch_object($statement))
	{
		$totalRows = $totalRows + 1;		
		echo "<tr><td class = 'transactions'>".$row->GROUP_NAME."</td></tr>";
	}
	echo "<tr><td class = 'transactions'><i>Total Rows: ".$totalRows."</i></td></tr>";
	?>
	</table>


<html>
<head><title>Search Results - MMT</title></head>
<body>
</body>
</html>
