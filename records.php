<?php

require "db_functions.php";
require "lib.php";

date_default_timezone_set('America/Los_Angeles');

$key = date('Ymd').'records';
echo cache($key, 60 * 60 * 24, function() {

  $mysqli = connect_to_db();

  // Fields to select in queries and pretty column names for them
  $main_fields = array(
      "kill_death_ratio",
      "kill_count",
      "death_count",
      "suicide_count",
      "points",
      "win_count",
      "lose_count",
      "tie_count",
      "flag_drops",
      "flag_pickups",
      "flag_returns",
      "flag_scores",
      "asteroid_crashes",
      "teleport_uses",
      "turret_kills",
      "ff_kills",
      "asteroid_kills",
      "turrets_engineered",
      "ffs_engineered",
      "teleports_engineered",
      "distance_traveled",
      "switched_team_count",
      "phaser_shots",
      "bouncer_shots",
      "triple_shots",
      "burst_shots",
      "mine_shots",
      "spybug_shots",
      "phaser_shots_struck",
      "bouncer_shots_struck",
      "triple_shots_struck",
      "burst_shots_struck",
      "mine_shots_struck",
      "spybug_shots_struck"
  );

  $query_template = "
  SELECT
    player_name,
    %s AS value,
    last_played AS period
  FROM
    player_mv
  ORDER BY
    value DESC,
    period ASC
  LIMIT 3
  ;
  ";

  $data = array();
  foreach($main_fields as $field) {

    // construct and run the query
    $query = sprintf($query_template, $field);
    $result = mysqli_query($mysqli, $query);
    $result or die(mysqli_error($mysqli));

    // collect all of the results
    $group = array();
    while($row = mysqli_fetch_assoc($result)) {
      $group[] = $row;
    }

    // add the record group to the final data
    $data[$field] = $group;
  }

  return json_encode($data);
});
