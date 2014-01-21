<?php

date_default_timezone_set('America/Denver');

require "../db_functions.php";

require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');


$days_filter = !empty($_REQUEST["days"]) ? $_REQUEST["days"] : "";

$main_query = "

select tmp.day_date, group_concat(cast(tmp.ppg as char)) as players_per_game
from (
    select sp.stats_game_id, date(sp.insertion_date) as day_date, count(sp.player_name) as ppg
    from stats_player sp
    where is_robot = 0
    group by sp.stats_game_id, day_date
) tmp
where tmp.day_date between sysdate() - interval '%s' day and sysdate() - interval 1 day
group by tmp.day_date

";

function stats($array, $output = 'mean') {
	if(!is_array($array)){
		return FALSE;
	}else{
		switch($output){
			case 'mean':
				$count = count($array);
				$sum = array_sum($array);
				$total = $sum / $count;
			break;
			case 'median':
				rsort($array);
				$middle = round(count($array) / 2);
				$total = $array[$middle-1];
			break;
			case 'mode':
				$v = array_count_values($array);
				arsort($v);
				foreach($v as $k => $v){$total = $k; break;}
			break;
			case 'range':
				sort($array);
				$sml = $array[0];
				rsort($array);
				$lrg = $array[0];
				$total = $lrg - $sml;
			break;
			case 'max':
				$total = max($array);
			break;
		}
		return $total;
	}
}


function get_database_data() {
	global $main_query;
	global $days_filter;
	
	$master_array = array();
	
	$date_array = array();
	$player_per_game_array = array();
	
	array_push($master_array, $date_array);
	array_push($master_array, $player_per_game_array);
	
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
		$players_per_game = $row["players_per_game"];
		
		array_push($master_array[0], $day_date);
		array_push($master_array[1], $players_per_game);
    }
    
    mysql_close($connection);
    
    return $master_array;
}

function build_averages($master_array) {

	$averages_array = array();

	$mean_array = array();
	$median_array = array();
	$mode_array = array();
	$range_array = array();
	$max_array = array();


	for ($i = 0; $i < count($master_array[0]); $i++) {
		$mean_array[$i] = stats(explode(",", $master_array[1][$i]), 'mean');
		$median_array[$i] = stats(explode(",", $master_array[1][$i]), 'median');
		$mode_array[$i] = stats(explode(",", $master_array[1][$i]), 'mode');
		$range_array[$i] = stats(explode(",", $master_array[1][$i]), 'range');
		$max_array[$i] = stats(explode(",", $master_array[1][$i]), 'max');
	}

	array_push($averages_array, $master_array[0]);  // dates_array
	array_push($averages_array, $mean_array);
	array_push($averages_array, $median_array);
	array_push($averages_array, $mode_array);
	array_push($averages_array, $range_array);
	array_push($averages_array, $max_array);

	return $averages_array;
}


function build_graph($averages_array) {
	global $days;

	$labels = $averages_array[0];
	
	$datay1 = $averages_array[1];
	$datay2 = $averages_array[2];
	$datay3 = $averages_array[3];
	$datay4 = $averages_array[4];
	$datay5 = $averages_array[5];

	// Setup the graph
	$graph = new Graph(800,400);
	$graph->SetScale("textlin");

	$theme_class=new UniversalTheme;

	$graph->SetTheme($theme_class);
	$graph->img->SetAntiAliasing(false);
	$graph->title->Set('Bitfighter Usage Graph 2 - Players per game');
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
	$p1->SetLegend('Mean');

	// Create the second line
//	$p2 = new LinePlot($datay2);
//	$graph->Add($p2);
//	$p2->SetColor("#FF0000");
//	$p2->SetLegend('Median');

	// Create the third line
//	$p3 = new LinePlot($datay3);
//	$graph->Add($p3);
//	$p3->SetColor("#FF1493");
//	$p3->SetLegend('Mode');

	// Create the fourth line
//	$p4 = new LinePlot($datay4);
//	$graph->Add($p4);
//	$p4->SetColor("#005400");
//	$p4->SetLegend('Range');

	// Create the fourth line
	$p5 = new LinePlot($datay5);
	$graph->Add($p5);
	$p5->SetColor("#B22222");
	$p5->SetLegend('Max');
	
	$graph->legend->Pos(0.5,0.06,'center','top');
	$graph->legend->SetColumns(4);
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

$averages_array = build_averages($master_array);

build_graph($averages_array);

?>

