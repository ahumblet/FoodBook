<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];

	echo "<link rel='stylesheet' href='login.css'>";
	
	include_once 'externalFunctions.php';
	
	startMysqli();
	
	$wallUsername = $_GET["username"];
	printf("%s's feed: <br><br>", $loggedInUser);
	
	$query = sprintf("select * from post");
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		while ($post = $result->fetch_assoc()) {
			$postingUser = $post["postingUser"];
			$visibility = $post["visibility"];
			$permission = hasPermission($loggedInUser , $postingUser, $visibility);
			if ($permission == True) {
				$returnFile = "feed.php";
				displayPostWithButtons($post, $returnFile);
				displayLikesAndDislikes($post);
				displayComments($post, $returnFile);
			}
		}
	}
	
?>








