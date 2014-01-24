<?php

require "db_functions.php";
require "lib.php";

date_default_timezone_set('America/Los_Angeles');

$mysqli = connect_to_db();
get_params($mysqli, array(
  'player' => '',
  'year' => date('Y'),
  'month' => date('m'),
  'alltime' => '',
  'authed' => 'yes'
));

if (empty($player)) {
  die("Please specify a player");
}

// Fields to select in queries and pretty column names for them
$main_fields = array(
    "player_name",
    "kill_death_ratio",
    "kill_count",
    "death_count",
    "suicide_count",
    "points",
    "win_count",
    "lose_count",
    "tie_count",
    "dnf_count",
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
    "last_played",
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

// Construct query from field map and passed parameters
if ($alltime == 'yes') {
  $filters = array();
  if (!empty($player)) {
    $filters[] = "player_name='" . sanitize($mysqli, html_entity_decode($player)) . "'";
  }
  if (!empty($authed)) {
    $filters[] = "is_authenticated=" . ($authed == 'yes' ? '1' : '0');
  }
  if (!empty($filters)) {
    $filter = "WHERE " . implode(" AND ", $filters);
  }
  $stats_query = "
    SELECT player_name
      , SUM(kill_count) / SUM(death_count) AS kill_death_ratio
      , SUM(kill_count) AS kill_count
      , SUM(death_count) AS death_count
      , SUM(suicide_count) AS suicide_count
      , SUM(points) AS points
      , SUM(win_count) AS win_count
      , SUM(lose_count) AS lose_count
      , SUM(tie_count) AS tie_count
      , SUM(flag_drops) AS flag_drops
      , SUM(flag_pickups) AS flag_pickups
      , SUM(flag_returns) AS flag_returns
      , SUM(flag_scores) AS flag_scores
      , SUM(asteroid_crashes) AS asteroid_crashes
      , SUM(teleport_uses) AS teleport_uses
      , SUM(switched_team_count) AS switched_team_count
      , SUM(turret_kills) AS turret_kills
      , SUM(ff_kills) AS ff_kills
      , SUM(asteroid_kills) AS asteroid_kills
      , SUM(turrets_engineered) AS turrets_engineered
      , SUM(ffs_engineered) AS ffs_engineered
      , SUM(teleports_engineered) AS teleports_engineered
      , SUM(distance_traveled) AS distance_traveled
      , MAX(last_played) AS last_played
      , SUM(phaser_shots) AS phaser_shots
      , SUM(bouncer_shots) AS bouncer_shots
      , SUM(triple_shots) AS triple_shots
      , SUM(burst_shots) AS burst_shots
      , SUM(mine_shots) AS mine_shots
      , SUM(spybug_shots) AS spybug_shots
      , SUM(phaser_shots_struck) AS phaser_shots_struck
      , SUM(bouncer_shots_struck) AS bouncer_shots_struck
      , SUM(triple_shots_struck) AS triple_shots_struck
      , SUM(burst_shots_struck) AS burst_shots_struck
      , SUM(mine_shots_struck) AS mine_shots_struck
      , SUM(spybug_shots_struck) AS spybug_shots_struck
      , MAX(last_played) AS last_update
      , is_authenticated
    FROM player_mv
    $filter
    GROUP BY player_name, is_authenticated
    ORDER BY is_authenticated DESC
    ;
  ";
} else {
  $filters = array();
  if (!empty($player)) {
    $filters[] = "player_name='" . sanitize($mysqli, html_entity_decode($player)) . "'";
  }
  if (!empty($authed)) {
    $filters[] = "is_authenticated=" . ($authed == 'yes' ? '1' : '0');
  }
  $filters[] = "time_period='$year-$month-01'";

  $filter = "WHERE " . implode(" AND ", $filters);
  $stats_query = "
    SELECT " . implode(", ",$main_fields) . "
         , last_played AS last_update
         , is_authenticated
    FROM player_mv
    $filter
    ORDER BY is_authenticated DESC
    LIMIT 1
    ;
  ";
}

$result = mysqli_query($mysqli, $stats_query);
$result or die(mysqli_error($mysqli));
$data = mysqli_fetch_assoc($result);

// get achievement data
$achievements = array();
if($data['is_authenticated']) {
  $achievement_query = "
    SELECT player_name, achievement_id
    FROM player_achievements
    WHERE player_name = '" . $data['player_name'] . "'
  ";
  $achievement_result = mysqli_query($mysqli, $achievement_query);
  $achievement_result or die(mysqli_error($mysqli));

  while($row = mysqli_fetch_assoc($achievement_result)) {
    $achievements[] = $row;
  }
}

// $last_modified = strtotime($data['last_update']);
// handle_cache_headers($last_modified);

$weapons = array(
  "phaser" => 100,
  "bouncer" => 100,
  "triple" => 200,
  "burst" => 700,
  "mine" => 900,
  "spybug" => 800
);

$data['found'] = !empty($data);
$data['achievements'] = $achievements;

echo json_encode($data);