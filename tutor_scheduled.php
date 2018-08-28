<?php

include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start();
$hawkId = $_SESSION['hawkId'];
/*
$query = "SELECT available_sessions.course_id, available_sessions.slotDate, available_sessions.slotTime, available_sessions.tutor_id, scheduled_sessions.student_id, scheduled_sessions.doc_id,
		  FROM available_sessions INNER JOIN scheduled_sessions
		  ON available_sessions.id = scheduled_sessions.id
		  WHERE available_sessions.tutor_id = '$hawkId';";

$query = "SELECT * FROM scheduled_sessions JOIN available_sessions on (scheduled_sessions.id=available_sessions.id) WHERE (scheduled_sessions.tutor_id='$hawkId');";
*/

$query = "SELECT A.course_id, A.slot, A.slot, A.tutor_id, S.student_id, S.doc_id FROM scheduled_sessions S, available_sessions A WHERE S.session_id=A.id AND A.tutor_id = '$hawkId';";
$result = queryDB($query, $db);

$sessions = array();

$i = 0;

while ($currentSessions = nextTuple($result)) {
        $sessions[$i] = $currentSessions;
        $i++;
}

$response = array();
$response['status'] = 'success';
$response['value']['sessions'] = $sessions;
header('Content-Type: application/json');
echo(json_encode($response));

?>