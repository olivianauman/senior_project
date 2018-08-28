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
$slot = $data['slot'];

//set up variables to handle errors
// is complete will be false if we find any problems when checking on the data
$isComplete = true;

//error message we'll send back to angular if we run into any problems
$errorMessage = "";

//
// Validation
//

// Only supporting U of I timezone.. set this so that FROM_UNIXTIME works well
date_default_timezone_set('America/Chicago');

// Try to create a date from the js iso date received. If it fails, send back invalid date error
$slot = strtotime($slot);

//check if slot isset or is false (strototime sets returns false if it can't parse the date)
if (!isset($slot) || !$slot) {
	$isComplete = false;
	$errorMessage .= "Please enter a valid date.";
}

//check if we already have a session with the same date and tutor as the one we are processing
if ($isComplete) {
	//set up a query to check if this session is in the database
	$query = "SELECT id FROM sessions WHERE slot='FROM_UNIXTIME($slot)' AND tutor_id='$hawkId';";


	//run the query
	$result = queryDB($query, $db);
	//check on the number of records returned
	if(nTuples($result) > 0) {
		// if we get at least one record back it means the movie is already in the database, so we have a duplicate
		$isComplete = false;
		$errorMessage .= "The session $slot, for tutor '$hawkId', is taken, please select another time.";

	}
}

// if we got this far and $isComplete is true it means we should add the movie to the database
if ($isComplete) {
	// we will set up the insert statement to add this new record to the database
	$insertquery = "INSERT INTO sessions(slot, course_id, tutor_id) VALUES (FROM_UNIXTIME($slot), (SELECT course_id FROM tutors WHERE hawk_id ='$hawkId'), '$hawkId');";

	//run the insert statement
	queryDB($insertquery, $db);

	// get the id for movie we just entered
	$movieid = mysqli_insert_id($db);

	//send a response back to angular
	$response = array();
	$response['status'] = 'success';
	header('Content-Type: application/json');
	echo(json_encode($response));
} else {
	// there's been an error. We need to report it to the angular controller.

	// one of the things we want to send back is the data that this php file recieved
	ob_start();
	var_dump($data);
	$postdump = ob_get_clean();

	//set up response array
	$response = array();
	$response['status'] = 'error';
	$response['message'] = $errorMessage . "\nDetails: $postdump";
	header('Content-Type: application/json');
	echo(json_encode($response));
}

?>
