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

        $connection = mysqli_connect($server, $username, $password, $database) or die("Could not connect: \n" . mysqli_error());
        return $connection;
}

function sanitize($mysqli, $str) {
	// apply stripslashes if magic_quotes_gpc is enabled
	if(get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	
	// a mysql connection is required before using this function
	$str = mysqli_real_escape_string($mysqli, $str);
	
	return $str;
}
