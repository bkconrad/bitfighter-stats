<?php
date_default_timezone_set('America/Los_Angeles');

$BADGE_MAP = array(
  0 => array("Developer", "developer.png"), 
  2 => array("Twenty five flags", "twenty_five_flags.png"), 
  3 => array("BBB Gold Medalist", "bbb_gold.png"), 
  4 => array("BBB Silve Medalist", "bbb_silver.png"), 
  5 => array("BBB Bronze Medalist", "bbb_bronze.png"), 
  6 => array("BBB Participant", "bbb_participation.png"), 
  7 => array("Level Design Contest Winner", "level_design_winner.png"), 
  8 => array("Zone Controller", "zone_controller.png")
);

function show_badge($arr) {
  if (!isset($arr[0])) {
    return;
  }
  print "<span class=\"badge\"><img title=\"" . $arr[0] . "\" src=\"badges/" . $arr[1] . "\"></img></span>\n";
}

function show_stat ($text) {
  print "<span class=\"stat\">$text</span><br/>\n";
}

function make_link($text, $arr) {
  if (!is_array($arr)) {
    $arr = array();
  }
  global $player;
  global $year;
  global $month;
  global $alltime;
  global $authed;

  $base = explode("?", $_SERVER["REQUEST_URI"]);
  $result = '';

  $arr['player'] = !empty($arr['player']) ? $arr['player'] : $player;
  $arr['year'] = !empty($arr['year']) ? $arr['year'] : $year;
  $arr['month'] = !empty($arr['month']) ? $arr['month'] : $month;
  $arr['alltime'] = !empty($arr['alltime']) ? $arr['alltime'] : $alltime;
  $arr['authed'] = !empty($arr['authed']) ? $arr['authed'] : $authed;

  if (!empty($arr['player'])) {
    $result .= 'player=' . urlencode(htmlspecialchars_decode($arr['player'])) . '&';
  }

  if (!empty($arr['authed'])) {
    $result .= 'authed=' . $arr['authed'] . '&';
  }

  // only add month and year if alltime was not specified
  if ($arr['alltime'] != 'yes') {
    if (!empty($arr['year'])) {
      $result .= 'year=' . $arr['year'] . '&';
    }
    if (!empty($arr['month'])) {
      $result .= 'month=' . $arr['month'];
    }
  } else {
    $result .= 'alltime=' . $arr['alltime'];
  }

  print("<a href=\"" . $base[0] . "?" . $result . "\">$text</a>");
}

$start_time = microtime();

require "db_functions.php";
require "lib.php";

date_default_timezone_set('America/Los_Angeles');

function time_elapsed ($time_in_seconds) {
  $ret = array();
  $secs = $time_in_seconds;
  $bit = array(
    'y'    => round($secs / 31556926),
    'mo'    => $secs / 2592000 % 12,
    'w'    => $secs / 604800 % 52,
    'd'    => $secs / 86400 % 7,
    'h'    => $secs / 3600 % 24,
    'm'    => $secs / 60 % 60,
    's'    => $secs % 60
  );

  foreach($bit as $unit => $value){
    if ($value >= 1)
      $ret[] = $value . $unit;
  }

  return join(' ', $ret);
}

connect_to_db();

get_params(array(
  'player' => '',
  'year' => date('Y'),
  'month' => date('m'),
  'alltime' => '',
  'authed' => ''
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
    $filters[] = "player_name='" . sanitize(html_entity_decode($player)) . "'";
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
    $filters[] = "player_name='" . sanitize(html_entity_decode($player)) . "'";
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

$result = mysql_query($stats_query);
$result or die(mysql_error());
$playerdata = mysql_fetch_assoc($result);

// get achievement data
if($playerdata['is_authenticated']) {
  $achievement_query = "
    SELECT player_name, achievement_id
    FROM player_achievements
    WHERE player_name = '" . $playerdata['player_name'] . "'
  ";
  $achievement_result = mysql_query($achievement_query);
  $achievement_result or die(mysql_error());
}

$last_modified = strtotime($playerdata['last_update']);
handle_cache_headers($last_modified);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Bitfighter Player Stats</title>
<link rel='stylesheet' href='player_stats.css'></link>
</head>
<body>
<?php

$games = $playerdata['win_count'] +
         $playerdata['tie_count'] +
         $playerdata['lose_count'] +
         $playerdata['dnf_count'];

$finished_games = $playerdata['win_count'] +
                  $playerdata['tie_count'] +
                  $playerdata['lose_count'];

?>


<div class="player">
  <span>&#9664; <a href="stats.php">Back to all players</a><br /></span>
  <form id='player-search' action="player.php" method="get">
  <div>
  <span>Search by player name:</span><br/><input type="text" name="player" value="<?php print $player; ?>"></input>
  <input type="submit"></input>
  </div>
  </form>
  <div id="month-select">

<?php

// convert to months since 0 AD to iterate through months
$current_months_total = date('Y') * 12 + date('m');
if ($alltime == 'yes') {
  print '<span>All Time</span>';
} else {
  make_link("All Time", array("alltime" => "yes"));
}
for ($i = 5; $i >= 0; $i--) {
  $link_months_total = $current_months_total - $i;
  $link_month = $link_months_total % 12;
  $link_year = ($link_months_total - $link_month) / 12;
  $link_month_name = date("F", mktime(0, 0, 0, $link_month, 1));

  if ($link_month == $month && $link_year == $year && $alltime != 'yes') {
    print '<span>' . $link_month_name . ' ' . $link_year . ' </span>';
  } else {
    make_link($link_month_name . " " . $link_year . " ", array("month" => $link_month, "year" => $link_year, "alltime" => 'no', 'page' => '1'));
  }
}

?>
  </div>
<?php

if (!empty($playerdata['player_name'])):
?>
  <h1 class="<?php print ($playerdata['is_authenticated'] == "1" ? 'auth' : '') ?>">
  <?php print $playerdata['player_name']; ?>
  </h1>
<?php
  while($achievement = mysql_fetch_assoc($achievement_result)) {
    show_badge($BADGE_MAP[$achievement['achievement_id']]);
  }
?>
  <div id='toggle-authenticated'>
<?php
if ($playerdata['is_authenticated'] == "1") {
  make_link("(show only unauthenticated data)", array("authed" => "no"));
} else {
  make_link("(show only authenticated data)", array("authed" => "yes"));
}
?>
  </div>
  <div id="frags">
    <h2>Frags</h2>
    Kills: <?php show_stat($playerdata['kill_count']); ?>
    Deaths: <?php show_stat($playerdata['death_count']); ?>
    Suicides: <?php show_stat($playerdata['suicide_count']); ?>
    KDR: <?php show_stat(round($playerdata['kill_death_ratio'], 2)); ?>
    Spread: <?php show_stat($playerdata['kill_count'] - $playerdata['death_count']); ?>
    Spread/game: <?php show_stat(round(($playerdata['kill_count'] - $playerdata['death_count']) / $games, 2)); ?>
  </div>
  <div id="flags">
    <h2>Flags</h2>
    Taken: <?php show_stat($playerdata['flag_pickups']); ?>
    Dropped: <?php show_stat($playerdata['flag_drops']); ?>
    Returned: <?php show_stat($playerdata['flag_returns']); ?>
    Scored: <?php show_stat($playerdata['flag_scores']); ?>
    Scored/game: <?php show_stat(round($playerdata['flag_scores'] / $games, 2)); ?>
    Scored %: <?php show_stat(round($playerdata['flag_scores'] / $playerdata['flag_pickups'] * 100, 2)); ?>
  </div>
  <div id="games">
    <h2>Games</h2>
    Played: <?php show_stat($games); ?>
    Wins: <?php show_stat($playerdata['win_count'] . '(' . round($playerdata['win_count'] / $finished_games * 100, 2) . '%)'); ?>
    Losses: <?php show_stat($playerdata['lose_count'] . '(' . round($playerdata['lose_count'] / $finished_games * 100, 2) . '%)'); ?>
    Ties: <?php show_stat($playerdata['tie_count'] . '(' . round($playerdata['tie_count'] / $finished_games * 100, 2) . '%)'); ?>
    Points: <?php show_stat($playerdata['points']); ?>
    Points/game: <?php show_stat(round($playerdata['points'] / $games, 2)); ?>
  </div>
  <div id="misc">
    <h2>Misc</h2>
    Asteroids Destroyed: <?php show_stat($playerdata['asteroid_kills']); ?>
    Force Fields Destroyed: <?php show_stat($playerdata['ff_kills']); ?>
    Turrets Destroyed: <?php show_stat($playerdata['turret_kills']); ?>
    Force Fields Engineered: <?php show_stat($playerdata['ffs_engineered']); ?>
    Turrets Engineered: <?php show_stat($playerdata['turrets_engineered']); ?>
    Teleporters Engineered: <?php show_stat($playerdata['teleports_engineered']); ?>
    Distance Traveled: <?php show_stat($playerdata['distance_traveled']); ?>
    Asteroid Crashes: <?php show_stat($playerdata['asteroid_crashes']); ?>
    Teleportations: <?php show_stat($playerdata['teleport_uses']); ?>
    Team Switches: <?php show_stat($playerdata['switched_team_count']); ?>
    Last Seen: <?php show_stat(time_ago($playerdata['last_played'])); ?>
  </div>
  <div id="weapons">
    <h2>Weapons</h2>
    <table>
      <tr class='table-head'>
      <td>Weapon</td><td>Shots Fired</td><td>Shots Struck</td><td>Accuracy</td><td>Firing Time</td>
      </tr>

<?php
$weapons = array(
  "phaser" => 100,
  "bouncer" => 100,
  "triple" => 200,
  "burst" => 700,
  "mine" => 900,
  "spybug" => 800
);

foreach ($weapons as $w => $delay) {
  $shots = $playerdata[$w . "_shots"];
  $shots_struck = $playerdata[$w . "_shots_struck"];
  print "<tr>\n";
  print "\t<td>" . str_replace("_", " ", $w) . "</td>\n";
  print "\t<td>$shots</td>\n";
  print "\t<td>$shots_struck</td>\n";
  if ($shots > 0) {
    print "\t<td>" . round($shots_struck / $shots * 100, 2) . "%</td>\n";
  } else {
    print "\t<td>N/A%</td>\n";
  }
  print "\t<td>" . time_elapsed(round($shots * $delay / 1000)) . "</td>\n";
  print "</tr>\n";
}
?>
    </table>
  </div>
<?php
else:
  // explain what we couldn't find, and offer an alternative
  $spanclass = ($authed == 'yes' ? 'auth' : '');
  $search_period = ($alltime == 'yes' ? "for all of time" : "during " . date("F \of Y", mktime(0, 0, $year, $month, 1)));
  $search_alternative = ($authed == 'yes' ? "unauthenticated data" : "authenticated data");
  print "<p>No data for player '<span class='$spanclass'>$player</span>' found $search_period.</p>";
  make_link("Try searching for $search_alternative", array('authed' => ($authed == 'yes' ? 'no' : 'yes')));
endif;
?>
</div>

<?php
printf("<!-- Generated in %s seconds on %s -->", round(microtime() - $start_time, 6), date('H:i:s'));
?>

</body>
</html>
