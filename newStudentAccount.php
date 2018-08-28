<?php
include_once('config.php');
include_once('dbutils.php');

// get data from form
$data = json_decode(file_get_contents('php://input'), true);
$hawkId = $data['hawk_id'];
$firstName = $data['first_name'];
$lastName = $data['last_name'];
$password = $data['password'];
$courseId = $data['course_id'];

// connect to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// check for required fields
$errorMessage = "";
$isComplete = true;
$status = 'success';
// response array
$response = array();


// check if hawkId meets criteria
if (!isset($hawkId) || (strlen($hawkId) < 2)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a hawkId with at least two characters. ";
} else {
    $hawkId = makeStringSafe($db, $hawkId);
}
//check if first name meets criteria
if (!isset($firstName) || (strlen($firstName) < 1)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a password with at least six characters. ";
} else {
    $firstName = makeStringSafe($db, $firstName);
}

//check if last name meets criteria
if (!isset($lastName) || (strlen($lastName) < 1)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a password with at least six characters. ";
} else {
    $lastName = makeStringSafe($db, $lastName);
}
//check if password meets criteria
if (!isset($password) || (strlen($password) < 6)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a password with at least six characters. ";
} else {
    $hashedpass = crypt($password, getsalt());
    $hashedpass = makeStringSafe($db, $hashedpass);
}
//check if course_id meets criteria
if (!isset($courseId) || (strlen($courseId) < 6)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a password with at least six characters. ";
} else {
    $courseId = makeStringSafe($db, $courseId);
}

//Make sure this hawk_id is not already in the db
if ($isComplete) {
	$query = "SELECT first_name FROM users WHERE hawk_id='$hawkId';";
	$result = queryDB($query, $db);

	if(nTuples($result) > 0) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "A user with hawkId $hawkId already exists. Please choose a different one. ";
	}
}

// Make sure the course id exists
if ($isComplete) {
  $query = "SELECT name FROM courses WHERE course_id='$courseId';";
  $result = queryDB($query, $db);

  if(nTuples($result) == 0) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Course id $courseId does not exist. Please choose a different one. ";
  }
}

if ($isComplete) {
    $queryUser = "INSERT INTO users(hawk_id, first_name, last_name, hashedpass, student, tutor, administrator, professor) VALUES ('$hawkId', '$firstName', '$lastName', '$hashedpass', TRUE, FALSE, FALSE, FALSE);";
    $queryStudent = "INSERT INTO students(hawk_id, course_id) VALUES ('$hawkId', '$courseId');";

    queryDB($queryUser, $db);
    queryDB($queryStudent, $db);

    $status = 'success';
    $response['status'] = $status;

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
  	$response['message'] = $errorMessage;
    $resposne['postDump'] = "\nDetails: $postdump";
  	header('Content-Type: application/json');
  	echo(json_encode($response));
}

?>
