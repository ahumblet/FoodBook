<?php
	session_start();
	include_once 'externalFunctions.php';
	startMysqli();
	
	$loggedInUser = $_SESSION["loggedInUser"];
	$wallUsername = $_GET["username"];
	
	generateHTMLTop('feed');
	displayFeed();
	generateHTMLBottom();
	
	//================== Function Definitions =====================//
	
	
	function displayFeed() {
		global $mysqli, $loggedInUser;
		
		printf("%s's feed: <br><br>", $loggedInUser);
		
		$returnFile = "feed.php";
		$query = sprintf("select * from post");
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			while ($post = $result->fetch_assoc()) {
				$postingUser = $post["postingUser"];
				$visibility = $post["visibility"];
				$permission = hasPermission($loggedInUser , $postingUser, $visibility);
				if ($permission == True) {
					displayPostWithButtons($post, $returnFile);
					displayLikesAndDislikes($post);
					displayComments($post, $returnFile);
				}
			}
		}
	}
	
?>








