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

	if(isset($_POST['add_email']))
	{
		$query = "insert into has_friends values ('".$_SESSION['email']."', '".$_POST['adding_email']."', 0)";
		$statement = oci_parse($connection, $query);

		if (!oci_execute($statement))
		{
			echo $query;
			die("Friend not added!");
		}
		header("Location:home.php");
	}
	else if (isset($_POST['delete_email']))
	{
		$query = "delete from has_friends where email_add = '".$_SESSION['email']."' and friend_email_add = '".$_POST['deleting_email']."' and dues = 0";
		$statement = oci_parse($connection, $query);

		if (!oci_execute($statement))
		{
			echo $query;
			die("Friend cannot be deleted! Check that you have no dues with this friend");
		}
		header("Location:home.php");
	}
?>

<html>
<head><title>Modify Friends - MMT</title></head>
<body>

<form name="input" action="modFriends.php" method="post">

Add a friend (Enter his/her email-address): <input type="text" name="adding_email" />

<input name="add_email" type="submit" value="add" />
<br />
Delete a friend (Enter his/her email-address): <!--<input type="text" name="deleting_email" />-->

<select name="deleting_email">
	<option selected>----</option>
<?php
	$query = "select friend_email_add from has_friends where email_add = '".$_SESSION['email']."' and dues = 0";
	$statement = oci_parse($connection, $query);
	if (!oci_execute($statement))
	{
		echo $query;
		die("Failed to execute query!");
	}
	
	while (1)
	{
		$row = oci_fetch_object($statement);
		
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
		
		echo "<option name= 'delete_email' value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option>";
	}
?>
</select>
<input name="delete_email" type="submit" value="delete" />

</form>
</body>

</html>
