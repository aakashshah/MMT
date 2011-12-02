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

    if (isset($_POST['delete_email']))
    {
        $query = "update users set monthly_budget = -1 where email_add = '".$_POST['deleting_email']."'";
        $statement = oci_parse($connection, $query);

        if (!oci_execute($statement))
        {
            echo $query;
            die("user cannot be deactivated!");
        }
        header("Location:home.php");
    }
?>

<html>
<head><title>Modify users - MMT</title></head>
<body>

<form name="input" action="delUsers.php" method="post">
<br />
<br />
Select a user to deactivate:

<select name="deleting_email">
    <option selected>----</option>
<?php
    $query = "select email_add, name, monthly_budget from users";
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

	if (($row->EMAIL_ADD == 'admin@mmt.com') || (($row->MONTHLY_BUDGET == (-1))))
	{
		continue;
	}

       	echo "<option name= 'delete_user' value = '".$row->EMAIL_ADD."'>".$row->NAME." (".$row->EMAIL_ADD.")</option>";
    }
?>
</select>
<input name="delete_email" type="submit" value="Deactivate" />

</form>
</body>

</html>
