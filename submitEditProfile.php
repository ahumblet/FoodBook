<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];

	$user = 'root';
	$password = 'root';
	$db = 'Nutrition';
	$host = 'localhost';
	$port = 3306;
	
	$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	$username = $loggedInUser;
	if (isset($_POST['submit'])) {
		//response to edit-profile form
		$firstName = $_POST["firstName"];
		$lastName = $_POST["lastName"];
		$email = $_POST["email"];
		$age = $_POST["age"];
		$photo = $_POST["photo"];
		$visibility = $_POST["visibility"];
		$query = sprintf("update profile set firstName = '%s', lastName = '%s', age = '%s', visibility = '%s', email = '%s' where username = '%s'", $firstName, $lastName, $age, $visibility, $email, $username);
		$mysqli->query($query);
		$type = $_POST["type"];
		$query = sprintf("update user set type = '%s' where username = '%s'", $type, $username);
		$mysqli->query($query);
	} elseif (isset($_FILES['fileToUpload'])) {
		$tmp_name  = $_FILES['fileToUpload']['tmp_name'];
		$file_content = addslashes(file_get_contents($tmp_name));
		$query = sprintf("update profile set photo = '%s' where username = '%s'", $file_content, $username);
		$mysqli->query($query);
	} else {
		echo "nothing";
	}
	
	//now go back to profile
	$headerString = sprintf("Location: profile.php?username=%s", $username);
	header($headerString);
	exit;
?>
