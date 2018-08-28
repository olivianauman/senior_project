<?php
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// Receive data from client
$data = json_decode(file_get_contents('php://input'), true);

$scheduled_id = $data['scheduled_id'];

session_start();
$hawkId = $_SESSION['hawkId'];

$isComplete = true;
$errorMessage = '';
$response = array();
$status = '';

if (!isset($scheduled_id)) {
	$isComplete = false;
  $status = 'error';
	$errorMessage .= "No scheduled session id received. ";
}

// Make sure we have a session to cancel and that the logged in student is canceling
if($isComplete) {
    // Get student_id to ensure correct student canceling but also get session_id
		$query = "SELECT student_id, session_id
              FROM scheduled_sessions
              JOIN sessions ON sessions.id=scheduled_sessions.session_id
              WHERE scheduled_sessions.id='$scheduled_id';";

		$result = queryDB($query, $db);
		$row = nextTuple($result);

		if(nTuples($result) === 0) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Scheduled session with id $scheduled_id does not exist. ";
		} elseif ($row['student_id'] != $hawkId) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Only the student that scheduled the session may delete the session. ";
		}
}

// Delete the scheduled_session row and update the session row
if ($isComplete) {
    $session_id = $row['session_id'];

    $queryDeleteScheduled = "DELETE FROM scheduled_sessions WHERE id='$scheduled_id';";
		$queryUpdateSession = "UPDATE sessions SET available=1 WHERE id='$session_id';";
    $queryToUpdateCredits = "UPDATE students SET session_credits=session_credits+1 WHERE hawk_id='$hawkId';";

    queryDB($queryDeleteScheduled, $db);
		queryDB($queryUpdateSession, $db);
    queryDB($queryToUpdateCredits, $db);

		$status = 'success';
		$response['status'] = $status;
} else {
	// Something's wrong - send back an error
	ob_start();
	var_dump($data);
	$postDump = ob_get_clean();

	$response = array();
	$status = 'error';
	$response['message'] = $errorMessage;
	$response['status'] = $status;
	// Split this off so it doesn't show up for the user
	$response['postDump'] = $postDump;
}

// Send response back
header('Content-Type: application/json');
echo(json_encode($response));

?>
