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

	//check for valid username and password
	$query = sprintf("select username from user where (username = '%s') and (password = MD5('%s'))", $username, $password);
	$result = $mysqli->query($query);
	if ($result->num_rows == 1) {
		header("Location: profile.php");
		exit;
	} else {
		header("Location: login.php?userError=1");
		exit;
	}
?>
