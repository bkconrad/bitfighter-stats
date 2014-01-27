<?php

date_default_timezone_set('America/Denver');

require "db_functions.php";

$query = "

select tmp.day_date, AVG(tmp.ppg) as players_per_game
from (
    select sp.stats_game_id, date(sp.insertion_date) as day_date, count(sp.player_name) as ppg
    from stats_player sp
    where is_robot = 0
    group by sp.stats_game_id, day_date
) tmp
where tmp.day_date between sysdate() - interval 120 day and sysdate() - interval 1 day
group by tmp.day_date

";

$mysqli = connect_to_db();
$result = mysqli_query($mysqli, $query);
$result or die(mysqli_error($mysqli));

$data = array();
while($row = mysqli_fetch_assoc($result)) {
  array_push($data, $row);
}

echo json_encode($data);