<?php
	session_start();
	$username = $_SESSION["username"];
	
	echo "<link rel='stylesheet' href='login.css'>";

	
	
	printf("%s's profile: ", $username);
	
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

	//get the profile entry
	$query = sprintf("select * from profile where username = '%s'", $username);
	$result = $mysqli->query($query);
	$entry = $result->fetch_assoc();
	
	//get the profile fields
	$query = sprintf("select column_name from information_schema.columns where table_schema = '%s' and table_name = 'profile'", $db);
	$profileFields = $mysqli->query($query);
	
	if ($profileFields->num_rows > 0) {
		while($row = $profileFields->fetch_assoc()) {
			$field = $row["column_name"];
			$fieldValue = $entry[$field];
			printf("<br>%s: %s", $row["column_name"], $fieldValue);
		}
	}
?>

