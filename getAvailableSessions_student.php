<?php

//include these to work with the database
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start();
$hawkId = $_SESSION['hawkId'];

$tablename = 'sessions';

// Query to obtain just the sessions with readable dates, ordered by date asc
$query = "SELECT sessions.id, users.first_name, users.last_name, sessions.course_id,
          DATE_FORMAT(sessions.slot, '%M %D, %Y %I:%i %p') as slot_date
          FROM $tablename
          JOIN users ON users.hawk_id=sessions.tutor_id
          WHERE sessions.course_id=
            (SELECT students.course_id FROM students WHERE hawk_id='$hawkId')
          AND sessions.slot >= CURDATE()
          AND available=TRUE
          ORDER BY slot ASC;";

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
