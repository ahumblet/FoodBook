<?php
	//establish connection and global variables
	$user = 'root';
	$password = 'root';
	$db = 'Nutrition';
	$host = 'localhost';
	$port = 3306;
	$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	//retrieve what was submitted in form
	$username = $_POST["username"];
	$password = $_POST["password"];
	$email = $_POST["email"];

	//check if username already exists
	$query = sprintf("select username from user where username = '%s'", $username);
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		header("Location: login.php?userError=2");
		exit;
	} else {
		header("Location: profile.php");
		exit;
	}
?>
