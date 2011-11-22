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

        $query = "select cat_id,cat_desc from category";
	$statementCategory = oci_parse($connection, $query);
	if (!oci_execute($statementCategory))
	{
		echo $query;
		die("Failed to execute query!");
	}

?> 
<html><head><title>Report a Payment</title></head>
<body>
<b>With whom are you settling the payment?</b>
<?php
	$queryFriend = "select friend_email_add from has_friends where email_add = '".$_SESSION['email']."'";
	echo $queryFriend;
	$statementFriend = oci_parse($connection, $queryFriend);
	if(!oci_execute($statementFriend))
	{
		echo $queryFriend;
		die("Failed to execute query!");
	}
	echo "<div id = 'whoSettled'></div>";
	echo '<select name = "nameWhoSettled" onChange="whoSettledFunction(this.value,\'whoSettled\')"  />';
	echo "<option value = '".$_SESSION['email']."'> ".$_SESSION['email']."</option>";
	while(1)
	{
		$row = oci_fetch_object($statementFriend);
		if (!$row)
		{
			break;
		}
		$subQuery = "select name from users where email_add = '".$row->FRIEND_EMAIL_ADD."'";
		$subStatement = oci_parse($connection, $subQuery);
		if (!oci_execute($subStatement))
		{
			echo $subQuery;
			die("Failed to execute subquery!");
		}
		$friendName = oci_fetch_object($subStatement);
		echo "<option value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option>";
	}


?>




