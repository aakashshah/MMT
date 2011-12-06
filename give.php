#!/usr/local/bin/php

<?php session_start();

	if (!isset($_SESSION['email']))
	{
		header("Location:index.php");
	}
	
	if (!require("mainBar.php"))
	{
		die("Failed to include mainbar!");
	}
?>

<html>
<head><title>Give Details - MMT</title>
</head>

<body>
	<br /></br >
	<!-- A table with 2 columns, one figure and the other as details -->
	<table align = "center" width = "100%">
		<tr>
			<td align = "center" valign = "top">
	<table class = "transactions">
	<?php
		/* First check for the actual 'give' type (right column summation) */
		$firstRow = 1;
		$query = "select friend, sum(total) as amt from ((select with_username as friend, with_amt as total from participates where email_add = '".$_SESSION['email']."') union	(select email_add as friend, (-with_amt) as total from participates where with_username = '".$_SESSION['email']."')) group by friend";
		$amtSet = "";
		$amtLabelSet = "";
		$nameSet = "";
		$statement = oci_parse($connection, $query);
		if (!oci_execute($statement))
		{
			die($query);
		}

		while ($row = oci_fetch_object($statement))
		{
			/* If the amount is 0 or negative, it should not be displayed */
			if ($row->AMT >= 0)
			{
				continue;
			}
			
			$row->AMT = (-$row->AMT);

			if ($firstRow == 1)
			{
				echo "<tr><td class = 'transactions'>Friend</td><td class = 'transactions'>Amount</td></tr>";
				$firstRow = 0;
				$amtSet = "".$row->AMT;
				$amtLabelSet = $row->FRIEND." (".$row->AMT.")";
				$nameSet = "".$row->FRIEND;
			}
			else
			{
				$amtSet = $amtSet.",".$row->AMT;
				$amtLabelSet = $amtLabelSet."|".$row->FRIEND." (".$row->AMT.")";
				$nameSet = $nameSet."|".$row->FRIEND;
			}
			echo "<tr><td class = 'transactions'>".$row->FRIEND."</td><td class = 'transactions'>".$row->AMT."</td></tr>";
		}
	?>
	</table>
			</td>
			<td align = "center" valign = "top">
	<?php
	/* If there is nothing to display, skip displaying the chart */
	if (0 == $firstRow)
	{
		echo "<img src='http://chart.apis.google.com/chart?chs=700x300&cht=p3&chd=t:".$amtSet."&chl=".$amtLabelSet."&chdl=".$nameSet."&chdlp=b&chtt=Amount Breakup' alt='Amount Breakup' />";
	}
	else
	{
		echo "<center>No transactions found!</center>";
	}
	?>
			</td>
		</tr>
	</table>
</body>
</html>
