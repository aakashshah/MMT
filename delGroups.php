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

    if (isset($_POST['delete_group']))
    {
    	/* First delete from belongs_to without commiting to database */
    	$query = "delete from belongs_to where group_id = ".$_POST['deleting_group'];
        $statement = oci_parse($connection, $query);

        if (!oci_execute($statement, OCI_NO_AUTO_COMMIT))
        {
            die($query);
        }

	/* If this deletion is success, it will commit both belongs_to and the current delete query */
        $query = "delete from usergroup where group_id = ".$_POST['deleting_group'];
        $statement = oci_parse($connection, $query);

        if (!oci_execute($statement))
        {
            die($query);
        }
        header("Location:home.php");
    }
?>

<html>
<head><title>Delete Groups - MMT</title></head>
<body>

<br />
<br />
<form name="input" action="delGroups.php" method="post">

Delete a group: 

<select name="deleting_group">
    <option selected>----</option>
<?php
    $query = "select group_id, group_name from usergroup";
    $statement = oci_parse($connection, $query);
    if (!oci_execute($statement))
    {
        echo $query;
        die("Failed to execute query!");
    }

    while ($row = oci_fetch_object($statement))
    {
        echo "<option name= 'delete_group' value = '".$row->GROUP_ID."'>(".$row->GROUP_ID.") ".$row->GROUP_NAME."</option>";
    }
?>
</select>
<input name="delete_group" type="submit" value="delete" />

</form>
</body>

</html>
