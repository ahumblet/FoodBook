<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	$wallUsername = $_GET["username"];
	
	generateHTMLTop('feed');
	displayFeed();
	generateHTMLBottom();
	
	//================== Function Definitions =====================//
	
	
	function displayFeed() {
		global $mysqli, $loggedInUser;
		
		printf("<div class='pageHeader'>%s's feed </div>", $loggedInUser);
		
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
					//displayLikesAndDislikes($post);
					displayComments($post, $returnFile);
				}
			}
		}
	}
	
?>








