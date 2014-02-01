<?php

date_default_timezone_set('America/Denver');

require "db_functions.php";
require "lib.php";

header('Content-Type: application/json');
echo cache('players_per_game', 60 * 60 * 24, function() {
	$query = "

	select tmp.day_date, AVG(tmp.ppg) as players_per_game, tmp2.unique_player_count as unique_player_count
	from (
	    select sp.stats_game_id, date(sp.insertion_date) as day_date, count(sp.player_name) as ppg
	    from stats_player sp
	    where is_robot = 0
	    group by sp.stats_game_id, day_date
	) tmp,
	(
	    select date(insertion_date) as day_date, count(distinct player_name) as unique_player_count
	    from stats_player
	    where is_robot = 0
	    group by day_date
	) tmp2
	where tmp.day_date = tmp2.day_date AND tmp.day_date between sysdate() - interval 120 day and sysdate() - interval 1 day
	group by tmp.day_date

	";

	$mysqli = connect_to_db();
	$result = mysqli_query($mysqli, $query);
	$result or die(mysqli_error($mysqli));

	$data = array();
	while($row = mysqli_fetch_assoc($result)) {
	  array_push($data, $row);
	}

	return json_encode($data);
});
