<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
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
	
	//add a new location entry
	if (isset($_POST["addLocation"])) {
		$locName = $_POST["locName"];
		$longitude = $_POST["longitude"];
		$latitude = $_POST["latitude"];
		
		//create new post entry
		$query = sprintf("INSERT INTO interactive (interactiveID) VALUES (NULL);");
		$result = $mysqli->query($query);
		$interactiveId = $mysqli->insert_id;
		
		$query = sprintf("insert into location (interactiveID, locName, longitude, latitude) value ('%s', '%s', '%s', '%s')", $interactiveId, $locName, $longitude, $latitude);
		$mysqli->query($query);
		
	} elseif (isset($_POST["likeLocation"])) {
		$interactiveID = $_POST["likeLocation"];
		$liker = $loggedInUser;
		$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp, value) values ('%s', '%s', now(), 'like')", $interactiveID, $liker);
		$mysqli->query($query);
	}
	
	//go back to locations page
	$headerString = sprintf("Location: locations.php");
	header($headerString);
	exit;
?>





