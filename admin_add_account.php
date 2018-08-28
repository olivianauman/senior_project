<?php
include_once('config.php');
include_once('dbutils.php');

// get data from form
$data = json_decode(file_get_contents('php://input'), true);
$hawkId = $data['hawk_id'];
$role = $data['role'];
$firstName = $data['first_name'];
$lastName = $data['last_name'];
$password = $data['password'];
$courseId = $data['course_id'];
$phone = $data['phone_number'];

// Get currently logged in user's role so we can double check their authorization
session_start();
$authorizedRole = $_SESSION['authorizedRole'];

// connect to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// check for required fields
$errorMessage = "";
$isComplete = true;
$status = 'success';
// response array
$response = array();

// Set the proper variables to report an error, and craft the error message
function responseError($message) {
	$isComplete = false;
	$status = 'error';
	$errorMessage .= $message . ' ';
}

// be sure user can add users
if ($authorizedRole != 'administrator') {
		responseError("You must be an admin to add users.");
}

// require role
if (!isset($role)) {
		responseError("Role is required.");
} else {
		$role = makeStringSafe($db, $role);
}

// check if hawkId meets criteria
if (!isset($hawkId) || (strlen($hawkId) < 2)) {
    responseError("Please enter a hawkId with at least two characters.");
} else {
    $hawkId = makeStringSafe($db, $hawkId);
}

//check if first name meets criteria
if (!isset($firstName) || (strlen($firstName) < 1)) {
    responseError("Please enter a password with at least six characters.");
} else {
    $firstName = makeStringSafe($db, $firstName);
}

//check if last name meets criteria
if (!isset($lastName) || (strlen($lastName) < 1)) {
    responseError("Please enter a password with at least six characters.");
} else {
    $lastName = makeStringSafe($db, $lastName);
}

//check if password meets criteria
if (!isset($password) || (strlen($password) < 6)) {
    responseError("Please enter a password with at least six characters.");
} else {
    $hashedpass = crypt($password, getsalt());
    $hashedpass = makeStringSafe($db, $hashedpass);
}

//if this new user is a professor, student, or tutor then they must have a valid course_id
if ($role == 'student' || $role == 'professor' || $role == 'tutor') {
		if (!isset($courseId) || (strlen($courseId) < 6)) {
		    responseError("Please enter a password with at least six characters.");
		} else {
		    $courseId = makeStringSafe($db, $courseId);
		}
}

// since this is not a required, we will simply make it safe if it's present
if (isset($phone)) {
		$phone = makeStringSafe($db, $phone);
}

//Make sure this hawk_id is not already in the db
if ($isComplete) {
	$query = "SELECT first_name FROM users WHERE hawk_id='$hawk_id';";
	$result = queryDB($query, $db);

	if(nTuples(result) > 0) {
		responseError("A user with hawkId $hawkId already exists. Please choose a different one.");
	}
}

if ($isComplete) {
		// get a value for each of these fields so that we can enter them into the db
		// this way we force ourslves to be a little more sure that 1 of them is true,
		// rather than defaulting all of them to false in the db and then forgetting
		// to set one later
		$student = ($role == 'student') ? true : false;
		$tutor = ($role == 'tutor') ? true : false;
		$professor = ($role == 'professor') ? true : false;
		$administrator = ($role == 'administrator') ? true : false;

		// we always create a user. User their role to set the proper column to true
    $queryUser = "INSERT INTO users(hawk_id, first_name, last_name, hashedpass, student, tutor, professor, administrator)
									VALUES ('$hawkId', '$firstName', '$lastName', '$hashedpass', '$student', '$tutor', '$professor', '$administrator');";

		queryDB($queryUser, $db);

		// depending on the user's role, we also need to add them to their specific table
		switch ($role) {
			 case 'student':
			 		$queryRoleTable = "INSERT INTO students(hawk_id, course_id, phone_number)
														VALUES ('$hawkId', '$courseId', '$phone');";
					queryDB($queryRoleTable, $db);
			 		break;
			 case 'tutor':
			 		$queryRoleTable = "INSERT INTO tutors(hawk_id, course_id, phone_number)
														VALUES ('$hawkId', '$courseId', '$phone');";
					queryDB($queryRoleTable, $db);
			 		break;
			case 'professor':
					$queryRoleTable = "INSERT INTO professors(hawk_id, course_id)
														VALUES ('$hawkId', '$courseId');";
					queryDB($queryRoleTable, $db);
	 			 	break;
			// no need for a special admin table, so just continue on
			case 'administrator':
					break;
			default:
				 	responseError("Role must be student, tutor, professor, or administrator");
				 	break;
		}

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
  	$response['message'] = $errorMessage . "\nDetails: $postdump";
  	header('Content-Type: application/json');
  	echo(json_encode($response));
}

?>
