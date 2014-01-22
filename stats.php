<?php
date_default_timezone_set('America/Los_Angeles');

$start_time = microtime(true);

require "db_functions.php";
require "lib.php";

/**
 * Create a link to this page with the current parameters, except where they are
 * overriden in $arr which is an associative array with parameter names and values
 */
function make_link($text, $arr) {
  if (!is_array($arr)) {
    $arr = array();
  }
  global $player;
  global $sort;
  global $order;
  global $page;
  global $year;
  global $month;
  global $alltime;
  global $authed;

  $base = explode("?", $_SERVER["REQUEST_URI"]);
  $result = '';

  $arr['player'] = urlencode(html_entity_decode(!empty($arr['player']) ? $arr['player'] : $player));
  $arr['sort'] = !empty($arr['sort']) ? $arr['sort'] : $sort;
  $arr['order'] = !empty($arr['order']) ? $arr['order'] : $order;
  $arr['page'] = !empty($arr['page']) ? $arr['page'] : $page;
  $arr['year'] = !empty($arr['year']) ? $arr['year'] : $year;
  $arr['month'] = !empty($arr['month']) ? $arr['month'] : $month;
  $arr['alltime'] = !empty($arr['alltime']) ? $arr['alltime'] : $alltime;
  $arr['authed'] = !empty($arr['authed']) ? $arr['authed'] : $authed;

  if ($arr['player'] != '') {
    $result .= 'player=' . $arr['player'] . '&';
  }
  if ($arr['order'] != '') {
    $result .= 'order=' . $arr['order'] . '&';
  }
  if ($arr['sort'] != '') {
    $result .= 'sort=' . $arr['sort'] . '&';
  }
  if ($arr['page'] != '') {
    $result .= 'page=' . $arr['page'] . '&';
  }
  if ($arr['authed'] != '') {
    $result .= 'authed=' . $arr['authed'] . '&';
  }

  // only add month and year if alltime was not specified
  if ($arr['alltime'] != 'yes') {
    if ($arr['year'] != '') {
      $result .= 'year=' . $arr['year'] . '&';
    }
    if ($arr['month'] != '') {
      $result .= 'month=' . $arr['month'];
    }
  } else {
    $result .= 'alltime=' . $arr['alltime'];
  }
  print("<a href=\"" . $base[0] . "?" . htmlspecialchars($result) . "\">$text</a>");
}

/**
 * Print a page selection div
 */
function make_pager () {
  global $page;
  global $pages;
  printf("<div class=\"pager\">Page $page of $pages<br/>");

  if ($page != 1) {
    make_link('first', array("page" => 1));
  } else {
    print 'first';
  }
  print("&nbsp;");

  if ($page > 1) {
    make_link('prev', array("page" => $page - 1));
  } else {
    print 'prev';
  }
  print("&nbsp;");

  if ($page < $pages) {
    make_link('next', array("page" => $page + 1));
  } else {
    print 'next';
  }
  print("&nbsp;");

  if ($page != $pages) {
    make_link('last', array("page" => $pages));
  } else {
    print 'last';
  }
  print("</div>");
}

$mysqli = connect_to_db();
get_params(array(
  'player'  => '',
  'alltime' => '',
  'authed'  => '',
  'order'   => 'kill_death_ratio',
  'sort'    => 'DESC',
  'page'    => '1',
  'year'    => date('Y'),
  'month'   => date('m')
));

$per_page = 40;
$page = max($page, 1);
$start = ($page - 1) * $per_page;

// Fields to display on pages and pretty column names for them
$displayed_fields = array(
    'player_name' => 'Player',
    'kill_death_ratio' => 'KDR',
    'kill_count' => 'Kills',
    'death_count' => 'Deaths',
    'suicide_count' => 'Suicides',
    'points' => 'Points',
    'win_count' => 'W',
    'lose_count' => 'L',
    'tie_count' => 'T',
    'game_count' => 'Total',
    'flag_drops' => 'Dropped',
    'flag_pickups' => 'Taken',
    'flag_returns' => 'Returned',
    'flag_scores' => 'Scored',
    'asteroid_crashes' => 'Asteroid&nbsp;Crashes',
    'teleport_uses' => 'Teleportations',
    'switched_team_count' => 'Team&nbsp;Switches',
    'last_played' => 'Last&nbsp;Seen',
);


// build filter
$filters = array();
if ($alltime != 'yes') {
  $filters[] = "time_period='$year-$month-01'";
}
if ($player != '') {
  $filters[] = "player_name LIKE '%" . html_entity_decode($player) . "%'";
}
if ($authed != '') {
  $filters[] = "is_authenticated=" . ($authed == 'yes' ? '1' : '0');
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
    ORDER BY
      $order
      $sort
    LIMIT $start,$per_page
    ;
  ";

  // should only need to check for the current month
  // (as long as one game has been played)
  $last_modified_query = "
    SELECT
      MAX(last_played) AS last_update
    FROM player_mv
    WHERE time_period = '" . date("Y-m-01") ."'
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
    FROM player_mv
    $filter
    ORDER BY
      $order
      $sort
    LIMIT $start,$per_page
    ;
  ";

  $last_modified_query = "
    SELECT
      MAX(last_played) AS last_update
    FROM player_mv
    WHERE time_period = '$year-$month-01'
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

// // check last modification time against cache headers
// $result = mysqli_query($last_modified_query);
// $result or die(mysqli_error());
// $row = mysqli_fetch_assoc($result);
// $last_modified = strtotime($row['last_update']);
// handle_cache_headers($last_modified);

// count number of results
$result = mysqli_query($mysqli, $count_query);
$result or die(mysqli_error($mysqli));
$row = mysqli_fetch_assoc($result);
$count = $row['count'];
$pages = ceil($count / $per_page);

// run the actual player data query
$result = mysqli_query($mysqli, $stats_query);
$result or die(mysqli_error($mysqli));

$all = array();

while($row = mysqli_fetch_assoc($result)) {
  array_push($all, $row);
}

echo json_encode($all);
