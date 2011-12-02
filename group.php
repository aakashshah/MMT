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
		$id = $_POST['to_group'];
				
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
		$id = $_POST['gname'];
		
		$finalQuery = "delete from belongs_to where email_add = '".$_POST['friend_delete_group']."' and group_id = ".$id;
		$finalStatement = oci_parse($connection, $finalQuery);
		if (!oci_execute($finalStatement))
		{
			echo $finalQuery;
			die("Friend cannot be deleted!");
		}
		
		$newQuery = "select * from belongs_to where group_id = ".$id;
		$newStatement = oci_parse($connection, $newQuery);
		if (!oci_execute($newStatement))
		{
			echo $newQuery;
			die("Query failed!");
		}
		
		$row = oci_fetch_object($newStatement);
	        if (!$row)
	        {
			$deleteQuery = "delete from usergroup where group_id = ".$id;
			$delStatement = oci_parse($connection, $deleteQuery);
			
			if (!oci_execute($delStatement))
			{
				echo $deleteQuery;
				die("Group cannot be deleted!");
			}
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
	<table class = "transactions" border = "0" align = "center">
	<tr>
		<td class = "transactions">Choose one of the options below:</td>
	</tr>
	<tr><td bgcolor = '#A4C639'>Create a new group</td></tr>
	<tr>
	<td>
	Group Name: <input type="text" name="my_group_name" />
	<input class = "mainButton" name="group_name" type="submit" value="Create" />
	</td>
	</tr>
	<tr>
	<td bgcolor = '#A4C639'>
	Add a friend to a group
	<?php
		$grpQuery = "select group_id, group_name from usergroup where group_owner = '".$_SESSION['email']."'";
		$statement = oci_parse($connection, $grpQuery);
		if (!oci_execute($statement))
		{
			echo $grpQuery;
			die ("Failed to execute query!");
		}
		
	?>
	</td>
	</tr>
	<tr>
	<td>	
	Friend <input type="text" name="add_friend_email" /> to
	<!-- Show all groups to the user of which he is the owner -->
	<select name = "to_group">
	<?php
		while (1)
		{
			$row = oci_fetch_object($statement);
			if (!$row)
			{
				break;
			}
			
			echo "<option value=".$row->GROUP_ID.">".$row->GROUP_NAME."</option>";
		}
	?>
	</select>
	<input class="mainButton" name="group_friend" type="submit" value="Add" />
	</td>
	</tr>
	<tr>
	<td bgcolor = '#A4C639'>Delete a friend from a group</td>
	</tr>
	<tr>
	<td>
	Friend <input type="text" name="friend_delete_group" /> from 
	<select name = "gname">
	<?php
		if (!oci_execute($statement))
		{
			echo $grpQuery;
			die ("Failed to execute query!");
		}
		
		while (1)
		{
			$row = oci_fetch_object($statement);
			if (!$row)
			{
				break;
			}
			
			/* group names do not allow  */
			echo "<option value=".$row->GROUP_ID.">".$row->GROUP_NAME."</option>";
		}
	?>
	</select>
	<input class = "mainButton" name = "delete_friend" type="submit" value="Delete" />
	</td>
	</tr>
	</table>
</form>

</body>
</html>
