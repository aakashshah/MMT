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
				
				$queryGive = "select sum(with_amt) as amt from participates where email_add = '".$_SESSION['email']."'";
				$stmt = oci_parse($connection, $queryGive);
				if (!oci_execute($stmt))
				{
					echo $queryGive;
					die("Failed to execute query");
				}
				$row = oci_fetch_object($stmt);
				if ($row->AMT)
				{
					$collect = $row->AMT;
				}
				
				$queryCollect = "select sum(with_amt) as amt from participates where with_username = '".$_SESSION['email']."'";
				$stmt = oci_parse($connection, $queryCollect);
				if (!oci_execute($stmt))
				{
					echo $queryCollect;
					die("Failed to execute query");
				}
				$row = oci_fetch_object($stmt);
				
				if ($row->AMT)
				{
					$give = $row->AMT;
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
<table border = "0" cellpadding = "0" cellspacing = "0">
	<tr>
		<?php
			$budgetQuery = "select sum(shared_amt) as amt from shares s, transaction t where t.trans_id = s.trans_id and t.txn_date like '%".strtoupper(date('M-y'))."'";
			
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
				echo "keep track of your expenses. define monthly budget now!";
			}
			else // the monthly budget is defined
			{
				if ($monthExp > $_SESSION['mbudget'])
				{
					echo "budget overdue by $".($_SESSION['mbudget'] - $monthExp)."!";
				}
				else
				{
					/* Calculate Percentage */
					$monthExp = ($monthExp / $_SESSION['mbudget']) * 100;
					$monthExp = round($monthExp);
					
					/* Colour used as royalblue */
					echo "<td bgcolor='royalblue' width='".$monthExp."'></td>";
					echo "<td bgcolor='#A4C639' width='100%'></td>";
				}
			}
		?>
	</tr>
</table>
