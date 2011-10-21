#!/usr/local/bin/php
<?php session_start(); 

#!/usr/local/bin/php


	$connection = oci_connect($username = 'aakash',
				  $password = 'password',
				  $connection_string = '//oracle.cise.ufl.edu/orcl');

	if (!$connection)
	{
		die("Connection Failed");
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

		
?



<html>
<body>

</body>
</html>
