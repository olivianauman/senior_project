<?php
    include_once('config.php');
    include_once('dbutils.php');

    // get data from form
    $data = json_decode(file_get_contents('php://input'), true);
    $hawkId = $data['hawkId'];
	$password = $data['password'];
    $role = $data['role'];

   // connect to the database
    $db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);

    // check for required fields
    $isComplete = true;
    $errorMessage = "";

    // check if hawkId meets criteria
    if (!isset($hawkId) || (strlen($hawkId) < 2)) {
        $isComplete = false;
        $errorMessage .= "Please enter a hawkId with at least two characters. ";
    } else {
        $hawkId = makeStringSafe($db, $hawkId);
    }

    if (!isset($password) || (strlen($password) < 6)) {
        $isComplete = false;
        $errorMessage .= "Please enter a password with at least six characters. ";
    }

    if ($isComplete) {
        // get the hashed password from the user with the email that got entered
        $query = "SELECT first_name, last_name, $role, hashedpass FROM users WHERE hawk_id='$hawkId';";
        $result = queryDB($query, $db);

        if (nTuples($result) == 0) {
            // no such hawkId
            $errorMessage .= " HawkId $hawkId does not correspond to any account in the system. ";
            $isComplete = false;
        }
    }

    if ($isComplete) {

        $row = nextTuple($result);
		$roleValue = $row[$role];
		$fullName = $row['first_name'] . ' ' . $row['last_name'];

		if ($roleValue != true) {
            //if role is set to false it is unauthorized
            $errorMessage .= " Invalid Credentials. Please be sure you have selected the correct role. ";
            $isComplete = false;
        }
    }

    if ($isComplete) {
        // there is an account that corresponds to the email that the user entered
		// get the hashed password for that account
		$hashedpass = $row['hashedpass'];
		$id = $row['id'];

		// compare entered password to the password on the database
        // $hashedpass is the version of hashed password stored in the database for $hawkId
        // $hashedpass includes the salt, and php's crypt function knows how to extract the salt from $hashedpass
        // $password is the text password the user entered in login.html
		if ($hashedpass != crypt($password, $hashedpass)) {
            // if password is incorrect
            $errorMessage .= " The password you enterered is incorrect. ";
            $isComplete = false;
        }
    }

    if ($isComplete) {
        // password was entered correctly

        // start a session
        // if the session variable 'hawkId' is set, then we assume that the user is logged in
        session_start();
        $_SESSION['hawkId'] = $hawkId;
        $_SESSION['autorizedRole'] = $role;

        // send response back
        $response = array();
        $response['status'] = 'success';
		$response['message'] = 'logged in';
        $response['authorizedRole'] = $role;
        header('Content-Type: application/json');
        echo(json_encode($response));
    } else {
        // there's been an error. We need to report it to the angular controller.

        // one of the things we want to send back is the data that his php file received
        ob_start();
        // uncomment when testing
        //var_dump($data);
        $postdump = ob_get_clean();

        // set up our response array
        $response = array();
        $response['status'] = 'error';
        $response['message'] = $errorMessage . $postdump;
        header('Content-Type: application/json');
        echo(json_encode($response));
    }

?>
