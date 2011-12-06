#!/usr/local/bin/php

<script language ="JavaScript" name=emailId>//src = "trackPay.js">
var sharedarray = new Array();
var count=0; 
var paidarray = new Array();
var count1=0;
function whoPaidFunction(emailId,divName)
{
	   if(emailId == '----')
		return;
           if(arraySearch(paidarray,emailId)==-1)
           {
                paidarray[count1]=emailId;
                count1++;
	   var newdiv = document.createElement('div');
          newdiv.innerHTML = emailId +"&nbsp; &nbsp;  <input type = 'text' name = 'paidAmt[]'  value = 0></input>   <input type='hidden' name = 'paidEmailIds[]' value = " + emailId + "> </input> " ;
          document.getElementById(divName).appendChild(newdiv);
	   }
}

function arraySearch(arr,val)
{
	//document.write(arr);
	for (var i=0; i<arr.length; i++)
	{
    		//document.write(arr[i]);
		//alert(arr[i]+" "+val+i);
		if (arr[i].toString() == val.toString())
		{
			//alert('Already Selected!!!');
			return 1;
		}
		//document.write("Not Found");
	}
	return -1;
}
function form_input_is_numeric(input)
{
    return !isNaN(input);
}
function whoParticipatedFunction(emailId,divName)
{
	if(emailId == '----')
		return;
        var newdiv = document.createElement('div');
        if(emailId.substring(0,5)=="Group")
        {
		var ajax;
		ajax = new XMLHttpRequest();
		ajax.onreadystatechange=function()
  		{
  			if (ajax.readyState==4 && ajax.status==200)
 			{
				var test = ajax.responseText.split(',', 1);
				var i=0;
				var tempResponse=ajax.responseText.slice(ajax.responseText.indexOf(",")+1,ajax.responseText.length);
				var groupMembers=parseInt(test);
				while(i!=groupMembers) 
				{
					var newdiv = document.createElement('div');
					var member = tempResponse.split(',', 1);
					var member1=tempResponse.slice(tempResponse.indexOf(",")+1,tempResponse.length);
					tempResponse=member1;
					i++;
					//document.write(shareEmailIds);
					if(arraySearch(sharedarray,member)==-1)
				        {	
						sharedarray[count]=member;
						count++;
						//document.write(sharedarray[count]);	
					newdiv.innerHTML = member+"&nbsp; \
					Share: <input type = 'text' name = 'sharedAmt[]' value = 0 ></input> <input type='hidden' name = 'shareEmailIds[]' value = " + member + "> </input> " ;
					 document.getElementById(divName).appendChild(newdiv);
					}
				}
    			}
  		}
		ajax.open("GET","groupinfo.php?groupName="+emailId,true);
		ajax.send();
        }
        else
	{
		if(arraySearch(sharedarray,emailId)==-1)
		{
			sharedarray[count]=emailId;
                         count++;
		newdiv.innerHTML = emailId +"&nbsp; \
	        Share: <input type = 'text' name = 'sharedAmt[]' value = 0 ></input> <input type='hidden' name = 'shareEmailIds[]' value = " + emailId + "> </input>" ;
        	document.getElementById(divName).appendChild(newdiv);
		}
	}
}
function validateCheck()
{
	var jVar = document.forms["Report_Expense"]["trans_amt"].value;
        if (null == jVar || "" == jVar)
        {
                alert("Amount cannot be blank!");
                //return false;
        }
	else if(0== jVar)
	{
		 alert("Amount cannot be Zero!");
	}
	else if(!form_input_is_numeric(jVar))
	{
		alert("Amount needs to be Numeric");
	}
}
</script>

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

        $query = "select cat_id,cat_desc from category where cat_id >0";
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
			$txnId = ($row->M) + 1;
		}


		//$catRow = oci_fetch_object($statementCategory);
		//$catId = $catRow->CAT_ID;
	
		//update transaction table
		// trans_id, cat_id, type, txn_desc, tot_amt, date
		$type = "EX"; //hard-coded to EX: expense type
		$txn_desc = $_POST['trans_desc'];
		$txn_amt = $_POST['trans_amt'];
		//if(!preg_match('/^[0-9]{1,}$/', $txn_amt)) 
		//	echo "<script>alert('Wrong Amount Entered.Please enter correct Amount!!!');</script>";
		//else
		{
		$txn_amt = (int)$_POST['trans_amt'];
		$txn_date = $_POST['trans_date'];
		$catId = $_POST['category'];//category id corrected, should be feteched from option selected
		if(strlen($txn_desc)==0)
		{
			$queryMaxTxnId = "select cat_desc as m from category where cat_id = '".$catId."'";
                	$statement = oci_parse($connection, $queryMaxTxnId);
                	if (!oci_execute($statement))
                	{
                        	echo $query;
                        	die ("Failed to execute query!");
                	}

                	$row = oci_fetch_object($statement);
			$txn_desc=$row->M;
		}
//		$query = "insert into transaction values ($txnId, $catId,'".$type ."','". $txn_desc."' ,$txn_amt,to_date('".$txn_date ."','yyyy-mm-dd'))";
		if(strlen($txn_date)==0)
                {
                        $query = "insert into transaction values ($txnId, $catId,'".$type ."','". $txn_desc."' ,$txn_amt,sysdate)";
                }
                else{
                        $query = "insert into transaction values ($txnId, $catId,'".$type ."','". $txn_desc."' ,$txn_amt,to_date('".$txn_date ."','yyyy-mm-dd'))";
                }
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
		$whoPaidAmt = 0;
		//find what paidEmailIds are there in shareEmailIds and calculate finalEmailIds which are there or not there in shareEmailIds
		for($i = 0 ; $i<count($paidEmailIds); $i++)
		{
			$key = array_search($paidEmailIds[$i], $shareEmailIds);
			//echo "<br>".$key.$paidEmailIds[$i]."<br>";
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
			$whoPaidAmt=$whoPaidAmt+$paidAmt[$i];			
		}
/*
		echo "<br>";	
		print_r($finalAmt);
		echo "<br>";	
		print_r($finalEmailIds);
		echo "<br>";	
*/
		$whosharedAmt=0;
		//find which shareEmailIds are not there in paidEmailIds and calculate finalEmailIds for them.
		for($i = 0 ; $i<count($shareEmailIds); $i++)
		{
			$key = array_search($shareEmailIds[$i], $paidEmailIds);
			if($key === false) //not found
			{	
				$finalEmailIds[$k] = $shareEmailIds[$i];
				$finalAmt[$k] = $sharedAmt[$i];
			}
			$whosharedAmt=$whosharedAmt+$sharedAmt[$i];
			$k = $k + 1;
		}
		if($txn_amt!=$whoPaidAmt)
			 echo "<script>alert('Please check Amount in Who Paid and Who Particiapted fields!!!');</script>";
		else if($whosharedAmt!=$whoPaidAmt)
			echo "<script>alert('Paid and Contribution Amount not Matching. Please enter correct Amount!!!');</script>";
		else
		{
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
				die("TRANSACTION NOT added!");
			}
		}
		header("Location:home.php");
		}
		}
	}
?>

<html><head><title>Report Expense</title></head>
<body>
<form name = 'Report_Expense' action = 'addTransaction.php' onsubmit = 'return validateCheck() 'method = 'post'>
		
			Date:<input name = 'trans_date' type = 'date' />
			<br>Total Amount:<input name = 'trans_amt' type = 'integer' />
			<br>Description:<input name = 'trans_desc' type = 'text' />
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
									
				echo "<option value = '".$row->CAT_ID."'>  ".$row->CAT_DESC." </option>";

				}
			?>
			</select>

                        <br><br><b>Who Paid:</b><br>
			<?php
			echo "<div id = 'whoPaid'></div>";
			echo "Add Someone: ";
			echo '<select name = "nameWhoPaid" onChange="whoPaidFunction(this.value,\'whoPaid\')"  />';
        		echo '<option selected>----</option>';
			?>
			<br>
			
                        <?php
				echo "<option value = '".$_SESSION['email']."'> ".$_SESSION['email']."</option>";
				$count=0;	
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
					$paidlist[$count]=$row->FRIEND_EMAIL_ADD;
					$count=$count+1;
                                }
				for($i=0;$i<$count;$i++)
					echo  "<option value = '".$paidlist[$i]."'>".$friendName->NAME." (".$paidlist[$i].")</option>";

                        ?>
                        </select>
	
                        <br><br><b>Who participated:</b><br>
			<div id = 'whoParticipated'></div> 
			Add Someone:<select name = "who_participated" onChange="whoParticipatedFunction(this.value,'whoParticipated')" />
			echo '<option selected>----</option>';
			<?php
					$query1 = "select friend_email_add from has_friends where email_add = '".$_SESSION['email']."'";
					$statement1 = oci_parse($connection, $query1);
					if (!oci_execute($statement1))
					{
					echo $query1;
					die("Failed to execute query!");
					}	

				print $statement1;
				//$query2 = "select group_name from UserGroup where group_id in (select group_id from belongs_to  where email_add = '".$_SESSION['email']."')";
                                echo "<option value = '".$_SESSION['email']."'> ".$_SESSION['email']."</option>";
				$query2 = "select group_name from UserGroup where GROUP_OWNER =  '".$_SESSION['email']."'";
				$statement2 = oci_parse($connection, $query2);
				if (!oci_execute($statement2))
                                {
                                        echo $query2;
                                        die("Failed to execute query!");
                                }
				while(1)
                                {
                                        $row1 = oci_fetch_object($statement2);
                                        if (!$row1)
                                        {
                                                print "breaking";
                                                break;
                                        }
                                        echo "<option value = '".$row1->GROUP_NAME."'>GROUP:".$row1->GROUP_NAME."</option>";
                                }
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
