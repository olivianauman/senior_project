<?php
include_once('config.php');
include_once('dbutils.php');

// get data from form
$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'];
$doc_name = $data['doc_name'];
$doc_data = $data['doc_data'];


// connect to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// check for required fields
$errorMessage = "";
$isComplete = true;
$status = 'success';
// response array
$response = array();


// check if name meets criteria
if (!isset($doc_name) || (strlen($doc_name) < 1)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a document name with at least one character. ";
} else {
    $doc_name = makeStringSafe($db, $doc_name);
}
//check if text meets criteria
if (!isset($doc_data) || (strlen($doc_data) < 1)) {
    $isComplete = false;
    $status = 'error';
    $errorMessage .= "Please enter a document with at least one character. ";
} else {
    $doc_data = makeStringSafe($db, $doc_data);
}

$course_id = makeStringSafe($db, $course_id);

if ($isComplete) {
    $query = "INSERT INTO course_docs(course_id, doc_name, doc_data) VALUES ('$course_id', '$doc_name', '$doc_data');";
    queryDB($query, $db);

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
