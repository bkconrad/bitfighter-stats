<?php

date_default_timezone_set('America/Denver');

require "../db_functions.php";

require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');


$days_filter = !empty($_REQUEST["days"]) ? $_REQUEST["days"] : "";

$main_query = "

select tmp1.day_date, tmp1.game_count, tmp2.unique_player_count
from (
    select date(insertion_date) as day_date, count(stats_game_id) as game_count
    from stats_game
    group by day_date
) tmp1,
(
    select date(insertion_date) as day_date, count(distinct player_name) as unique_player_count
    from stats_player
    where is_robot = 0
    group by day_date
) tmp2
where tmp1.day_date = tmp2.day_date
and tmp1.day_date between sysdate() - interval '%s' day and sysdate() - interval 1 day

";

function get_database_data() {
	global $main_query;
	global $days_filter;
	
	$master_array = array();
	$date_array = array();
	$game_count_array = array();
	$unique_player_name_array = array();
	
	array_push($master_array, $date_array);
	array_push($master_array, $game_count_array);
	array_push($master_array, $unique_player_name_array);
	
	$connection = connect_to_db();
	
	// set up parameters for statement
	$days = sanitize($days_filter);
    
	if (empty($days)) {
		$days = 7;
	}
	
	if ($days > 120) { // no infinite stats for you..  yet
		$days = 120;
	}
	
	$days = $days + 1;  // adjust do not include current day
	
    $query = sprintf($main_query, $days);
    
    $statement = mysql_query($query, $connection) or die('could not execute query: ' . $query);

	// build up data structure
    while ($row = mysql_fetch_array($statement)) {
		$day_date = $row["day_date"];
		$game_count = $row["game_count"];
		$unique_player_count = $row["unique_player_count"];
		
		array_push($master_array[0], $day_date);
		array_push($master_array[1], $game_count);
		array_push($master_array[2], $unique_player_count);
    }
    
    mysql_close($connection);
    
    return $master_array;
}


function build_graph($master_array) {
	global $days;

	$labels = $master_array[0];
	$datay1 = $master_array[1];
	$datay2 = $master_array[2];

	// Setup the graph
	$graph = new Graph(800,400);
	$graph->SetScale("textlin");

	$theme_class=new UniversalTheme;

	$graph->SetTheme($theme_class);
	$graph->img->SetAntiAliasing(false);
	$graph->title->Set('Bitfighter Usage Graph');
	$graph->SetBox(false);

	$graph->img->SetAntiAliasing();

	$graph->yaxis->HideZeroLabel();
	$graph->yaxis->HideLine(false);
	$graph->yaxis->HideTicks(false,false);

	$graph->xgrid->Show();
	$graph->xgrid->SetLineStyle("solid");
	$graph->xaxis->SetLabelAngle(90);
	$graph->xaxis->SetTickLabels($labels);
	$graph->xgrid->SetColor('#E3E3E3');

	// Create the first line
	$p1 = new LinePlot($datay1);
	$graph->Add($p1);
	$p1->SetColor("#6495ED");
	$p1->SetLegend('Game Count');

	// Create the second line
	$p2 = new LinePlot($datay2);
	$graph->Add($p2);
	$p2->SetColor("#B22222");
	$p2->SetLegend('Unique Player Count');

	$graph->legend->Pos(0.5,0.06,'center','top');
	// Output line
	$graph->Stroke();
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

$master_array = get_database_data();

build_graph($master_array);

?>

