#!/usr/local/bin/php

<?php
	phpinfo();
	$to = "aakashv2.1@gmail.com";
	$subject = "Test mail";
	$message = "Hello! This is a simple email message.";
	$from = "someonelse@example.com";
	$headers = "From:" . $from;
	mail($to,$subject,$message,$headers);
	echo "Mail Sent.";
?>
