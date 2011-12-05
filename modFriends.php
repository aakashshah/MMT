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
		/* Check if the user exists */
		$query = "select * from users where email_add = '".$_POST['adding_email']."'";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			echo "line27<br />";
			die($query);
		}
		$row = oci_fetch_object($statement);
		
		/* If the friend does not exists, create a dummy friend, with blank password */
		if (!$row)
		{
			$query = "insert into users values ('".$_POST['adding_email']."', 'd41d8cd98f00b204e9800998ecf8427e', 'NA', 0, '0000000000', -1)";
			$statement = oci_parse($connection, $query);
			if (!oci_execute($statement))
			{
				echo "line40<br />";
				die($query);
			}
		}
		
		
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

<br />
<br />

<table class = "transactions" align = "center">
<form name="input" action="modFriends.php" method="post">
	<tr>
		<td class = "transactions" colspan="3">Choose one of the option below:</td>
	</tr>
	<tr>
		<td>Add a friend's email-address:</td>
		<td><input type="text" name="adding_email" /></td>
		<td><input class="mainButton" name="add_email" type="submit" value="Add" /></td>
	</tr>
	<tr>
		<td>Select a friend to delete:</td>
		<td>
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
		</td>
		<td><input class = "mainButton" name="delete_email" type="submit" value="Delete" /></td>
	</tr>
</table>

</form>
</body>

</html>
