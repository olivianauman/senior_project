<?php
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// Receive data from client
$data = json_decode(file_get_contents('php://input'), true);

$session_id = $data['session_id'];

session_start();
$hawkId = $_SESSION['hawkId'];

$isComplete = true;
$errorMessage = '';
$response = array();
$status = '';

if (!isset($session_id)) {
	$isComplete = false;
	$errorMessage .= "No session_id received. ";
}

if($isComplete) {
		$query = "SELECT tutor_id FROM sessions WHERE id='$session_id' AND available=TRUE;";

		$result = queryDB($query, $db);
		$row = nextTuple($result);
		if(nTuples($result) === 0) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Session with id $session_id either does not exist or has been scheduled. ";
		} elseif ($row['tutor_id'] != $hawkId) {
			$isComplete = false;
			$status = 'error';
			$errorMessage .= "Only the tutor that scheduled the session may delete the session. ";
		}
}

if ($isComplete) {
		$query = "DELETE FROM sessions WHERE id='$session_id';";
		queryDB($query, $db);
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
	$response['tutor_id'] = $row['tutor_id'];
	$response['hawk_id'] = $hawkId;
	// Split this off so it doesn't show up for the user
	$response['postDump'] = $postDump;
}

// Send response back
header('Content-Type: application/json');
echo(json_encode($response));

?>
