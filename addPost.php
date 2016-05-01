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
	
	//i need to create a new interactive, then I need to create a corresponding post.
	
	$query = sprintf("INSERT INTO interactive (interactiveID) VALUES (NULL);");
	$result = $mysqli->query($query);
	$interactiveId = $mysqli->insert_id;
	
	$query = sprintf('insert into post (interactiveID, postingUser, receivingUser, title, textContent, mediaContent, visibility, location, timestamp) values ("%s", "%s", "%s", "%s", "%s", "%s", "everyone", NULL, now())', $interactiveId, $poster, $postee, $title, $content, $photo);
	
	$result = $mysqli->query($query);
	
	$headerString = sprintf("Location: wall.php?username=%s", $postee);
	header($headerString);
	exit;
?>





