<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	startMysqli();
	
	$requestedFriend = $_POST["requestedFriend"];
	$query = sprintf("INSERT INTO friendship (username1, username2) VALUES ('%s', '%s')", $loggedInUser, $requestedFriend);
	
	$result = $mysqli->query($query);
	
	header("Location: friends.php");
	exit;
	
?>
	