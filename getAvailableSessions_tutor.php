<?php

include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start();
$hawkId = $_SESSION['hawkId'];

$tablename = 'sessions';

//query to obtain just the sessions in the future and with readable dates
$query = "SELECT id, course_id, DATE_FORMAT(slot, '%M %D, %Y %I:%i %p') as slot_date
          FROM $tablename
          WHERE tutor_id='$hawkId'
          AND available=TRUE
          AND sessions.slot >= CURDATE()
          ORDER BY slot ASC;";

$result = queryDB($query, $db);

$sessions = array();

$i = 0;

while ($currentSession = nextTuple($result)) {
    $sessions[$i] = $currentSession;
    $i++;
}

$response = array();
$response['status'] = 'success';
$response['value']['sessions'] = $sessions;
header('Content-Type: application/json');
echo(json_encode($response));

?>
