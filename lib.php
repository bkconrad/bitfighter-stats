<?php
/**
 * Send expiration header data and 304 if unmodified
 * @param last_modified modification time in seconds since the epoch
 */ 
function handle_cache_headers($last_modified) {
  header('Last-Modified:' .
  gmdate('D,d M Y H:i:s', $last_modified) . ' GMT');
  $request= getallheaders();
  if(isset($request['If-Modified-Since'])) {
    $modifiedSince = explode(';', $request['If-Modified-Since']);
    $modifiedSince = strtotime($modifiedSince[0]);
  }else {
    $modifiedSince= 0;
  }

  if($last_modified && $last_modified <= $modifiedSince) {
    header('HTTP/1.1 304 Not Modified');
    exit();
  }
}

/**
 * Pulls listed params into variables in the global namespace after sanitizing and setting
 * default values.
 * @param arr An associative array of form "param_name" => "default_value"
 */
function get_params($arr) {
  foreach ($arr as $key => $value) {
    global $$key;
    if (isset($_REQUEST[$key])) {
      $$key = sanitize(htmlspecialchars($_REQUEST[$key]));
    }
    $$key = empty($$key) ? $value : $$key;
  }
}

function time_ago ($oldtime) {
  $secs = time() - strtotime($oldtime);
  $bit = array(
    '&nbsp;year'   => round($secs / 31556926),
    '&nbsp;month'  => $secs / 2592000 % 12,
    '&nbsp;week'   => $secs / 604800 % 52,
    '&nbsp;day'    => $secs / 86400 % 7,
    '&nbsp;hour'   => $secs / 3600 % 24,
    '&nbsp;minute' => $secs / 60 % 60,
    '&nbsp;second' => $secs % 60
  );

  foreach($bit as $unit => $value){
    if($value > 1) {
      $ret[] = $value . $unit . 's';
    } else if($value == 1) {
      $ret[] = $value . $unit;
    } else {
      continue;
    }
    break;
  }
  $ret[] = 'ago';

  return join('&nbsp;', $ret);
}

?>
