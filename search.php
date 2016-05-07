<?php
	session_start();
	include_once 'externalFunctions.php';
	startMysqli();
	
	$loggedInUser = $_SESSION["loggedInUser"];
	$wallUsername = $_GET["username"];
	$searchTerm = $_POST["searchTerm"];

	//perform all search queries and collect results
	
	//I should check for empty search field.
	
	
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
			array_push($profileMatches, $match);
		}
	}
	
	generateHTMLTop('search');
	displayResults();
	generateHTMLBottom();
	
	
	function displayResults() {
		global $loggedInUser, $searchTerm, $locationMatches, $postMatches, $commentMatches, $profileMatches;
		
		printf("search results for: %s <br>", $searchTerm);

		//show locations:
		if (sizeof($locationMatches) > 0) {
			printf("<br>%d location matches:<br>", sizeof($locationMatches));
			foreach ($locationMatches as &$location) {
				printf("%s: %s, %s<br>", $location["locName"], $location["longitude"], $location["latitude"]);
			}
		}
		
		//show posts
		if (sizeof($postMatches) > 0) {
			printf("<br>%d post matches:<br>", sizeof($postMatches));
			foreach ($postMatches as &$match) {
				displayPostOnly($match);
			}
		}
		
		//show comments
		if (sizeof($commentMatches) > 0) {
			printf("<br>%d comment matches:<br>", sizeof($commentMatches));
			foreach ($commentMatches as &$match) {
				displayCommentOnly($match);
			}
		}
	}
	
	
	
	
	/*I'm going to change this to:
		-- perform all queries and collect items in lists
		-- create a local displayResults function
		-- start naive then add nice css to results
		-- have top and bottom html
		-- I need to check visibility
	 */
	
	
	//search users
	/*$query = sprintf("SELECT * FROM user WHERE username LIKE '%%%s%%'", $searchTerm);
	 $result = $mysqli->query($query);
	 
	 
	 if ($result->num_rows > 0) {
		printf("<br>found %s user matches: <br>", $result->num_rows);
		while ($userMatch = $result->fetch_assoc()) {
	 printf("%s<br>", $userMatch["username"]); //sould be a link eventually
		}
	 }*/
	
	?>








