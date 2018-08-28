<?php

include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

session_start();
$hawkId = $_SESSION['hawkId'];

$query = "SELECT * FROM users RIGHT JOIN students on (users.hawk_id=students.hawk_id) WHERE users.student=1 AND students.course_id=(SELECT professors.course_id FROM professors WHERE professors.hawk_id='$hawkId');";

$result = queryDB($query, $db);

$users = array();

$i = 0;

while ($currentUser = nextTuple($result)) {
        $users[$i] = $currentUser;
        $i++;
}

$response = array();
$response['status'] = 'success';
$response['value']['users'] = $users;
header('Content-Type: application/json');
echo(json_encode($response));

?>