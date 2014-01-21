<?php

$username = "root";
$password = "root";
$server = "localhost";
$database = "bf_stats";

function connect_to_db() {
        global $username;
        global $password;
        global $server;
        global $database;

        $connection  = mysql_pconnect($server, $username, $password) or die("Could not connect: \n" . mysql_error());
        mysql_select_db($database, $connection) or die("Cannot select db $dbname: \n" . mysql_error());
        return $connection;
}

function sanitize($data) {
	// apply stripslashes if magic_quotes_gpc is enabled
	if(get_magic_quotes_gpc()) {
		$data = stripslashes($data);
	}
	
	// a mysql connection is required before using this function
	$data = mysql_real_escape_string($data);
	
	return $data;
}
?>
