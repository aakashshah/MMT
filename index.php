#!/usr/local/bin/php

<html>
<head>
<title>Welcome to MMT</title>
</head>
<body>

<form name = 'login' action = 'index.php' method = 'post'>
	<table align = 'center' border = '0'>
		<tr>
			<td>Username:</td><td><input name = 'username' type = 'text' /></td>
		</tr>
		<tr>
			<td>Password:</td><td><input name = 'password' type = 'password' /></td>
		</tr>
		<tr>
			<td align = 'center' colspan = '2'><input name = 'login' type = 'submit' value = 'Login' /></td>
		</tr>
	</table>
</form>

</body>
</html>
