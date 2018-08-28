<?php

//include these to work with the database
include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start(); // getting hawkID of current user
$hawkId = $_SESSION['hawkId'];

//query to obtain just the courses that the currently logged in professor instructs
$query = "SELECT courses.course_id, courses.name FROM courses INNER JOIN professors on courses.course_id=professors.course_id where professors.hawk_id='$hawkId';";

//query to database
$result = queryDB($query, $db);

$courses = array();

$i = 0;

while ($currentCourse = nextTuple($result)) {
        $courses[$i] = $currentCourse;
        $i++;
}

//make JSON object
$response = array();
$response['status'] = 'success';
$response['value']['courses'] = $courses;
header('Content-Type: application/json');
echo(json_encode($response));

?>