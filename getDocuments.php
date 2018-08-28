<?php

include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

// retrieve js data
$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'];

$query = "SELECT * FROM course_docs WHERE course_id='$course_id';";

$result = queryDB($query, $db);

$documents = array();

$i = 0;

while ($currentDocument = nextTuple($result)) {
        $documents[$i] = $currentDocument;
        $i++;
}

$response = array();
$response['status'] = 'success';
$response['value']['documents'] = $documents;
header('Content-Type: application/json');
echo(json_encode($response));

?>
