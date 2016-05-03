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
	printf("%s's wall: <br><br>", $wallUsername);
	
	newPostForm();
	displayWall();

	function newPostForm() {
		//form to create a new post
		global $mysqli, $loggedInUser, $wallUsername;
		printf("Write on %s's wall: <br>", $wallUsername);
		printf('<form action="addPost.php" method="post">');
		printf('Title: <input type="text" name="title"><br>');
		printf('Content: <textarea name="content" style="width:250px;height:50px;"></textarea><br>');
		printf('Photo: <input type="file" name="photo"> <br>');
		$query = sprintf("select * from location");
		//drop down for locations
		$locationResults = $mysqli->query($query);
		printf('Location: <select name="location">');
		printf('<option value=""></option>');
		if ($locationResults->num_rows > 0) {
			while ($location = $locationResults->fetch_assoc()) {
				printf('<option value="%s">%s</option>', $location["locName"], $location["locName"]);
			}
		}
		printf('</select><br>');
		//drop down for visibility
		$query = sprintf("select * from visibility");
		$visibilityResults = $mysqli->query($query);
		printf('Visibility: <select name="visibility">');
		if ($visibilityResults->num_rows > 0) {
			while ($visibilityRow = $visibilityResults->fetch_assoc()) {
				printf('<option value="%s">%s</option>', $visibilityRow["level"], $visibilityRow["level"]);
			}
		}
		printf('</select><br>');
		printf('<input type="hidden" name="poster" value="%s">', $loggedInUser);
		printf('<input type="hidden" name="postee" value="%s">', $wallUsername);
		printf('<input type="submit" value="POST"> <br>');
		printf('</form><br>');
	}
	
	function displayWall() {
		global $mysqli, $loggedInUser, $wallUsername;
		//query all posts on this user's wall
		$query = sprintf('select * from post where receivingUser = "%s" order by timestamp desc', $wallUsername);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			while ($post = $result->fetch_assoc()) {
				$visibility = $post["visibility"];
				$postingUser = $post["postingUser"];
				$permission = hasPermission($loggedInUser, $postingUser, $visibility);
				if ($permission == True) {
					printf("<br><br>%s %s says:<br>", $post["timestamp"], $post["postingUser"]);
					printf("%s<br>", $post["title"]);
					printf("%s<br>", $post["textContent"]);
					//lookup location name if there is one
					if ($post["location"] != '') {
						$query = sprintf("select * from location where interactiveID = '%s'", $post["location"]);
						$locationResult = $mysqli->query($query);
						$locationRow = $locationResult->fetch_assoc();
						printf("Location: %s<br>", $locationRow["locName"]);
					}
					printf('<form action="likeOrComment.php" method="post" id="like">');
					printf('<input type="hidden" value="%s" name="likingUser">', $loggedInUser);
					printf('<input type="hidden" value="%s" name="likedUser">', $wallUsername);
					printf('<input type="hidden" value="%s" name="interactiveId">', $post["interactiveID"]);
					printf('<button type="submit" value="Like" name="Like">Like</button>');
					printf('<button type="submit" value="Dislike" name="Dislike">Dislike</button>');
					printf('<button type="submit" value="Comment" name="Comment">Comment</button>');
					printf('</form>');
					
					displayLikesAndDislikes($post);
					displayComments($post, $loggedInUser, $wallUsername);
				}
			}
		}
	}
	
	function displayLikesAndDislikes($item)
	{
		global $mysqli;
		//show likes for this post
		$query = sprintf("select * from interactiveLike where likedInteractiveID = '%s' and value = 'like'", $item["interactiveID"]);
		$likeResults = $mysqli->query($query);
		if ($likeResults->num_rows > 0 ) {
			printf('liked by: ');
			while ($like = $likeResults->fetch_assoc()) {
				printf('%s, ', $like["likingUser"]);
			}
			printf("<br>");
		}
		//show dislikes for this post
		$query = sprintf("select * from interactiveLike where likedInteractiveID = '%s' and value = 'dislike'", $item["interactiveID"]);
		$dislikeResults = $mysqli->query($query);
		if ($dislikeResults->num_rows > 0 ) {
			printf('disliked by: ');
			while ($dislike = $dislikeResults->fetch_assoc()) {
				printf('%s, ', $dislike["likingUser"]);
			}
			printf("<br>");
		}
	}
	
	function displayComments($row, $loggedInUser, $wallUsername) {
		global $mysqli;
		//show comments for this object (comment or post)
		$query = sprintf("select * from comment where commentedThing = '%s'", $row["interactiveID"]);
		$commentResults = $mysqli->query($query);
		while ($comment = $commentResults->fetch_assoc()) {
			$visibility = $comment["visibility"];
			$postingUser = $comment["postingUser"];
			$permission = hasPermission($loggedInUser, $postingUser, $visibility);
			//$mysqli->kill();
			//$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
			if ($permission == True) {
				//display the comment:
				printf("<br>&nbsp&nbsp&nbsp%s %s:&nbsp&nbsp&nbsp%s<br>", $comment["timestamp"], $comment["postingUser"], $comment["textContent"]);
				
				//print location if there is one, requires looking up the location name
				if ($comment["location"] != '') {
					$query = sprintf("select * from location where interactiveID = '%s'", $comment["location"]);
					$locationResult = $mysqli->query($query);
					$locationRow = $locationResult->fetch_assoc();
					printf("Location: %s<br>", $locationRow["locName"]);
				}
				
				//display like, dislike, comment buttons:
				$commentId = $comment["interactiveID"];
				printf('<form action="likeOrComment.php" method="post" id="like">');
				printf('<input type="hidden" value="%s" name="likingUser">', $loggedInUser);
				printf('<input type="hidden" value="%s" name="likedUser">', $wallUsername);
				printf('<input type="hidden" value="%s" name="interactiveId">', $commentId);
				printf('<button type="submit" value="Like" name="Like">Like</button>');
				printf('<button type="submit" value="Dislike" name="Dislike">Dislike</button>');
				printf('<button type="submit" value="Comment" name="Comment">Comment</button>');
				printf('</form>');
				//display likes, dislikes
				displayLikesAndDislikes($mysqli, $comment);
				displayComments($comment, $loggedInUser, $wallUsername);
			}
		}
	}
?>








