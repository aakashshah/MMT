<script language ="JavaScript" src = "whoPaid.js"></script>

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

        $query = "select cat_id,cat_desc from category";
	$statementCategory = oci_parse($connection, $query);
	if (!oci_execute($statementCategory))
	{
		echo $query;
		die("Failed to execute query!");
	}
        $query1 = "select friend_email_add from has_friends where email_add = '".$_SESSION['email']."'";
        $statement1 = oci_parse($connection, $query1);
        if (!oci_execute($statement1))
        {
                echo $query1;
                die("Failed to execute query!");
        }	
        if(isset($_POST['submit']))
	{   
		$queryMaxTxnId = "select MAX(trans_id) as m from transaction";
		$statement = oci_parse($connection, $queryMaxTxnId);
		if (!oci_execute($statement))
		{
			echo $query;
			die ("Failed to execute query!");
		}
		
		$row = oci_fetch_object($statement);
		if (!$row)
		{
			$txnId = 1;
		}
		else
		{
			echo $row->M;
			$txnId = ($row->M) + 1;
		}


		$catId = oci_fetch_object($statementCategory,"cat_id");
		
		// trans_id, cat_id, type, txn_desc, tot_amt, date
		$query = "insert into transaction values (".$txnId.", '', )";
		$statement = oci_parse($connection, $query);

		if (!oci_execute($statement))
		{
			echo $query;
			die("tRANSACTION NOT  added!");
		}
		// email_add, trans_id, shared_amt
                $query = "insert into shares values ('', '', )";
                $statement = oci_parse($connection, $query);

                if (!oci_execute($statement))
                {
                        echo $query;
                        die("TRANSACTION NOT  added!");
                }
		header("Location:home.php");
	}
?>

<html><head><title>Report Expense</title></head>
<body>

<form name = 'Report_Expense' action = 'addTransaction.php' method = 'post'>
		
			Date:<input name = 'trans_date' type = 'date' />
			<br>Total Amount:<input name = 'trans_amt' type = 'integer' />
			<br>Category:<select name = 'category'  />
			<?php
				while(1)
				{
					$row = oci_fetch_object($statementCategory,"cat_desc");
					//$row="Food";
  					if (!$row)
					{
						print "$row not set";
						break;
					}
									
				//$cat_desc= mysql_result($result,$i,0);
				//	$cat_desc=oci_result($stmt, "CAT_DESC");
				//	$cat_id=oci_result($result,"cat_id");
					echo "<option value = '".$row."'>  ".$row." </option>";

				}
			?>
			</select>

                        <br><br>Who Paid:<br>
			<?php
			echo $_SESSION['email'] ;
			echo "<input value = 0> </input>";
			echo "<div id = 'txtHint'></div>";
			?>
			<br>
			<select name = 'whoPaid' onChange="whoPaidFunc(this.value)"  />
			
                        <?php

				echo "<option value = '".$_SESSION['email']."'> ".$_SESSION['email']."</option>";

                                while(1)
                                {
                                        $row = oci_fetch_object($statement1);
                                        //$row="Food";
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
                                //$cat_desc= mysql_result($result,$i,0);
                                //      $cat_desc=oci_result($stmt, "CAT_DESC");
                                //      $cat_id=oci_result($result,"cat_id");
					 echo "<option value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option>";
                                }

                        ?>
                        </select>
	
                        <br><br>Who participated:<select name = 'who_participated'  />
			<?php
					$query1 = "select friend_email_add from has_friends where email_add = '".$_SESSION['email']."'";
					$statement1 = oci_parse($connection, $query1);
					if (!oci_execute($statement1))
					{
					echo $query1;
					die("Failed to execute query!");
					}	

				print $statement1;
				echo "<option value = '".$_SESSION['email']."'> ".$_SESSION['email']."</option>";
				while(1)
                                {
                                        $row = oci_fetch_object($statement1);
                                        //$row="Food";
                                        if (!$row)
                                        {
						print "breaking";
                                                break;
                                        }
					$subQuery = "select name from users where email_add = '".$row->FRIEND_EMAIL_ADD."'";
			
					$subStatement = oci_parse($connection, $subQuery);
					if (!oci_execute($subStatement))
					{
						echo $subStatement;
						die("Failed to execute subquery!");
					}

					$friendName = oci_fetch_object($subStatement);	

                                //$cat_desc= mysql_result($result,$i,0);
                                //      $cat_desc=oci_result($stmt, "CAT_DESC");
                                //      $cat_id=oci_result($result,"cat_id");
					 echo "<option value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option>";
                                }
                        ?>
	<br><br>
                        </select>
			Share: <input name = 'share_amt' type = 'integer' />

			<br><br>	<input name = 'submit' type = 'submit' value = 'Submit' />
				<input name = 'Back' type = 'submit' value = 'Cancel' />
			
</form>
</body></html>
