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
	$errorMessage .= "No scheduled session id received. ";
}

// Make sure we have a session to cancel and that the logged in tutor is canceling
if($isComplete) {
    // Get tutor_id to ensure correct tutor canceling but also get session_id
		$query = "SELECT tutor_id, session_id
              FROM scheduled_sessions
              JOIN sessions ON sessions.id=scheduled_sessions.session_id
              WHERE scheduled_sessions.id='$scheduled_id';";

		$result = queryDB($query, $db);
		$row = nextTuple($result);

		if(nTuples($result) === 0) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Scheduled session with id $scheduled_id does not exist. ";
		} elseif ($row['tutor_id'] != $hawkId) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Only the tutor that scheduled the session may delete the session. ";
		}
}

// Delete both the scheduled_session and the session rows
if ($isComplete) {
    // Pull session_id from last query so we can delete the corresponding sessions row
    $session_id = $row['session_id'];
    $queryDeleteScheduled = "DELETE FROM scheduled_sessions WHERE id='$scheduled_id';";
		$queryDeleteSession = "DELETE FROM sessions WHERE id='$session_id';";

    queryDB($queryDeleteScheduled, $db);
		queryDB($queryDeleteSession, $db);

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
