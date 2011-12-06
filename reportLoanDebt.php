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

	if(isset($_POST['submit']))
	{
		$personMakingPayment = $_POST['personMakingPayment'];
		$personReceivingPayment = $_POST['personReceivingPayment'];
		$paymentAmt = $_POST['paymentAmt'];
		$paymentDate = $_POST['paymentDate'];
		$paymentDescription = $_POST['paymentDescription'];
		
		echo $personMakingPayment.$personReceivingPayment.$paymentAmt.$paymentDate.$paymentDescription;

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

		//insertion into transaction
		$txn_type = "LN"; //hard-coded to "LN" type for Payment

		$catId = 0; //0 for Payment category id
		$query = "insert into transaction values ($txnId, $catId,'".$txn_type ."','". $paymentDescription."' ,$paymentAmt,to_date('".$paymentDate ."','yyyy-mm-dd'))";
		$statement = oci_parse($connection, $query);

		if (!oci_execute($statement))
		{
			//echo $query;
			die("TRANSACTION NOT  added! Date not in proper format");
		}

		//insertion into participates
		$query = "insert into participates values ('".$personReceivingPayment."','".$personMakingPayment."',$txnId ,$catId  , -($paymentAmt))";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			echo $query;
			die("insertion into participates table failed!");
		}
		echo("<br>Transaction added successfully<br>");	
		header("Location:index.php");
	}
?> 
<html><head><title>Report a Loan/Debt</title></head>
<script type = "text/javascript" src = "js/validations.js"></script>
<body>
<b>With whom are you settling the payment?</b>
<?php
	$queryFriend = "select friend_email_add from has_friends where email_add = '".$_SESSION['email']."'";
	$statementFriend = oci_parse($connection, $queryFriend);
	if(!oci_execute($statementFriend))
	{
		echo $queryFriend;
		die("Failed to execute query!");
	}
	echo "<div id = 'whoSettled'></div>";
	echo '<select id = "nameWhoSettled" onClick = "debt(2)" >';
	while(1)
	{
		$row = oci_fetch_object($statementFriend);
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
		echo "<option value = '".$row->FRIEND_EMAIL_ADD."'>".$friendName->NAME." (".$row->FRIEND_EMAIL_ADD.")</option><br/>";
	}
	echo "</select>";
?>
	<br /><br />
	<b>Who owes money? </b>
	<br />


        <input type="radio" id = "payment"  name="payment" onclick="debt(0);" value = "debt" />  The other person owes me (loan)  
	<br/>
        <input type="radio" id = "payment"  name="payment" onclick="debt(1);" value = "loan" />I owe the other person (debt)

	<form name = "reportPayment" action = "reportLoanDebt.php" onsubmit = "return validateReportLoanDebt()" method="post">
        <span id="update"></span>
	<br/><br/>
	<input type="submit" name = "submit" value ="Submit">
	<input type="submit" name = "back" value="Cancel">
	</form>
        <script type="text/javascript">
            function debt(received){
		    var selected = document.getElementById("nameWhoSettled");
		    var userToSettleWith = selected.options[selected.selectedIndex].value;
			
		    //if user is changed
	            if(received == 2) {
			    document.getElementById("update").innerHTML = "";	
			    document.getElementById("payment").checked = false;	
			    return;
		    }

		    //if received 
		    if(received == 1)
		    {
			    document.getElementById("update").innerHTML = " \
			    <table border = 0><tr><td><b>Description:</b></td><td>You received loan from " + userToSettleWith  +"</td></tr> \
			    <tr><td><b>Person giving loan:</b></td><td> "+userToSettleWith+ "</td></tr>\
			    <tr><td><b>Person receiving loan:</b></td><td><? echo $_SESSION['email']; ?> (you)</td></tr></table><br/><br/><input type='hidden' name = 'personMakingPayment' value = '" + userToSettleWith + "'> </input> <input type='hidden' name = 'personReceivingPayment' value= '<?echo $_SESSION['email'];?>'> </input>" ;
		    }
		    //if paid
		    else if(received == 0)
		    {
			    document.getElementById("update").innerHTML = " \
			    <table border = 0><tr><td><b>Description:</b></td><td>You gave loan to " + userToSettleWith +" </td></tr>\
			    <tr><td><b>Person giving loan:</b></td><td> <?php echo $_SESSION['email']; ?> (you)</td></tr>\
			    <tr><td><b>Person receiving loan:</b></td><td> "+ userToSettleWith + "</td></tr></table><br/><br/><input type='hidden' name='personMakingPayment' value='<? echo $_SESSION['email']; ?>' > </input> <input type='hidden' name = 'personReceivingPayment' value= "+ userToSettleWith +"></input>" ;
		    }	

		    document.getElementById("update").innerHTML += " \
		    <table border =0 ><tr><td><b>Total amount:</b></td><td><input type= 'text' name = 'paymentAmt' /> USD $  </td></tr>\
		    <tr><td><b>Date:</b></td><td><input name = 'paymentDate' type = 'date' /> </td></tr> \
		    <tr><td><b>Comment:</b></td><td><input type = 'text' name = 'paymentDescription' /></td></tr></table>";
            }
        </script>




	 
</body>
</html>


