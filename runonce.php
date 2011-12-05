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
							password varchar(32) not null,
							name varchar(32),
							bank_balance numeric(10,2),
							ph_no varchar(16),
							monthly_budget numeric,
							constraint user_pk primary key (email_add))');
		if (!oci_execute($statement))
		{
			die("USERS table creation failed!");
		}
		echo "<br />USERS table created!<br />";
	}
	else
	{
		echo "<br />USERS table already present!<br />";
	}
	
	/* Create admin always */
	$statement = oci_parse($connection, "insert into users values ('admin@mmt.com', '21232f297a57a5a743894a0e4a801fc3', 'admin', 0, 0, 0)");
	oci_execute($statement);

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
		echo "<br />CATEGORY table created!<br />";
	}
	else
	{
		echo "<br />CATEGORY table already present!<br />";
	}
	
	/* Create default categories */
	$statement = oci_parse($connection, "insert into category values (0,'Dummy')");
	oci_execute($statement);
	$statement = oci_parse($connection, "insert into category values (1, 'Groceries')");
	oci_execute($statement);
	$statement = oci_parse($connection, "insert into category values (2, 'Restaurant')");
	oci_execute($statement);
	$statement = oci_parse($connection, "insert into category values (3, 'Clothing')");  	
	oci_execute($statement);

	//TRANSACTION
	$statement = oci_parse($connection, 'select 1 from transaction');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table transaction(
							trans_id int,
							cat_id int,
							type char(2),
							txn_desc varchar(64),
							tot_amt numeric(10,2),
							txn_date date,
							constraint transaction_pk primary key (trans_id, cat_id),
							constraint transaction_fk foreign key (cat_id) references category(cat_id))');
		if (!oci_execute($statement))
		{
			die("TRANSACTION table creation failed!");
		}
		echo "<br />TRANSACTION table created!<br />";
	}
	else
	{
		echo "<br />TRANSACTION table already present!<br />";
	}

	//USERGROUP
	$statement = oci_parse($connection, 'select 1 from usergroup');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table usergroup(
							group_id int,
							group_owner varchar(32),
							group_name varchar(64),
							constraint group_pk primary key (group_id),
							constraint group_fk foreign key (group_owner) references users(email_add))');
		if (!oci_execute($statement))
		{
			die("USERGROUP table creation failed!");
		}
		echo "<br />USERGROUP table created!<br />";
	}
	else
	{
		echo "<br />USERGROUP table already present!<br />";
	}

	//HAS_FRIENDS
	$statement = oci_parse($connection, 'select 1 from has_friends');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table has_friends(
							email_add varchar(32),
							friend_email_add varchar(32),
							dues numeric(10,2),
							constraint has_friends_pk primary key (email_add, friend_email_add),
							constraint has_friends_fk1 foreign key (friend_email_add) references users(email_add),
							constraint has_friends_fk2 foreign key (email_add) references users(email_add))');
		if (!oci_execute($statement))
		{
			die("HAS_FRIENDS table creation failed!");
		}
		echo "<br />HAS_FRIENDS table created!<br />";
	}
	else
	{
		echo "<br />HAS_FRIENDS table already present!<br />";
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
		echo "<br />BELONGS_TO table created!<br />";
	}
	else
	{
		echo "<br />BELONGS_TO table already present!<br />";
	}

	//PARTICIPATES
	$statement = oci_parse($connection, 'select 1 from participates');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table participates(
							email_add varchar(32),
							with_username varchar(32),
							trans_id int,
							cat_id int,
							with_amt numeric(10,2),
							constraint participates_pk primary key (email_add, with_username, trans_id, cat_id),
							constraint participates_fk1 foreign key (email_add) references users(email_add),
							constraint participates_fk2 foreign key (trans_id, cat_id) references transaction(trans_id, cat_id))');
		if (!oci_execute($statement))
		{
			die("PARTICIPATES table creation failed!");
		}
		echo "<br />PARTICIPATES table created!<br />";
	}
	else
	{
		echo "<br />PARTICIPATES table already present!<br />";
	}

	//SHARES
	$statement = oci_parse($connection, 'select 1 from shares');

	if (false == oci_execute($statement))
	{
		$statement = oci_parse($connection, 'create table shares(
							email_add varchar(32),
							trans_id int,
							cat_id int,
							shared_amt numeric(10,2),
							constraint shares_pk primary key (email_add, trans_id, cat_id),
							constraint shares_fk1 foreign key (email_add) references users(email_add),
							constraint shares_fk2 foreign key (trans_id, cat_id) references transaction(trans_id, cat_id))');
		if (!oci_execute($statement))
		{
			die("SHARES table creation failed!");
		}
		echo "<br />SHARES table created!<br />";
	}
	else
	{
		echo "<br />SHARES table already present!<br />";
	}


	oci_free_statement($statement);
	oci_close($connection);
?>
