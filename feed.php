<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	echo "<link rel='stylesheet' href='login.css'>";
	
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
	
	include_once 'externalFunctions.php';
	
	$wallUsername = $_GET["username"];
	printf("%s's feed: <br><br>", $loggedInUser);
	
	$query = printf("select * from post");
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		while ($post = $result->fetch_assoc()) {
			$postingUser = $post["postingUser"];
			$visibility = $post["visibility"];
			$permission = hasPermission($loggedInUser , $postingUser, $visibility);
			if ($permission == True) {
				
				
				$posteeUser = $post["receivingUser"];
				
				
			}
		}
	}
?>








