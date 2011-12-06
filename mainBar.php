<link rel="stylesheet" href="mmt.css">

<table width="100%" border="0" cellspacing="0" bgcolor="#2D2D2D">
	<tr>
		<td>
		<a class = "bar" href = "home.php">Home</a>
		</td>
		<td>
			<?php
				if (!require("connection.php"))
				{
					// connection failure return error code 1
					exit(1);
				}
				$give = 0;
				$collect = 0;
				
				$query = "select friend, sum(total) as amt from ((select with_username as friend, with_amt as total from participates where email_add = '".$_SESSION['email']."') union	(select email_add as friend, (-with_amt) as total from participates where with_username = '".$_SESSION['email']."')) group by friend";
				
				$stmt = oci_parse($connection, $query);
				if (!oci_execute($stmt))
				{
					echo $queryCollect;
					die("Failed to execute query");
				}
				
				while ($row = oci_fetch_object($stmt))
				{
					if ($row->AMT > 0)
					{
						$collect = $collect + $row->AMT;
					}
					else if ($row->AMT < 0)
					{
						$give = $give + (-$row->AMT);
					}
					/* We do not want to calculate 0 sums */
				}
			?>
			<a class = "bar" href='give.php'>Give&nbsp;$<?php echo $give; ?></a>
		</td>
		<td>
			<a class = "bar" href='collect.php'>Collect&nbsp;$<?php echo $collect; ?></a>
		</td>
		<td width="100%">
			<form name = "searchform" action = "search.php" method = "get">
			<input class = "searchBar" name = "searchString" type = "text" value = "Search..." onfocus="this.value = '';"/>
			<input type = "submit" style="visibility:hidden" />
			</form>
		</td>
		<td>
			<a class = "bar" href='profileSettings.php'>Monthly&nbsp;Budget:&nbsp$<?=$_SESSION['mbudget'];?></a>
		</td>
		<td>
		<a class = "bar" href = "profileSettings.php" title = "<?php echo $_SESSION['email'] ?>">
		<?php session_start();
			echo ucfirst($_SESSION['alias']);
		?></a>
		</td>
		<td>
			<a class = "bar" href = "logout.php" onclick = "if (!confirm('Are you sure?')) return false;">Logout</a>
		</td>
	</tr>
</table>
<!-- This is to implement the monthly budget bar -->
<table border = "0" cellpadding = "0" cellspacing = "0" width = "100%">
	<tr>
		<?php
			$budgetQuery = "select sum(shared_amt) as amt from shares s, transaction t where t.trans_id = s.trans_id and s.email_add = '".$_SESSION['email']."' and t.txn_date like '%".strtoupper(date('M-y'))."'";
			
			/* If the query fails to execute, the expense is taken to be 0. from
			database it will be updated later */
			$monthExp = 0;
			
			$stmt = oci_parse($connection, $budgetQuery);
			if (!oci_execute($stmt))
			{
				echo $budgetQuery;
				die("Failed to execute query");
			}
			$row = oci_fetch_object($stmt);
			if ($row->AMT)
			{
				$monthExp = $row->AMT;
			}
			
			//echo $monthExp;
			/* If the monthly budget is not defined */
			if ($_SESSION['mbudget'] == 0)
			{
				echo "<td bgcolor='#A4C639' width='100%'><font color='#000000' size='3'><a href='profileSettings.php'>Keep track of your expenses! Click here to define monthly budget now!</a></font></td>";
			}
			else // the monthly budget is defined
			{
				if ($monthExp > $_SESSION['mbudget'])
				{
					echo "<td bgcolor='#A4C639' width='100%'><font color='#000000' size='3'>Budget overdue by $".($monthExp - $_SESSION['mbudget'])."!</font></td>";
				}
				else
				{
					/* Calculate Percentage */
					$monthPercentage = ($monthExp / $_SESSION['mbudget']) * 100;
					$monthPercentage = round($monthPercentage);
					
					/* Display in used td */
					if ($monthPercentage < 50)
					{
						$unUsedString = "<font color='#000000' size='2'>$".$monthExp." of $".$_SESSION['mbudget']." used</font>";
						$usedString = "";
					}
					else // disply in unused td
					{
						$usedString = "<font color='#000000' size='2'>$".$monthExp." of $".$_SESSION['mbudget']." used</font>";
						$unUsedString = "";
					}
					
					/* Colour used as blue */
					echo "<td bgcolor='#99CCFF' width='".$monthPercentage."%' align='right'>".$usedString."</td>";
					echo "<td bgcolor='#A4C639' width='".(100 - $monthPercentage)."%'>".$unUsedString."</td>";
					
					/* Check if mail needs to be sent to the user */
					if ($monthPercentage > 90)
					{
						if (!isset($_SESSION['notification'])
							|| ($_SESSION['notification'] == 'notsent'))
						{
							$to = $_SESSION['email'];
							$headers = "From: admin@mmt.com";
							$subject = "Budget Overdue Notification";
							$message = "Greetings ".ucfirst($_SESSION['alias']).",\n\nThis is to notify that your monthly expense is nearing your budget limit or has already exceeded!\nYour current monthly expense: $".$monthExp.".\nYour defined monthly budget is: $".$_SESSION['mbudget'].".\n\nYours truly,\nAdminstrator\nMMT.com\n";
							mail($to, $subject, $message, $headers);
						}
						
						$_SESSION['notification'] = 'sent';
					}
					else
					{
						$_SESSION['notification'] = 'notsent';
					}
				}
			}
		?>
	</tr>
</table>
