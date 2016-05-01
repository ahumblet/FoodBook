<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	$user = 'root';
	$password = 'root';
	$db = 'Nutrition';
	$host = 'localhost';
	$port = 3306;
	
	$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	$acceptedFriend = $_POST["acceptedFriend"];
	
	$query = sprintf("INSERT INTO friendship (username1, username2) VALUES ('%s', '%s')", $loggedInUser, $acceptedFriend);
	
	$result = $mysqli->query($query);
	
	header("Location: friends.php");
	exit;
	
?>
