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
	
	$interactiveId = $_POST["interactiveId"];
	$likingUser = $_POST["likingUser"];
	$likedUser = $_POST["likedUser"];
	
	$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp) VALUE ('%s', '%s', now())", $interactiveId, $likingUser);
	$result = $mysqli->query($query);

	//go back to the wall
	$headerString = sprintf("Location: wall.php?username=%s", $likedUser);
	header($headerString);
	exit;
?>





