<?php
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
$tableName = 'users';

// Receive data from client
$data = json_decode(file_get_contents('php://input'), true);

$hawk_id = $data['hawk_id'];

$isComplete = true;
$errorMessage = '';
$response = array();
$status = '';

if (!isset($hawk_id)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= "Must include Hawk_id of movie to be deleted: $hawk_id";
}

if($isComplete) {
	$query = "SELECT first_name FROM $tableName WHERE hawk_id='$hawk_id';";
	$result = queryDB($query, $db);
	
	if(nTuples(result) === 0) {
		$status = 'error';
		$errorMessage .= "Hawk_id $hawk_id does not match any record in the 'movies' table";
	}
}

if ($isComplete) {
	$query = "DELETE FROM $tableName WHERE hawk_id='$hawk_id';";
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
	// Split this off so it doesn't show up for the user
	$response['postDump'] = $postDump;
}

// Send response back
header('Content-Type: application/json');
echo(json_encode($response));

?>