<?php
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);
$tableName = 'users';

// Receive data from client
$data = json_decode(file_get_contents('php://input'), true);

$isComplete = true;
$errorMessage = '';
$response = array();
$status = '';

$first_name = $data['first_name'];
$last_name = $data['last_name'];
$student = $data['student'];
$professor = $data['professor'];
$tutor = $data['tutor'];
$administrator = $data['administrator'];
$hawk_id = $data['hawk_id'];


//
// Validation
//

if (!isset($hawk_id)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= "Must include ID of user to be deleted: $hawk_id";
}

if (!isset($first_name)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= 'First name must be at least 1 character long.';
} else {
	$first_name = makeStringSafe($db, $first_name);
}

if (!isset($last_name)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= 'Last name must be at least 1 character long.';
} else {
	$last_name = makeStringSafe($db, $last_name);
}

if (!isset($student)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= 'Student must be yes or no';
} else {
	$student = makeStringSafe($db, $student);
}

if (!isset($tutor)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= 'Student must be yes or no';
} else {
	$tutor = makeStringSafe($db, $tutor);
}

if (!isset($professor)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= 'Student must be yes or no';
} else {
	$professor = makeStringSafe($db, $professor);
}

if (!isset($administrator)) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= 'Student must be yes or no';
} else {
	$administrator = makeStringSafe($db, $administrator);
}

if($isComplete) {
	$query = "SELECT first_name, last_name FROM $tableName WHERE hawk_id='$hawk_id';";
	$result = queryDB($query, $db);

	if(nTuples(result) === 0) {
		$isComplete = false;
		$status = 'error';
		$errorMessage .= "Hawk_id $hawk_id does not match any record in the 'users' table";
	}
}

if ($isComplete) {
	$query = "UPDATE $tableName
			SET first_name='$first_name', last_name='$last_name', student='$student',
			tutor='$tutor', professor='$professor', administrator='$administrator'
			WHERE hawk_id='$hawk_id';";


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
