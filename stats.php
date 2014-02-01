<?php
date_default_timezone_set('America/Los_Angeles');

require "db_functions.php";
require "lib.php";

check_params(array(
  'alltime'  => '',
  'year'     => date('Y'),
  'month'    => date('m')
));

// cache archived stats forever
$ttl = 0;

if($month == date('n')) {
  // cache the current month's stats for ten minutes
  $ttl = 10 * 60;
}

echo cache("stats$year$month", $ttl, function() {

  global $alltime, $year, $month;

  $mysqli = connect_to_db();

  get_params($mysqli, array(
    'alltime'  => '',
    'year'     => date('Y'),
    'month'    => date('m')
  ));

  // build filter
  $filters = array();
  if ($alltime != 'yes') {
    $filters[] = "time_period='$year-$month-01'";
  }
  if (!empty($filters)) {
    $filter = "WHERE " . implode(" AND ", $filters);
  }

  // Construct query
  if ($alltime == 'yes') {

    $stats_query = "
      SELECT player_name
        , SUM(kill_count) AS kill_count
        , SUM(death_count) AS death_count
        , SUM(kill_count) / SUM(death_count) AS kill_death_ratio
        , SUM(suicide_count) AS suicide_count
        , SUM(points) AS points
        , SUM(win_count) AS win_count
        , SUM(lose_count) AS lose_count
        , SUM(tie_count) AS tie_count
        , SUM(win_count) + SUM(lose_count) + SUM(tie_count) AS game_count
        , SUM(flag_drops) AS flag_drops
        , SUM(flag_pickups) AS flag_pickups
        , SUM(flag_returns) AS flag_returns
        , SUM(flag_scores) AS flag_scores
        , SUM(asteroid_crashes) AS asteroid_crashes
        , SUM(teleport_uses) AS teleport_uses
        , SUM(switched_team_count) AS switched_team_count
        , MAX(last_played) AS last_played
        , is_authenticated
      FROM player_mv
      $filter
      GROUP BY player_name, is_authenticated
      ;
    ";

    // huge and slow, I'd rather disable page counting for all-time views
    $count_query = "
      SELECT
        COUNT(counts.subcount) AS count
      FROM (
        SELECT COUNT(*) AS subcount
        FROM player_mv
        $filter
        GROUP BY player_name, is_authenticated
      ) AS counts
      ;
    ";

  } else {

    $stats_query = "
      SELECT player_name
        , kill_count
        , death_count
        , kill_death_ratio
        , suicide_count
        , points
        , win_count
        , lose_count
        , tie_count
        , win_count + lose_count + tie_count AS game_count
        , flag_drops
        , flag_pickups
        , flag_returns
        , flag_scores
        , asteroid_crashes
        , teleport_uses
        , switched_team_count
        , last_played
        , is_authenticated
        , turret_kills
        , ff_kills
        , asteroid_kills
        , turrets_engineered
        , ffs_engineered
        , teleports_engineered
        , distance_traveled
        , switched_team_count
      FROM player_mv
      $filter
      ;
    ";

    $count_query = "
      SELECT
        COUNT(*) AS count
      FROM player_mv
      $filter
      ;
    ";
  }

  // count number of results
  $result = mysqli_query($mysqli, $count_query);
  $result or die(mysqli_error($mysqli));
  $row = mysqli_fetch_assoc($result);
  $count = intval($row['count']);

  // echo $stats_query;

  // run the actual player data query
  $result = mysqli_query($mysqli, $stats_query);
  $result or die(mysqli_error($mysqli));

  $all = array();

  while($row = mysqli_fetch_assoc($result)) {
    array_push($all, $row);
  }

  return json_encode($all);
});
