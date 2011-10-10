#!/usr/local/bin/php
<?php
	$connection = oci_connect($username = 'aakash',
				  $password = 'password',
				  $connection_string = '//oracle.cise.ufl.edu/orcl');

	if (!$connection)
	{
		die("Connection Failed");
	}

	//USERS
	$statement = oci_parse($connection, 'select 1 from users');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table users(
							email_add varchar(32),
							password varchar(32),
							bank_balance int,
							ph_no int,
							monthly_budget int,
							constraint user_pk primary key (email_add))');
		if (!oci_execute($statement))
		{
			die("USERS table creation failed!");
		}
		echo "\nUSERS table created!\n";
	}
	else
	{
		echo "\nUSERS table already present!\n";
	}

	//CATEGORY
	$statement = oci_parse($connection, 'select 1 from category');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table category(
							cat_id int,
							cat_desc varchar(64),
							constraint category_pk primary key (cat_id))');
		if (!oci_execute($statement))
		{
			die("CATEGORY table creation failed!");
		}
		echo "\nCATEGORY table created!\n";
	}
	else
	{
		echo "\nCATEGORY table already present!\n";
	}

	//TRANSACTION
	$statement = oci_parse($connection, 'select 1 from transaction');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table transaction(
							trans_id int,
							cat_id int,
							type char(2),
							txn_desc varchar(64),
							tot_amt int,
							txn_date date,
							constraint transaction_pk primary key (trans_id, cat_id),
							constraint transaction_fk foreign key (cat_id) references category(cat_id))');
		if (!oci_execute($statement))
		{
			die("TRANSACTION table creation failed!");
		}
		echo "\nTRANSACTION table created!\n";
	}
	else
	{
		echo "\nTRANSACTION table already present!\n";
	}

	//USERGROUP
	$statement = oci_parse($connection, 'select 1 from usergroup');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table usergroup(
							group_id int,
							group_name varchar(64),
							constraint group_pk primary key (group_id))');
		if (!oci_execute($statement))
		{
			die("USERGROUP table creation failed!");
		}
		echo "\nUSERGROUP table created!\n";
	}
	else
	{
		echo "\nUSERGROUP table already present!\n";
	}

	//HAS_FRIENDS
	$statement = oci_parse($connection, 'select 1 from has_friends');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table has_friends(
							email_add varchar(32),
							friend_email_add varchar(32),
							dues int,
							constraint has_friends_pk primary key (email_add, friend_email_add),
							constraint has_friends_fk1 foreign key (friend_email_add) references users(email_add),
							constraint has_friends_fk2 foreign key (email_add) references users(email_add))');
		if (!oci_execute($statement))
		{
			die("HAS_FRIENDS table creation failed!");
		}
		echo "\nHAS_FRIENDS table created!\n";
	}
	else
	{
		echo "\nHAS_FRIENDS table already present!\n";
	}

	//BELONGS_TO
	$statement = oci_parse($connection, 'select 1 from belongs_to');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table belongs_to(
							email_add varchar(32),
							group_id int,
							constraint belongs_to_pk primary key (email_add, group_id),
							constraint belongs_to_fk1 foreign key (email_add) references users(email_add),
							constraint belongs_to_fk2 foreign key (group_id) references usergroup(group_id))');
		if (!oci_execute($statement))
		{
			die("BELONGS_TO table creation failed!");
		}
		echo "\nBELONGS_TO table created!\n";
	}
	else
	{
		echo "\nBELONGS_TO table already present!\n";
	}

	//PARTICIPATES
	$statement = oci_parse($connection, 'select 1 from participates');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table participates(
							email_add varchar(32),
							with_username varchar(32),
							trans_id int,
							with_amt int,
							constraint participates_pk primary key (email_add, with_username, trans_id),
							constraint participates_fk1 foreign key (email_add) references users(email_add))');
		if (!oci_execute($statement))
		{
			die("PARTICIPATES table creation failed!");
		}
		echo "\nPARTICIPATES table created!\n";
	}
	else
	{
		echo "\nPARTICIPATES table already present!\n";
	}

	//SHARES
	$statement = oci_parse($connection, 'select 1 from shares');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table shares(
							email_add varchar(32),
							trans_id int,
							shared_amt int,
							constraint shares_pk primary key (email_add, trans_id))');
		if (!oci_execute($statement))
		{
			die("SHARES table creation failed!");
		}
		echo "\nSHARES table created!\n";
	}
	else
	{
		echo "\nSHARES table already present!\n";
	}


	oci_free_statement($statement);
	oci_close($connection);
?>
