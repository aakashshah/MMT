#!/usr/local/bin/php

<link rel="stylesheet" href="mmt.css">

<table width="100%" border="0" cellpadding="8" cellspacing="0" bgcolor="#2D2D2D">
	<tr>
		<td>
		<a href = "home.php">Home</a>
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
