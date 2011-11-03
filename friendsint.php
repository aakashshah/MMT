#!/usr/local/bin/php
<?php session_start(); 

#!/usr/local/bin/php


	if (!require("connection.php"))
	{
		// connection failure return error code 1
		exit(1);
	}

	if(isset($_POST['add_email']))
	{
		$statement = oci_parse($connection, 'insert into has_friends (email_add, friend_email_add, dues) values ('$_SESSION['email']', '$_POST['adding_email']', 0)');

		if (!oci_execute($statement))
		{
			die("Friend not added!");
		}
		echo "\nFriend added!\n";	
	}

	else if (isset($_POST['delete_email']))
	{
	$statement = oci_parse($connection, 'DELETE FROM Users WHERE email_add = '$_SESSION['email']' and friend_email_add = '$_POST['deleting_email']' and dues = 0');

	if (!oci_execute($statement))
	{
		die("Friend cannot be deleted! Check that you have no dues with this friend");
	}
	echo "\nFriend deleted!\n";
}

		
?>

<html>
<body>

</body>
</html>
