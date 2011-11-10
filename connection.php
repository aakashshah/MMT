<?php
	$connection = oci_connect($username = 'aakash',
				  $password = 'password',
				  $connection_string = '//oracle.cise.ufl.edu/orcl');

	if (!$connection)
	{
		die("Connection to database ".$connection_string." failed!");
	}

?>
