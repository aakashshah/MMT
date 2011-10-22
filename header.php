<?php
session_start();
$email = $_SESSION['email'];

getAmountToCollect()
{
	$toCollect = 0;
	$toReturn = 0;
	$stmtToCollect = oci_parse($connection, "select sum(dues) from has_friends where email_add = '$email' and dues < 0 group by ".$email);
	if (!oci_execute($stmt))
	{
		die("Failed to execute query");
	}
	$rowToCollect = oci_fetch_object($stmt);
	if(!$rowToCollect)
	{

	}
	else
	{
		$toCollect = $row; 

	}
}

getAmountToReturn()
{
	$stmtToReturn = oci_parse($connection, "select sum(dues) from has_friends where email_add = '$usrname' and dues > 0 group by ".$email

	if (!oci_execute($stmt))
	{
		die("Failed to execute query");
	}
	$rowToCollect = oci_fetch_object($stmt);
	if(!$rowToCollect)
	{

	}
	else
	{
		$toCollect = $row; 
	}
}

getBankBalance()
{
$stmt = oci_parse($connection, "select bank_balance from users where email_add = '$usrname' ");
if (!oci_execute($stmt))
{
	die("Failed to execute query");
}

// there will be no rows if the combination is not true
$row = oci_fetch_object($stmt);
if (!$row)
{

}
else
{
	$bankBalance = $row
}
}



}

<a href="logout.php">Log out </a>

?>
