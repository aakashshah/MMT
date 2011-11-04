#!/usr/local/bin/php

<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#2D2D2D">
	<tr>
		<td>
		<font color="#FFFFFF">Home</font>
		</td>
		<td width="100%">
		</td>
		<td>
		<font color="#FFFFFF">
		<?php session_start();
			echo ucfirst($_SESSION['alias']);
		?></font>
		</td>
		<td>
			<a href = "logout.php">Logout</a>
		</td>
	</tr>
</table>
