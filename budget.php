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
<head><title>Budget Split - MMT</title>
</head>

<body>
	<br /></br >
	<img src="http://chart.apis.google.com/chart?chxr=0,-10,100&chxs=0,676767,13.5,0,l,676767&chxt=x&chbh=a&chs=300x225&cht=bvg&chco=A2C180&chds=0,130&chd=t:10,50,60,80,40,60,30,120&chtt=Vertical+bar+chart" width="300" height="225" alt="Vertical bar chart" />
</body>
</html>
