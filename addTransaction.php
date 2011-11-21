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

        if (!require("mainBar.php"))
        {
                die("Failed to include mainbar!");
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
		//find trans_id and cat_id
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

		//transaction type is always EX: expense for this page.
		$type = "EX";  
		$txn_desc = $_POST['trans_desc'] ; 
		$txn_amt = (int)$_POST['trans_amt'];
		$txn_date = $_POST['trans_date'];

		$query = "insert into transaction values ($txnId, $catId,'".$type ."','". $txn_desc."' ,$txn_amt,to_date('".$txn_date ."','yyyy-mm-dd'))";
		$statement = oci_parse($connection, $query);

		if (!oci_execute($statement))
		{
			echo $query;
			die("TRANSACTION NOT  added!");
		}


		$paidAmt = $_POST['paidAmt'];
		$paidEmailIds = $_POST['paidEmailIds'];
/*
		echo "paid:";
		print_r( $paidEmailIds);
		echo "<br>";	
		print_r($paidAmt);		
		echo "<br>";	
*/
		$sharedAmt = $_POST['sharedAmt'];
		$shareEmailIds = $_POST['shareEmailIds'];
/*
		echo "shared";
		print_r($sharedAmt);
		echo "<br>";	
		print_r($shareEmailIds);
*/
		$k = 0;//index for final array
		//find what paidEmailIds are there in shareEmailIds and calculate finalEmailIds which are there or not there in shareEmailIds
		for($i = 0 ; $i<count($paidEmailIds); $i++)
		{
			$key = array_search($paidEmailIds[$i], $shareEmailIds);
			echo "<br>".$key.$paidEmailIds[$i]."<br>";
			if($key === false) //not found
			{	
				$finalEmailIds[$k] = $paidEmailIds[$i];
				$finalAmt[$k] = -($paidAmt[$i]);
			}
			else //found
			{
				$finalEmailIds[$k] = $paidEmailIds[$k];
				$finalAmt[$k] = $sharedAmt[$key] - $paidAmt[$i];		

			}
			$k = $k + 1;			
		}
/*
		echo "<br>";	
		print_r($finalAmt);
		echo "<br>";	
		print_r($finalEmailIds);
		echo "<br>";	
*/
		//find which shareEmailIds are not there in paidEmailIds and calculate finalEmailIds for them.
		for($i = 0 ; $i<count($shareEmailIds); $i++)
		{
			$key = array_search($shareEmailIds[$i], $paidEmailIds);
			if($key === false) //not found
			{	
				$finalEmailIds[$k] = $shareEmailIds[$i];
				$finalAmt[$k] = $sharedAmt[$i];
			}
			$k = $k + 1;
		}

		//sort finalAmt[] along with finalEmailIds
		array_multisort($finalAmt,$finalEmailIds);
/*
		echo "<br>After sorting:<br>";
		print_r($finalAmt);
		echo "<br>";	
		print_r($finalEmailIds);
		echo "<br>";	
*/
		//cache amounts to be paid and insert into participates table
		$i = 0;
		$j = count($finalAmt)-1;
		while($i <= $j)
		{
			if($finalAmt[$i] ==0 || $finalAmt[$j] ==0 ) break;
			
			if( -($finalAmt[$i]) <= $finalAmt[$j])
			{	
				//email_id,with_username,trans_id,cat_id,with_amt
				$query = "insert into participates values ('".$finalEmailIds[$i]."','".$finalEmailIds[$j]."',$txnId ,$catId  , -($finalAmt[$i]))";
				$statement = oci_parse($connection, $query);
				if (!oci_execute($statement))
				{
					echo $query;
					die("insertion into participates table failed!");
				}
				$finalAmt[$j] += $finalAmt[$i];
				$finalAmt[$i] = 0;
				++$i;
			}
			else
			{
				$query = "insert into participates values ('".$finalEmailIds[$i]."','".$finalEmailIds[$j]."',$txnId, $catId, $finalAmt[$j])";
				$statement = oci_parse($connection, $query);
				if (!oci_execute($statement))
				{
					echo $query;
					die("insertion into participates table failed!");
				}
				$finalAmt[$i] += $finalAmt[$j];
				$finalAmt[$j] = 0;
				--$j;
			}
		}

	
	
		//update SHARES table
		//email_add, trans_id, cat_id , shared_amt
		for($i=0; $i < count($sharedAmt); $i++)
		{
			$query = "insert into shares values ('$shareEmailIds[$i]', $txnId, $catId,$sharedAmt[$i] )";
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
			<br>Description:<input name = 'trans_desc' type = 'text'/>
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

                        <br><br><b>Who Paid:</b><br>
			<?php
			echo "<div id = 'whoPaid'></div>";
			echo "Add Someone: ";
			echo '<select name = "nameWhoPaid" onClick="whoPaidFunction(this.value,\'whoPaid\')"  />';
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
	
                        <br><br><b>Who participated:</b><br>
			<div id = 'whoParticipated'></div> 
			Add Someone:<select name = "who_participated" onClick="whoParticipatedFunction(this.value,'whoParticipated')" />
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
