<?php

//we need to include these two gfiles in order to work with the database
include_once('config.php');
include_once('dbutils.php');

// get a handle to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start();
$hawkId = $_SESSION['hawkId'];
$role = $_SESSION['autorizedRole'];

//get data from the angular controller
//decode the json object
$data = json_decode(file_get_contents('php://input'), true);

//get each piece of data
$session_id = $data['session_id'];

//set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

//error message we'll send back to angular if we run into any problems
$errorMessage = "";

$sessions = "sessions";
$scheduled = "scheduled_sessions";

// Array with which we will send back a response to the client
$response = array();

//
// Validation
//

//check if session_id isset or is false (strototime sets returns false if it can't parse the date)
if (!isset($session_id) || !$session_id) {
		$isComplete = false;
		$errorMessage .= "session_id required but was not received. ";
} else {
    $session_id = makeStringSafe($db, $session_id);
}

//be sure we have the session the student is signing up for
if($isComplete) {
		$query = "SELECT course_id FROM $sessions WHERE id='$session_id' AND available=TRUE;";

		$result = queryDB($query, $db);
		$row = nextTuple($result);
		if(nTuples($result) === 0) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Session with id $session_id either does not exist or has already been scheduled. ";
		}

}

// Make sure the student has the right course_id to sign up for this tutoring session
if ($isComplete) {
	$query = "SELECT course_id, session_credits FROM students WHERE hawk_id='$hawkId';";

	$result = queryDB($query, $db);
	$studentRow = nextTuple($result);

	if ($row['course_id'] != $studentRow['course_id']) {
		$isComplete = false;
		$status = 'error';
		$errorMessage .= "You may only sign up for a session for a course that you are part of. ";
	}

	if ($studentRow['session_credits'] == 0) {
		$isComplete = false;
		$status = 'error';
		$errorMessage .= "You must have credits in order to sign up for a session. ";
	}
}

// Now that our checks have passed, we can update sessions and insert into scheduled_sessions
if ($isComplete) {
	// Set the session to be unavailable so we don't schedule it again or delete it
	$queryToUpdateSessions = "UPDATE $sessions SET available=FALSE WHERE id='$session_id';";
	$queryToUpdateCredits =  "UPDATE students SET session_credits=session_credits-1 WHERE hawk_id='$hawkId';";
	// Add the new scheduled session to the database
	$queryToInsert = "INSERT INTO $scheduled(session_id, student_id) VALUES ('$session_id', '$hawkId');";

	// Send the queries to the db
	queryDB($queryToUpdateSessions, $db);
	queryDB($queryToUpdateCredits, $db);
	queryDB($queryToInsert, $db);

	$id = mysqli_insert_id($db);

	// Everything succeeded and we can send back the scheduled_session id
	$response['status'] = 'success';
	$response['id'] = $id;
	header('Content-Type: application/json');
	echo(json_encode($response));
} else {
	// there's been an error. We need to report it to the angular controller.

	// one of the things we want to send back is the data that this php file recieved
	ob_start();
	var_dump($data);
	$postdump = ob_get_clean();

	// Sned back error response
	$response['status'] = 'error';
	$response['message'] = $errorMessage . "\nDetails: $postdump";
	header('Content-Type: application/json');
	echo(json_encode($response));
}

?>
