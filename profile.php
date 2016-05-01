<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	echo "<link rel='stylesheet' href='login.css'>";
	
	$profileUsername = $_GET["username"];
	
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
	$query = sprintf("select * from profile where username = '%s'", $profileUsername);
	$result = $mysqli->query($query);
	$entry = $result->fetch_assoc();
	
	//print profile title
	printf("%s's profile: ", $profileUsername);
	
	//get the profile fields
	$query = sprintf("select column_name from information_schema.columns where table_schema = '%s' and table_name = 'profile'", $db);
	$profileFields = $mysqli->query($query);
	
	if ($profileFields->num_rows > 0) {
		while($row = $profileFields->fetch_assoc()) {
			$field = $row["column_name"];
			$fieldValue = $entry[$field];
			if ($row["column_name"] == "photo") {
				echo "<br>photo: <br>";
				echo '<img src="data:image/jpeg;base64, ' . base64_encode($fieldValue) . '"/>';
			} else {
				printf("<br>%s: %s", $row["column_name"], $fieldValue);
			}
		}
	}
	
	if ($loggedInUser == $profileUsername) {
		echo '<form action="editProfile.php">';
		echo '<input type="submit" value="Edit Profile">';
		echo '</form>';
	}
?>


