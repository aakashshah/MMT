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
	
	if(isset($_POST['group_name']))
	{
		$query = "select MAX(group_id) as m from usergroup";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			echo $query;
			die ("Failed to execute query!");
		}
		
		$row = oci_fetch_object($statement);
		if (!$row)
		{
			$groupId = 1;
		}
		else
		{
			echo $row->M;
			$groupId = ($row->M) + 1;
		}

		$finalquery = "insert into usergroup values (".$groupId.", '".$_SESSION['email']."','".$_POST['my_group_name']."')";

		$finalStatement = oci_parse($connection, $finalquery);

		if (!oci_execute($finalStatement))
		{
			echo $finalquery;
			die("User group could not be added!");
		}
		header("Location:home.php");
	}
	/***************     Adding a friend to a group  *************************/
	else if (isset($_POST['group_friend']))
	{
		$query = "select group_id from usergroup where group_owner = '".$_SESSION['email']."' and group_name = '".$_POST['to_group']."'";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			echo $query;
			die ("Failed to execute query!");
		}
		
		$row = oci_fetch_object($statement);
		$id = $row->GROUP_ID;
				
		$finalQuery = "insert into belongs_to values ('".$_POST['add_friend_email']."',".$id.")";
		$finalStatement = oci_parse($connection, $finalQuery);
		if (!oci_execute($finalStatement))
		{
			echo $finalQuery;
			die("Friend cannot be added!");
		}
		
		header("Location:home.php");
	}
	/***************     Deleting a friend from a group  *************************/
	else if (isset($_POST['delete_friend']))
	{
		$query = "select group_id from usergroup where group_owner = '".$_SESSION['email']."' and group_name = '".$_POST['gname']."'";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			echo $query;
			die ("Failed to execute query!");
		}
		
		$row = oci_fetch_object($statement);
		$id = $row->GROUP_ID;
		
		$finalQuery = "delete from belongs_to where email_add = '".$_POST['friend_delete_group']."' and group_id = ".$id;
		$finalStatement = oci_parse($connection, $finalQuery);
		if (!oci_execute($finalStatement))
		{
			echo $finalQuery;
			die("Friend cannot be deleted!");
		}
		
		header("Location:home.php");
	}
?>

<html>
<head><title>Modify Groups - MMT</title></head>
<body>

<form name = 'groupform' action = 'group.php' method = 'post'>
	<br />
	<br />
	Create a new group
	Group Name: <input type="text" name="my_group_name" /><br />
	<input name="group_name" type="submit" value="Add Group" />
	
	<br />
	<br />
	<br />
	Add a friend to a group<br />
	Group Name: <input type="text" name="to_group" /></br />
	Members: <input type="text" name="add_friend_email" /></br />
	<input name="group_friend" type="submit" value="Add Friend" />
	
	<br />
	<br />
	<br />
	Delete/Modify Groups<br />
	Enter group name: <input type="text" name="gname" /><br />
	Enter friend name: <input type="text" name="friend_delete_group" /><br />
	<input name = "delete_friend" type="submit" value="Delete Friend" />
</form>

</body>
</html>
