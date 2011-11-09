#!/usr/local/bin/php

<link rel="stylesheet" href="mmt.css">

<table width="100%" border="0" cellpadding="8" cellspacing="0" bgcolor="#2D2D2D">
	<tr>
		<td>
		<a href = "home.php">Home</a>
		</td>
		<td>
			<?php
				$give = 0;
				$collect = 0;
				
				$queryGive = "select sum(with_amt) from participates where email_add = ".$_SESSION['email'];
				$queryCollect = "select sum(with_amt) from participates where with_username = ".$_SESSION['email'];
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
