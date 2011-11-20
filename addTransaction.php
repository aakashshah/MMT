#!/usr/local/bin/php

<script language ="JavaScript" src = "trackPay.js"></script>

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
		$paidAmt = $_POST['paidAmt'];
		$emailIds = $_POST['paidEmailIds'];

		$participatedAmt = $_POST['participatedAmt'];
		$shareEmailIds = $_POST['shareEmailIds'];
		
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


		$catRow = oci_fetch_object($statementCategory);
		$catId = $catRow->CAT_ID;
		
		//update transaction table
		// trans_id, cat_id, type, txn_desc, tot_amt, date
		$type = "l"; //update transaction type later, TODO
		$txn_desc = "txn desc" ; //update txn desc later , TODO
		$txn_amt = (int)$_POST['trans_amt'];
		$txn_date = $_POST['trans_date'];

		$query = "insert into transaction values ($txnId, $catId,'".$type ."','". $txn_desc."' ,$txn_amt,to_date('".$txn_date ."','yyyy-mm-dd'))";
		$statement = oci_parse($connection, $query);

		if (!oci_execute($statement))
		{
			echo $query;
			die("TRANSACTION NOT  added!");
		}

		//update SHARES table
		//email_add, trans_id, cat_id , shared_amt
		for($i=0; $i < count($participatedAmt); $i++)
		{
			$query = "insert into shares values ('$shareEmailIds[$i]', $txnId, $catId,$participatedAmt[$i] )";
			$statement = oci_parse($connection, $query);

			if (!oci_execute($statement))
			{
				echo $query;
				die("TRANSACTION NOT  added!");
			}
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
					$row = oci_fetch_object($statementCategory);
  					if (!$row)
					{
						print "$row not set";
						break;
					}
									
				echo "<option value = '".$row->CAT_DESC."'>  ".$row->CAT_DESC." </option>";

				}
			?>
			</select>

                        <br><br>Who Paid:<br>
			<?php
			echo $_SESSION['email'] ;
			echo "<input type='hidden' value ='".$_SESSION['email'] ."' name ='paidEmailIds[]' > </input>";
			echo "<input type='text' value = 0 name = 'paidAmt[]'> </input>";
			echo "<div id = 'whoPaid'></div>";
			echo "Add Someone: ";
			echo "<select name = 'nameWhoPaid' onClick='whoPaidFunction(this.value)'  />";
			?>
			<br>
			
                        <?php

				echo "<option value = '".$_SESSION['email']."'> ".$_SESSION['email']."</option>";

                                while(1)
                                {
                                        $row = oci_fetch_object($statement1);
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
					echo "<option value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option>";
                                }

                        ?>
                        </select>
	
                        <br><br>Who participated:<br>
			<div id = 'whoParticipated'></div> 
			Add Someone:<select name = 'who_participated' onClick='whoParticipatedFunction(this.value)' />
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

					echo "<option value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option>";
                                }
                        ?>
	<br><br>
                        </select>
			<br><br>	<input name = 'submit' type = 'submit' value = 'Submit' />
				<input name = 'Back' type = 'submit' value = 'Cancel' />
			
</form>
</body></html>
