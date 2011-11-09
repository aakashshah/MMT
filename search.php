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

//$count = strlen($_POST['cat_desc']);

$query = "select cat_desc from Category where cat_desc like'%".$_POST['search']."%'";
$statement = oci_parse($connection, $query);
oci_execute($statement);
$row = oci_fetch_object($statement);

		if (!$row)
		{
			echo $row;
			echo "\n";
		}

$query = "select  type, txn_desc, tot_amt, date from Transaction where txn_desc like'%".$_POST['search']."%'";
$statement = oci_parse($connection, $query);
oci_execute($statement);
$row = oci_fetch_object($statement);

		if (!$row)
		{
			echo $row;
			echo "\n";
		}
		
$query = "select ph_no, name, email_add, monthly_budget from User where email_add = (select friend_email_add from has_friends where email_add = ".$_SESSION['email']." and friend_email_add like '%".$_POST['search']."%')";
$statement = oci_parse($connection, $query);
oci_execute($statement);
$row = oci_fetch_object($statement);

		if (!$row)
		{
			echo $row;
			echo "\n";
		}
	
		
$grpnames = "select group_name from UserGroup where group_name like '%".$_POST['search']."%'" ;
$statement = oci_parse($connection, $query);
oci_execute($statement);
$row = oci_fetch_object($statement);

		if (!$row)
		{
			echo $row;
			echo "\n";
		}
?>



<html>
<head><title>SEARCH</title></head>
<body>

<form action="search.php" method="post">

Search: <input type="text" name="search" />

</form>

</body>
</html>
