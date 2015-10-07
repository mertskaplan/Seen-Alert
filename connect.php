<?php

	$databasename = "databasename";
	$db_username = "username";
	$db_password = "password";
	$db_server = "localhost"; // default: "localhost"

	@$connect = mysql_connect($db_server,$db_username,$db_password) or die("Error : " . mysql_error());
	@mysql_select_db($databasename,$connect) or die("Error : " . mysql_error());
	mysql_query("SET NAMES ‘utf8′");
	mysql_query("SET CHARACTER SET utf8");
	mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");  
	
	ob_start();
?>