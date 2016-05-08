<?php
	//only php, no html
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	$title = $_POST["title"];
	$content = $_POST["content"];
	$photo = $_POST["photo"];
	$poster = $_POST["poster"];
	$postee = $_POST["postee"];
	$locationName = $_POST["location"];
	$visibility = $_POST["visibility"];
	
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
		$query = sprintf('insert into post (interactiveID, postingUser, receivingUser, title, textContent, mediaContent, visibility, location, timestamp) values ("%s", "%s", "%s", "%s", "%s", "%s", "%s", NULL, now())', $interactiveId, $poster, $postee, $title, $content, $photo, $visibility);
	} else {
		$query = sprintf('insert into post (interactiveID, postingUser, receivingUser, title, textContent, mediaContent, visibility, location, timestamp) values ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", now())', $interactiveId, $poster, $postee, $title, $content, $photo, $visibility, $locationId);
	}
	$result = $mysqli->query($query);
	
	$returnFile = $_POST["returnFile"];
	$headerString = sprintf("Location: %s?username=%s", $returnFile, $postee);
	header($headerString);
	exit;
?>





