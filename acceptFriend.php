<?php
	//only php, no html
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	if (isset($_POST["acceptedFriend"])) {
		$acceptedFriend = $_POST["acceptedFriend"];
		$query = sprintf("INSERT INTO friendship (username1, username2) VALUES ('%s', '%s')", $loggedInUser, $acceptedFriend);
		$mysqli->query($query);
		
	} elseif (isset($_POST["rejectedFriend"])) {
		$rejectedFriend = $_POST["rejectedFriend"];
		$query = sprintf("delete from friendship where username1 = '%s' and username2 = '%s'", $rejectedFriend, $loggedInUser);
		$mysqli->query($query);
	}
	
	header("Location: friends.php");
	exit;	
?>
