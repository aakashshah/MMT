#!/usr/local/bin/php
<?php
        if (!require("connection.php"))
                                {
                                        // connection failure return error code 1
                                        echo "Connection lost";
					exit(1);
                                }
	$groupid=$_GET['groupName'];
	$query3 = "select email_add from belongs_to where group_id in (select group_id from usergroup where GROUP_NAME = '".$groupid."')";
       $statement3 = oci_parse($connection, $query3);
       if (!oci_execute($statement3))
       {
                echo $groupid;
       }
       $response="";
	$i=0;		
       while(1)
       {
               $row = oci_fetch_object($statement3);
               if (!$row)
               {
                      break;
               }
       		$response=$response.$row->EMAIL_ADD.",";
		$i++;
 	}
	$response=$i.",".$response;
	echo $response;
?>
