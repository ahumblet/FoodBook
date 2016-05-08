<?php
	//only php, no html
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
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





