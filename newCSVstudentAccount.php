<?php  
// connect to the database
$db = connectDB($DBHost, $DBUser, $DBPassword, $DBName);


if(isset($_POST["submit"]))
{
 if($_FILES['file']['name'])
 {
  $filename = explode(".", $_FILES['file']['name']);
  if($filename[1] == 'csv')
  {
   $handle = fopen($_FILES['file']['tmp_name'], "r");
   while($data = fgetcsv($handle))
   {
        $hawkId = mysqli_real_escape_string($db, $data[0]);  
        $firstName = mysqli_real_escape_string($db, $data[1]);
        $lastName = mysqli_real_escape_string($db, $data[2]);  
        $password = mysqli_real_escape_string($db, $data[3]);
        $hashedpass = crypt($password, getsalt());
        $courseId = mysqli_real_escape_string($db, $data[4]);
        $queryUser = "INSERT INTO users(hawk_id, first_name, last_name, hashedpass, student, tutor, administrator, professor) VALUES ('$hawkId', '$firstName', '$lastName', '$hashedpass', TRUE, FALSE, FALSE, FALSE);";
        $queryStudent = "INSERT INTO students(hawk_id, course_id) VALUES ('$hawkId', '$courseId');";
        mysqli_query($db, $queryUser);
        mysqli_query($db, $queryStudent);
   }
   fclose($handle);
   echo "<script>alert('Import done');</script>";
  }
 }
}
?>