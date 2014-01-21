#!/usr/bin/php5

<?php

date_default_timezone_set('America/Denver');

$days_filter = !empty($_REQUEST["days"]) ? $_REQUEST["days"] : "";

$days = $days_filter;

if (empty($days)) {
	$days = 7;
}

if ($days > 120) { // no infinite stats for you..  yet
	$days = 120;
}



# Start script

if (!extension_loaded('mysql')) {
	print "You are missing the mysql php extension\n";
	exit;
}

if (!extension_loaded('gd')) {
	print "You are missing the php gd extension\n";
	exit;
}

print("
	<html>
	<head>
		<title>Bitfighter Game Reports</title>
		<link rel=\"stylesheet\" href=\"game_summary.css\" type=\"text/css\" />
	</head>
	<body>
	<h1>Bitfighter Usage Graphs for the last $days days</h1>
	<p>Select time period:</p>
	<form id=\"daterange\" method=\"POST\">
		Time filter: <select name=\"days\">
			<option value=\"7\">7 days</option>
			<option value=\"30\">30 days</option>
			<option value=\"60\">60 days</option>
			<option value=\"90\">90 days</option>
			<option value=\"120\">120 days</option>
		</select><br/>
		<input type=\"submit\" />
	</form>
	
	<img src=\"graph1.php?days=$days\" alt=\"\" />
	<br/><br/>
	<img src=\"graph2.php?days=$days\" alt=\"\" />
	");

print("</body></html>");
?>

