<?php

include_once('config.php');
include_once('dbutils.php');

$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

$tablename = "users";
$query = "SELECT * FROM $tablename;";

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