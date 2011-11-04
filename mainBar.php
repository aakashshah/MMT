#!/usr/local/bin/php

<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#2D2D2D" class="mmtStyle">
	<tr>
		<td>
		<font color="#FFFFFF">
		<?php session_start();
			echo "Welcome ".ucfirst($_SESSION['alias']);
		?></font>
		</td>
		<td align = "right">
			<a href = "logout.php">Logout</a>
		</td>
	</tr>
</table>
