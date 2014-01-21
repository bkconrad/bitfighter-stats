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

connect_to_db();
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

// check last modification time against cache headers
$result = mysql_query($last_modified_query);
$result or die(mysql_error());
$row = mysql_fetch_assoc($result);
$last_modified = strtotime($row['last_update']);
handle_cache_headers($last_modified);

// count number of results
$result = mysql_query($count_query);
$result or die(mysql_error());
$row = mysql_fetch_assoc($result);
$count = $row['count'];
$pages = ceil($count / $per_page);

// run the actual player data query
$result = mysql_query($stats_query);
$result or die(mysql_error());
$playerdata = mysql_fetch_assoc($result);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Bitfighter Player Stats</title>
<link rel='stylesheet' href='player_stats.css'></link>
<script type='text/javascript' src='assets/stats.js'>
</script>
</head>
<body>

<?php
make_pager();
?>

<form id='search-form' action="stats.php" method="get">
  <div>
    <div id="player-count"><?php print $count ?> result<?php print ($count != 1 ? 's' : '') ?> found</div>
    <label>Player name contains: </label><input type="text" name="player" value="<?php print $player; ?>"></input>
    <input type="submit" value="Search"></input>
    <div id="advanced-search">
    <fieldset>
    <legend>Authentication</legend>
    <input type="radio" name="authed" value="yes" <?php print $authed=='yes' ? 'checked' : '' ?>>Authenticated players</input><br/>
    <input type="radio" name="authed" value="no" <?php print $authed=='no' ? 'checked' : '' ?>>Unauthenticated players</input><br/>
    <input type="radio" name="authed" value="" <?php print ($authed == '') ? 'checked' : '' ?>>Both</input>
    </fieldset>
    <input type="hidden" name="alltime" value="<?php print $alltime ?>"></input>
    <input type="hidden" name="year" value="<?php print $year ?>"></input>
    <input type="hidden" name="month" value="<?php print $month ?>"></input>
    </div>
  </div>
</form>
<div id="month-select">
<?php

// convert to months since 0 AD to iterate through them
// interval of one month
$current = new DateTime();
$current->sub(new DateInterval('P5M'));
$one_month = new DateInterval('P1M');
$link_month = date('n');
$link_year = date('Y');
make_link("All Time", array("alltime" => 'yes', 'page' => '1'));
for ($i = 5; $i >= 0; $i--) {
  $link_month = $current->format('n');
  $link_year = $current->format('Y');
  $link_month_name = $current->format('F');

  if ($link_month == $month && $link_year == $year && $alltime != 'yes') {
    print '<span>' . $link_month_name . ' ' . $link_year . ' </span>';
  } else {
    make_link($link_month_name . " " . $link_year . " ", array("month" => $link_month, "year" => $link_year, "alltime" => 'no', 'page' => '1'));
  }
  $current->add($one_month);
}

?>
</div>
<?php
if (mysql_num_rows($result) > 0):
?>
<table>
  <tr class='table-sections'>
  <td colspan='6'>General</td>
  <td colspan='5'>Games</td>
  <td colspan='4'>Flags</td>
  <td colspan='4'>Misc</td>
  </tr>
  <tr class="table-head"><td>#</td>
<?php
  // Make header
  $sortchar = ($sort == 'DESC' ? '&#9660;' : '&#9650;');
  $header_class = '';
  foreach($displayed_fields as $key => $value) {
    $active = $key == $order;
    if ($active) {
      $link_sort = $sort == 'DESC' ? 'ASC' : 'DESC';
      $header_class = 'active';
    } else {
      $link_sort = 'DESC';
    }
    print "<td id=\"$key\" class=\"$header_class\">";
    make_link($value . ($active ? $sortchar : '') , array("order" => $key, "page" => 1, "sort" => $link_sort));
    print "</td>\n";
  }
  print "</tr>";

  // Fill table body
  $i = $start + 1;
  $parity = true;
  while ($playerdata) {
    foreach ($playerdata as $k => $v) {
      $playerdata[$k] = htmlspecialchars($v);
    }

    // Row parity
    print "<tr class=\"" . ($parity ? 'odd' : 'even') . "\">\n";
    $parity = !$parity;

    // Rank column
    print "\t<td>$i</td>\n";
    $i += 1;

    // Iterate through fields to be displayed and find the values in the current row
    foreach(array_keys($displayed_fields) as $field) {
      // Left-align player names
      $cell_class = ($field == 'player_name' ? 'left' : '');
      $cell_class = ($field == 'last_played' ? 'last-seen' : $cell_class);

      print "\t<td class='$cell_class'>";

      if ($field == 'kill_death_ratio') {
        // KDR to two decimal places
        printf("%.2f",$playerdata[$field]);

      } else if ($field == 'player_name') {
        // Make link to detailed player stats
        $link_player_name = urlencode(html_entity_decode($playerdata['player_name']));
        $link_class = $playerdata['is_authenticated'] ? 'auth' : '';
        print "<a class='$link_class' href=\"player.php?player=$link_player_name&alltime=$alltime&year=$year&month=$month\">" . str_replace(" ", "&nbsp;", $playerdata[$field]) . "</a>";

      } else {
        // Otherwise, just print
        print $playerdata[$field];
      }
      print "</td>\n";
    }
    print "</tr>\n";
    $playerdata = mysql_fetch_assoc($result);
  }
  print "</table>";

else:
  print "<p>No results to display</p>";

endif;

make_pager();
printf("<!--Generated in %ss at %s -->", round(microtime(true) - $start_time, 6), date('H:i:s'));
?>
</body>
</html>
