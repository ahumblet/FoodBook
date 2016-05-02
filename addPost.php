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
	
	$title = $_POST["title"];
	$content = $_POST["content"];
	$photo = $_POST["photo"];
	$poster = $_POST["poster"];
	$postee = $_POST["postee"];
	$locationName = $_POST["location"];
	
	//find associated location ID to use in new post query
	$locationId = 'NULL';
	$query = sprintf("SELECT * from location where locName = '%s'", $locationName);
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$locationId = $row["interactiveID"];
	}
	
	//create new post entry
	$query = sprintf("INSERT INTO interactive (interactiveID) VALUES (NULL);");
	$result = $mysqli->query($query);
	$interactiveId = $mysqli->insert_id;
	
	//this is a sloppy way of dealing with the fact that NULL locationID can't be in quotes
	if ($locationId == 'NULL') {
		$query = sprintf('insert into post (interactiveID, postingUser, receivingUser, title, textContent, mediaContent, visibility, location, timestamp) values ("%s", "%s", "%s", "%s", "%s", "%s", "everyone", NULL, now())', $interactiveId, $poster, $postee, $title, $content, $photo);
	} else {
		$query = sprintf('insert into post (interactiveID, postingUser, receivingUser, title, textContent, mediaContent, visibility, location, timestamp) values ("%s", "%s", "%s", "%s", "%s", "%s", "everyone", "%s", now())', $interactiveId, $poster, $postee, $title, $content, $photo, $locationId);
	}
	$result = $mysqli->query($query);
	
	$headerString = sprintf("Location: wall.php?username=%s", $postee);
	header($headerString);
	exit;
?>





