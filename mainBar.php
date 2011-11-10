<link rel="stylesheet" href="mmt.css">

<table width="100%" border="0" cellpadding="8" cellspacing="0" bgcolor="#2D2D2D">
	<tr>
		<td>
		<a href = "home.php">Home</a>
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
				if ($row)
				{
					$give = $row->AMT;
				}
				
				$queryCollect = "select sum(with_amt) as amt from participates where with_username = '".$_SESSION['email']."'";
				$stmt = oci_parse($connection, $queryCollect);
				if (!oci_execute($stmt))
				{
					echo $queryCollect;
					die("Failed to execute query");
				}
				$row = oci_fetch_object($stmt);
				
				if ($row)
				{
					$collect = $row->AMT;
				}
				
				oci_close($connection);
			?>
			<a href='give.php'>Give&nbsp;$<?php echo $give; ?></a>
		</td>
		<td>
			<a href='collect.php'>Collect&nbsp;$<?php echo $collect; ?></a>
		</td>
		<td width="100%">
		</td>
		<td>
		<a href = "profileSettings.php">
		<?php session_start();
			echo ucfirst($_SESSION['alias']);
		?></a>
		</td>
		<td>
			<a href = "logout.php">Logout</a>
		</td>
	</tr>
</table>
