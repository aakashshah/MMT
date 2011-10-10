#!/usr/local/bin/php

<?php
	$connection = oci_connect($username = 'aakash',
				  $password = 'password',
				  $connection_string = '//oracle.cise.ufl.edu/orcl');

	if (!$connection)
	{
		die("Connection Failed");
	}

	echo "\nBegin to drop tables...\n";

	$statement = oci_parse($connection, 'drop table shares');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table participates');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table belongs_to');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table has_friends');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table usergroup');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table transaction');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table category');
	oci_execute($statement);

	$statement = oci_parse($connection, 'drop table users');
	oci_execute($statement);

	echo "\nAll tables dropped...\n";

	oci_free_statement($statement);
	oci_close($connection);

?>
