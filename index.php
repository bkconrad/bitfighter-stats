<?php

require "db_functions.php";

date_default_timezone_set('America/Los_Angeles');

$days_filter = !empty($_REQUEST["days"]) ? htmlspecialchars($_REQUEST["days"]) : "";
$name_filter = !empty($_REQUEST["name"]) ? $_REQUEST["name"] : "";
$server_filter = !empty($_REQUEST["server"]) ? $_REQUEST["server"] : "";

$days = 0;
$server = "";
$name = "";

$main_query = "

select s.server_name, s.ip_address,
sg.stats_game_id, sg.game_type, sg.duration_seconds, sg.level_name, UNIX_TIMESTAMP(sg.insertion_date) as insertion_date, sg.is_team_game,
st.team_name, st.color_hex, st.team_score, st.result as team_result,
sp.player_name, sp.is_robot, sp.points, sp.kill_count, sp.death_count, sp.suicide_count, sp.is_authenticated, sp.result as player_result
from server s
join stats_game sg on s.server_id = sg.server_id
join stats_team st on sg.stats_game_id = st.stats_game_id
left join stats_player sp on st.stats_team_id = sp.stats_team_id

";

$standard_criteria = "

where sg.insertion_date between sysdate() - interval '%s' day and sysdate()

";

$name_criteria = "

inner join stats_player sp2 on sg.stats_game_id = sp2.stats_game_id
where sg.insertion_date between sysdate() - interval '%s' day and sysdate()
and sp2.insertion_date between sysdate() - interval '%s' day and sysdate()
and sp2.player_name = '%s'

";

$server_amendment = "

and s.server_name = '%s'

";


$order_amendment = "

order by sg.insertion_date desc, sg.level_name, st.team_name, sp.is_robot asc, sp.points desc

";

// Server query to populate server list

$server_query = "

select distinct s.server_name
from server s, stats_game sg
where s.server_id = sg.server_id
and sg.insertion_date between sysdate() - interval '%s' day and sysdate()
order by s.server_name

";

// a mysql connection is needed before calling this method
function filter_days() {
	global $days;
	
	global $days_filter;
	$days = sanitize($days_filter);
    
	if (empty($days)) {
		$days = 1;
	}
	
	if ($days > 30) { // no infinite stats for you..  yet
		$days = 30;
	}
}


function get_servers($connection) {
	global $server_query;
	global $days;

	$server_array = array();
	
	$query = sprintf($server_query, $days);
	$statement = mysql_query($query, $connection) or die('could not execute query: ' . $server_query);

	while ($row = mysql_fetch_array($statement)) {
		array_push($server_array, $row["server_name"]);
	}
	
	return $server_array;
}


function get_database_data($connection) {
	global $main_query;
	global $name_criteria;
	global $standard_criteria;
	global $order_amendment;
	global $server_amendment;

	global $name_filter;
	global $server_filter;
	
	global $days;
	global $server;
	global $name;
	
	$master_array = array();
    
	$last_stats_game_id = "";
	$last_team_name = "";
	$last_player_name = "";
	
	$stats_game_id_changed = false;
	$team_name_changed = false;
	$player_name_changed = false;
	
	filter_days();
	
	// set up parameters for statement
	$name = sanitize($name_filter);
	$server = sanitize($server_filter);
	
	$query = sprintf($main_query . $standard_criteria, $days);
	
	if (!empty($name_filter)) {
		$query = sprintf($main_query . $name_criteria, $days, $days, $name);
	}
	
	if (!empty($server_filter)) {
		$query = sprintf($query . $server_amendment, $server);
	}

	$query = $query . $order_amendment;
	
	$statement = mysql_query($query, $connection) or die('could not execute query: ' . $query);

	// build up data structure
    while ($row = mysql_fetch_array($statement)) {
		$server_name = $row["server_name"];
		$ip_address = $row["ip_address"];
		$stats_game_id = $row["stats_game_id"];
		$game_type = $row["game_type"];
		$duration_seconds = $row["duration_seconds"];
		$level_name = $row["level_name"];
		$is_team_game = $row["is_team_game"];
		$insertion_date = $row["insertion_date"];
		$team_name = $row["team_name"];
		$color_hex = $row["color_hex"];
		$team_score = $row["team_score"];
		$team_result = $row["team_result"];
		$player_name = $row["player_name"];
		$is_robot = $row["is_robot"];
		$is_authenticated = $row["is_authenticated"];
		$player_result = $row["player_result"];
		$points = $row["points"];
		$kill_count = $row["kill_count"];
		$death_count = $row["death_count"];
		$suicide_count = $row["suicide_count"];
		
		
		$current_game_array = array(
			"server_name" => $server_name,
			"ip_address" => $ip_address,
			"game_type" => $game_type,
			"duration_seconds" => $duration_seconds,
			"level_name" => $level_name,
			"is_team_game" => $is_team_game,
			"insertion_date" => $insertion_date,
			"team_array" => array()
		);
		
		$current_team_array = array(
			"team_name" => $team_name,
			"color_hex" => $color_hex,
			"team_score" => $team_score,
			"team_result" => $team_result,
			"player_array" => array()
		);
		
		$current_player_array = array(
			"player_name" => $player_name,
			"is_robot" => $is_robot,
			"points" => $points,
			"kill_count" => $kill_count,
			"death_count" => $death_count,
			"suicide_count" => $suicide_count,
			"is_authenticated" => $is_authenticated,
			"player_result" => $player_result
		);
		
		$stats_game_id_changed = false;
		$team_name_changed = false;
		$player_name_changed = false;
		
		if ($stats_game_id != $last_stats_game_id) {
			$stats_game_id_changed = true;
			$team_name_changed = true;
			$player_name_changed = true;
		}
		
		if ($last_team_name != $team_name) {
			$team_name_changed = true;
			$player_name_changed = true;
		}
		
		if ($last_player_name != $player_name) {
			$player_name_changed = true;
		}
		
		
		if ($stats_game_id_changed) {
			$master_array[$stats_game_id] = $current_game_array;
		}
		
		if ($team_name_changed) {
			$master_array[$stats_game_id]["team_array"][$team_name] = $current_team_array;
		}
		
		if ($player_name_changed) {
			$master_array["$stats_game_id"]["team_array"]["$team_name"]["player_array"]["$player_name"] = $current_player_array; 
		}
		
		$last_stats_game_id = $stats_game_id;
		$last_team_name = $team_name;
		$last_player_name = $player_name;
    }
	
	return $master_array;
}

function build_report_html($master_array, $server_array) {
	global $days;
	global $server;
	global $name;
	
	$day_selections = array(1,2,7,30);
	
	print("
		<html>
		<head>
			<title>Bitfighter Game Reports</title>
			<link rel=\"stylesheet\" href=\"game_summary.css\" type=\"text/css\" />
		</head>
		<body>
		<h1>Bitfighter Game Reports in the last $days day(s)</h1>
		<p><b>Note:</b> All times are given in America Pacific time zone</p>
		<p>Select game reports for a different time period:</p>
		<form id=\"daterange\" method=\"POST\">
			Time filter: <select name=\"days\">");
	foreach($day_selections as $cur_day) {
		print("<option value=\"$cur_day\"");
		if ("$cur_day" == "$days") {
			print("selected=\"selected\"");
		}
		print(">$cur_day day(s)</option>\n");
	}
	print("
			</select><br/>
			Server filter: <select name=\"server\">
			<option value=\"\">All Servers</option>
		");
	foreach($server_array as $cur_server) {
		print("<option value=\"$cur_server\"");
		if ("$cur_server" == "$server") {
			print("selected=\"selected\"");
		}
		print(">$cur_server</option>\n");
	}
	print("
			</select><br/>
			Name Filter: <input type=\"text\" name=\"name\" ");
	if (!empty($name)) {
		print("value=\"$name\" ");	
	}
	print("/><br/>
			<input type=\"submit\" />
		</form>
		
		");
	
	foreach($master_array as $game) {
		//print_r($game);
		$server_name = htmlspecialchars($game["server_name"]);
		$ip_address = $game["ip_address"];
		$game_type = $game["game_type"];
		$duration_seconds = $game["duration_seconds"];
		$level_name = htmlspecialchars($game["level_name"]);
		$is_team_game = $game["is_team_game"];
		$insertion_date = $game["insertion_date"];
		$team_array = $game["team_array"];
		
		$team_game = $is_team_game == 1 ? true : false;
		$date = strftime("%d %b %Y %I:%M:%S %p", $insertion_date);
		
		$duration_s = $duration_seconds % 60;
		$duration_m = ($duration_seconds - $duration_s) / 60;
		
		print("
			<div class=\"game\">
			<h2>Game ending on <span class=\"grey\">$date</span></h2>
			<table class=\"gameinfo\"><tbody>
			<tr><td><b>Server</b></td><td>$server_name</td></tr>
			<tr><td><b>IP Adress</b></td><td>$ip_address</td></tr>
			<tr><td><b>Type</b></td><td>$game_type</td></tr>
			<tr><td><b>Level Name</b></td><td>$level_name</td></tr>
			<tr><td><b>Duration</b></td><td>$duration_m"."m $duration_s"."s</td></tr>
			</tbody></table><br/>
		");
		
		if ($team_game) {
			print("<table><tbody><tr>");	
		}
		
		$team_increment = 0;
		
		foreach($team_array as $team) {
			$team_name = $team["team_name"];
			$color_hex = $team["color_hex"];
			$team_score = $team["team_score"];
			$team_result = $team["team_result"];
			$player_array = $team["player_array"];
			
			$team_increment++;
			
			if ($team_game) {
				if ($team_result == 'W')
					print("<td class=\"team winner\">");
				else
					print("<td class=\"team\">");
				
				print("<h3>");
				if ($team_result == 'W')
					print("WINNER: ");
				print("<span style=\"color: $color_hex;\">$team_name</span>&nbsp;&nbsp;<span class=\"points\">$team_score</span>");
				print("</h3>");
				print("<b>Players on this team:</b>");
			} 
			else {
				print("<br/><b>Players:</b>");
			}
			
			print("<table class=\"player\"><thead><tr>
				<td>Player name</td>
				<td>Points</td>
				<td>Kills</td>
				<td>Deaths</td>
				<td>Suicides</td>
			");
			if (!$team_game)
				print("<td>Result</td>");
			//print("<td>Shots</td>");
			print("</tr></thead>");
			print("<tbody>");
			
			$row_increment = 0;
			
			foreach($player_array as $player) {
        foreach($player as $k => $v) {
          $player[$k] = htmlspecialchars($v);
        }
				$player_name = $player["player_name"];
				$is_robot = $player["is_robot"];
				$points = $player["points"];
				$kill_count = $player["kill_count"];
				$death_count = $player["death_count"];
				$suicide_count = $player["suicide_count"];
				$is_authenticated = $player["is_authenticated"];
				$player_result = $player["player_result"];
				$shots_array = $player["shots_array"];
				
				$row_increment++;
				if ($row_increment % 2 == 0) {
					$row_class = "even";
				} else {
					$row_class = "odd";
				}
				
				print("<tr class=\"$row_class\">");
				if ($is_authenticated == 1)
					print("<td><u>$player_name</u>");
				else
					print("<td>$player_name");
				if ($is_robot == 1)
					print(" (robot)");
				print("</td>
					<td>$points</td>
					<td>$kill_count</td>
					<td>$death_count</td>
					<td>$suicide_count</td>
				");
				if (!$team_game)
					print("<td>$player_result</td>");
				
				/*
				print("<td><table class=\"shots\"><tbody><thead><tr>");
				print("<td>Weapon</td><td>Shots</td><td>Hits</td>");
				print("</tr></thead>");
				print("<tbody>");
			
				foreach($shots_array as $weapon_item) {
					$weapon = $weapon_item["weapon"];
					$shots = $weapon_item["shots"];
					$shots_struck = $weapon_item["shots_struck"];
					
					print("<tr><td>$weapon</td><td>$shots</td><td>$shots_struck</td></tr>");
				}
				
				print("</tbody></table>");
				
				print("</td>");
				*/
				print("</tr>");
			}
			
			print("</tbody></table>");
			
			if ($team_game) {
				print("</td>");
				
				if ($team_increment % 2 == 0) {
					print("</tr></tbody></table><br/><table><tbody><tr>");
				}
			}
			
		}
		
		if ($team_game)
			print("</tr></tbody></table>");	
		
		print("</div>");
	}
	
	print("</body></html>");
}


# Start script

if (!extension_loaded('mysql')) {
	print "You are missing the mysql php extension\n";
	exit;
}


$connection = connect_to_db();

$master_array = get_database_data($connection);
$server_array = get_servers($connection);

mysql_close($connection);

build_report_html($master_array, $server_array);

?>

