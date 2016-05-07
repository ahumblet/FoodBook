<?php
	session_start();
	include_once 'externalFunctions.php';
	startMysqli();
	
	$loggedInUser = $_SESSION["loggedInUser"];
	$wallUsername = $_GET["username"];

	$searchTerm = $_POST["searchTerm"];
	
	printf("search results for: %s <br>", $searchTerm);
	
	//search and display users
	$query = sprintf("SELECT * FROM user WHERE username LIKE '%%%s%%'", $searchTerm);
	$result = $mysqli->query($query);
	
	
	if ($result->num_rows > 0) {
		printf("<br>found %s user matches: <br>", $result->num_rows);
		while ($userMatch = $result->fetch_assoc()) {
			printf("%s<br>", $userMatch["username"]); //sould be a link eventually
		}
	}
					 
	//search and display locations
	$query = sprintf("SELECT * FROM LOCATION WHERE locName LIKE '%%%s%%' OR longitude LIKE '%%%s%%' OR latitude LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	
	if ($result->num_rows > 0) {
		printf("<br>found %s location matches: <br>", $result->num_rows);
		while ($userMatch = $result->fetch_assoc()) {
			printf("%s: %s, %s<br>", $userMatch["locName"], $userMatch["longitude"], $userMatch["latitude"]); //sould be a link eventually
		}
	}
	
	//search and display posts
	$query = sprintf("SELECT * FROM POST WHERE postingUser LIKE '%%%s%%' OR receivingUser LIKE '%%%s%%' OR title LIKE '%%%s%%' OR textContent LIKE '%%%s%%' OR location LIKE '%%%s%%' OR timestamp LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	
	if ($result->num_rows > 0) {
		printf("<br>found %s post matches: <br>", $result->num_rows);
		while ($match = $result->fetch_assoc()) {
			printf("%s to %s: %s<br>", $match["postingUser"], $match["receivingUser"], $match["textContent"]); 
		}
	}

	//search and display comments
	$query = sprintf("SELECT * FROM comment WHERE postingUser LIKE '%%%s%%' OR textContent LIKE '%%%s%%' OR location LIKE '%%%s%%' OR timestamp LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	
	if ($result->num_rows > 0) {
		printf("<br>found %s comment matches: <br>", $result->num_rows);
		while ($match = $result->fetch_assoc()) {
			printf("%s: %s<br>", $match["postingUser"], $match["textContent"]);
		}
	}

	//search and display profiles
	$query = sprintf("SELECT * FROM profile NATURAL JOIN user WHERE username LIKE '%%%s%%' OR firstName LIKE '%%%s%%' OR lastName LIKE '%%%s%%' OR age LIKE '%%%s%%' OR email LIKE '%%%s%%' OR type LIKE '%%%s%%'", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
	$result = $mysqli->query($query);
	
	if ($result->num_rows > 0) {
		printf("<br>found %s profile matches: <br>", $result->num_rows);
		while ($match = $result->fetch_assoc()) {
			printf("%s: %s, %s<br>", $match["username"], $match["firstName"], $match["type"]);
		}
	}	
?>








