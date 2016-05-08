<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	$wallUsername = $_GET["username"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	generateHTMLTop('wall');
	displayWall();
	generateHTMLBottom();

	
	//================== Function Definitions =====================//
	
	
	function displayWall() {
		global $mysqli, $loggedInUser, $wallUsername;
		
		printf("<div class='pageHeader'>%s's wall </div>", $wallUsername);
		
		$returnFile = "wall.php";
		newPostForm($returnFile);
		
		//query all posts on this user's wall
		$query = sprintf('select * from post where receivingUser = "%s" order by timestamp desc', $wallUsername);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			while ($post = $result->fetch_assoc()) {
				//check visibility of this post
				$visibility = $post["visibility"];
				$postingUser = $post["postingUser"];
				$permission = hasPermission($loggedInUser, $postingUser, $visibility);
				if ($permission == True) {
					displayPostWithButtons($post, $returnFile);
					//displayLikesAndDislikes($post);
					displayComments($post, $returnFile);
				}
			}
		}
	}
?>








