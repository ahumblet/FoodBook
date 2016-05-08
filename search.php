<?php
	session_start();
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	$loggedInUser = $_SESSION["loggedInUser"];
	$wallUsername = $_GET["username"];
	$searchTerm = $_POST["searchTerm"];

	//search locations
	$query = sprintf("SELECT * FROM LOCATION WHERE locName LIKE '%%%s%%' OR longitude LIKE '%%%s%%' OR latitude LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	$locationMatches = array();
	if ($result->num_rows > 0) {
		while ($match = $result->fetch_assoc()) {
			array_push($locationMatches, $match);
		}
	}
	//search posts
	$query = sprintf("SELECT * FROM POST WHERE postingUser LIKE '%%%s%%' OR receivingUser LIKE '%%%s%%' OR title LIKE '%%%s%%' OR textContent LIKE '%%%s%%' OR location LIKE '%%%s%%' OR timestamp LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	$postMatches = array();
	if ($result->num_rows > 0) {
		while ($match = $result->fetch_assoc()) {
			if (hasPermission($loggedInUser, $match["postingUser"], $match["visibility"])) {
				array_push($postMatches, $match);
			}
		}
	}
	//search and display comments
	restartMysqli();
	$query = sprintf("SELECT * FROM comment WHERE postingUser LIKE '%%%s%%' OR textContent LIKE '%%%s%%' OR location LIKE '%%%s%%' OR timestamp LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	$commentMatches = array();
	if ($result->num_rows > 0) {
		while ($match = $result->fetch_assoc()) {
			if (hasPermission($loggedInUser, $match["postingUser"], $match["visibility"])) {
				array_push($commentMatches, $match);
			}
		}
	}
	//search and display profiles
	$query = sprintf("SELECT * FROM profile NATURAL JOIN user WHERE username LIKE '%%%s%%' OR firstName LIKE '%%%s%%' OR lastName LIKE '%%%s%%' OR age LIKE '%%%s%%' OR email LIKE '%%%s%%' OR type LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	$profileMatches = array();
	if ($result->num_rows > 0) {
		while ($match = $result->fetch_assoc()) {
			if (hasPermission($loggedInUser, $match["username"], $match["visibility"])) {
				array_push($profileMatches, $match);
			}
		}
	}
	
	//HTML
	generateHTMLTop('search');
	if ($searchTerm != "") {
		displayResults();
	} else {
		printf("<div class='pageHeader'> Empty search field. Try again. </div>", $searchTerm);
	}
	generateHTMLBottom();
	
	//====================== Function Definitions ======================//
	
	function displayResults() {
		global $loggedInUser, $searchTerm, $locationMatches, $postMatches, $commentMatches, $profileMatches;
		
		printf("<div class='pageHeader'> search results for: '%s' </div>", $searchTerm);

		//show locations:
		if (sizeof($locationMatches) > 0) {
			printf("<div class='subHeader'>%d location matches:</div>", sizeof($locationMatches));
			foreach ($locationMatches as &$location) {
				printf("<div class='location'>");
				printf("%s: %s, %s<br>", $location["locName"], $location["longitude"], $location["latitude"]);
				printf("</div>");
			}
		}
		//show posts
		if (sizeof($postMatches) > 0) {
			printf("<div class='subHeader'>%d post matches:</div>", sizeof($postMatches));
			foreach ($postMatches as &$match) {
				displayPostOnly($match);
			}
		}
		//show comments
		if (sizeof($commentMatches) > 0) {
			printf("<div class='subHeader'>%d comment matches:</div>", sizeof($commentMatches));
			foreach ($commentMatches as &$match) {
				displayCommentOnly($match);
			}
		}
		//show profiles
		if (sizeof($profileMatches) > 0) {
			printf("<div class='subHeader'>%d profile matches:</div>", sizeof($profileMatches));
			foreach ($profileMatches as &$match) {
				displayLimitedProfile($match);
			}
			
		}
	}
?>








