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

        $query = "select cat_id,cat_desc from category";
	$statementCategory = oci_parse($connection, $query);
	if (!oci_execute($statementCategory))
	{
		echo $query;
		die("Failed to execute query!");
	}

?> 
<html><head><title>Report a Payment</title></head>
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
	echo '<select id = "nameWhoSettled" >';
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
	<b>Who Paid Whom: </b>
	<br />


        <input type="radio" name="payment" onclick="payment(0);" value = "madePayment" /> You made a Payment 
        <br />
        <input type="radio" name="payment" onclick="payment(1);" value = "receivedPayment" /> You received a Payment
        <br />
        <span id="update"></span>
        <script type="text/javascript">
            function payment(received){
		    var selected = document.getElementById("nameWhoSettled");
		    var userToSettleWith = selected.options[selected.selectedIndex].value;

		    //if received 
		    if(received == 1)
		    {
		    document.getElementById("update").innerHTML = " \
		    <br /><b>Description:</b>You received from " + userToSettleWith  +" \
		    <br /><b>Person making payment:</b> "+userToSettleWith+ "\
		    <br /><b>Person receiving payment:</b><? echo $_SESSION['email']; ?> (you)"; 
		    }
		    //if paid
		    else if(received == 0)
		    {
			    document.getElementById("update").innerHTML = " \
			    <br /><b>Description:</b>You paid " + userToSettleWith +" \
			    <br /><b>Person making payment:</b> <?php echo $_SESSION['email']; ?> (you)\
			    <br /><b>Person receiving payment:</b> "+ userToSettleWith  ;
		    }	

		    document.getElementById("update").innerHTML += " \
		    <br /><b>Total amount:</b><input type= 'text' name = 'paymentAmt' /> USD $  \
		    <br /><b>Date:</b><input name = 'paymentDate' type = 'date' /> \
		    <br /><b>Comment:</b><input type = 'text' name = 'paymentDescription' /> ";
            }
        </script>




	 
</body>
</html>


