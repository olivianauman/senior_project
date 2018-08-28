<?php

//include these to work with the database
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start(); // getting hawkID of current user
$hawkId = $_SESSION['hawkId'];

//query to obtain just the scheduled sessions for this user
$query = "SELECT scheduled_sessions.id, users.first_name, users.last_name, sessions.course_id,
          DATE_FORMAT(slot, '%M %D, %Y %I:%i %p') as slot_date
          FROM scheduled_sessions
          JOIN sessions on (scheduled_sessions.session_id=sessions.id)
          JOIN users ON users.hawk_id=sessions.tutor_id
          WHERE (scheduled_sessions.student_id='$hawkId')
          AND sessions.slot >= CURDATE()
          ORDER BY sessions.slot ASC;";

$queryCredits = "SELECT session_credits FROM students WHERE hawk_id='$hawkId';";

//query to database
$result = queryDB($query, $db);
$resultCredits = queryDB($queryCredits, $db);

$sessions = array();

$i = 0;

while ($currentSession = nextTuple($result)) {
    $sessions[$i] = $currentSession;
    $i++;
}

$creditsRow = nextTuple($resultCredits);

//make JSON object
$response = array();
$response['status'] = 'success';
$response['value']['sessions'] = $sessions;
$response['value']['credits'] = $creditsRow['session_credits'];
header('Content-Type: application/json');
echo(json_encode($response));

?>
