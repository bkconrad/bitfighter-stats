<?php

date_default_timezone_set('America/Denver');

require "db_functions.php";

$query = "
SELECT
	COUNT(1) AS `count`,
	DAYOFWEEK( `insertion_date` ) AS `day`,
	HOUR( `insertion_date` ) AS `hour` 
FROM  `stats_game` 
GROUP BY CONCAT( DAYOFWEEK( insertion_date ) ,  ' ', HOUR( insertion_date ) ) 
";

$mysqli = connect_to_db();
$result = mysqli_query($mysqli, $query);
$result or die(mysqli_error($mysqli));

$data = array();
while($row = mysqli_fetch_assoc($result)) {
  array_push($data, $row);
}

header('Content-Type: application/json');
echo json_encode($data);