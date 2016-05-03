<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	$wallUsername = $_GET["username"];
	
	echo "<link rel='stylesheet' href='login.css'>";
	
	include_once 'externalFunctions.php';
	startMysqli();
	
	printf("%s's wall: <br><br>", $wallUsername);
	newPostForm();
	displayWall();

	function displayWall() {
		global $mysqli, $loggedInUser, $wallUsername;
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
					$returnFile = "wall.php";
					displayPostWithButtons($post, $returnFile);
					displayLikesAndDislikes($post);
					displayComments($post, $returnFile);
				}
			}
		}
	}
?>








