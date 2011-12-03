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

    if(isset($_POST['Add']))
    {
        $query = "select MAX(cat_id) as m from Category";
        $statement = oci_parse($connection, $query);
        if (!oci_execute($statement))
        {
            echo $query;
            die ("Failed to execute query!");
        }

        $row = oci_fetch_object($statement);
        if (!$row)
        {
            $catId = 1;
        }
        else
        {
            $catId = ($row->M) + 1;
        }

        $finalquery = "insert into Category values (".$catId.",'".$_POST['cat_desc']."')";

        $finalStatement = oci_parse($connection, $finalquery);

        if (!oci_execute($finalStatement))
        {
            echo $finalquery;
            die("Category could not be added!");
        }
        header("Location:home.php");
    }
    else if (isset($_POST['delete_cat']))
    {
        $query = "delete from Category where cat_id = ".$_POST['deleting_cat'];
        $statement = oci_parse($connection, $query);

        if (!oci_execute($statement))
        {
            echo "<br /><br />There are some transactions belonging to category ".$_POST['deleting_cat'].". Please delete those first.";
        }
    }
?>

<html>
<head><title>Modify Categories - MMT</title></head>
<body>

<br />
<br />

<table class = "transactions" align = "center">
<form name="input" action="modCategories.php" method="post">
    <tr>
        <td class = "transactions" colspan="3">Choose one of the option below:</td>
    </tr>
    <tr>
        <td>Add a category:</td>
        <td><input type="text" name="cat_desc" /></td>
        <td><input class="mainButton" name="Add" type="submit" value="Add" /></td>
    </tr>
    <tr>
        <td>Select a category to delete:</td>
        <td>
            <select name="deleting_cat">
                <option selected>----</option>
                <?php
                $query = "select cat_id from Category";
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
        
                    $subQuery = "select cat_desc from category where cat_id = '".$row->CAT_ID."'";
                    $subStatement = oci_parse($connection, $subQuery);
                    if (!oci_execute($subStatement))
                    {
                        echo $subQuery;
                        die("Failed to execute subquery!");
                    }
        
                    $catName = oci_fetch_object($subStatement);
        
                    echo "<option name= 'delete_cat' value = '".$row->CAT_ID."'>(".$row->CAT_ID.") ".$catName->CAT_DESC."</option>";
                }
                ?>
            </select>
        </td>
        <td><input class = "mainButton" name="delete_cat" type="submit" value="Delete" /></td>
    </tr>
</table>

</form>
</body>

</html>
