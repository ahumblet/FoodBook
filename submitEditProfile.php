<?php
	session_start();
	$username = $_SESSION["username"];

	$user = 'root';
	$password = 'root';
	$db = 'Nutrition';
	$host = 'localhost';
	$port = 3306;
	
	$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	if (isset($_POST['submit'])) {
		//response to edit-profile form
		$firstName = $_POST["firstName"];
		$lastName = $_POST["lastName"];
		$age = $_POST["age"];
		$photo = $_POST["photo"];
		$visibility = $_POST["visibility"];
		
		//make changes to the database
		$query = sprintf("delete from profile where username = '%s'", $username);
		$result = $mysqli->query($query);
		
		$query = sprintf("insert into profile (username, firstName, lastName, age, photo, visibility) values ('%s', '%s', '%s', '%s', '%s', '%s')", $username, $firstName, $lastName, $age, $photo, $visibility);
		$result = $mysqli->query($query);
		
		//now go back to profile
		header("Location: profile.php");
		exit;
	} else {
		echo "profile was not edited?";
	}
?>
