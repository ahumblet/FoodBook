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
	
	$wallUsername = $_GET["username"];
	printf("%s's wall: <br><br>", $wallUsername);
	
	
	//form to create a new post
	printf("Write on %s's wall: <br>", $wallUsername);
	printf('<form action="addPost.php" method="post">');
	printf('Title: <input type="text" name="title"><br>');
	printf('Content: <textarea name="content" style="width:250px;height:50px;"></textarea><br>');
	printf('Photo: <input type="file" name="photo"> <br>');
	$query = sprintf("select * from location");
	$locationResults = $mysqli->query($query);
	printf('Location: <select name="location">');
	printf('<option value=""></option>');
	if ($locationResults->num_rows > 0) {
		while ($location = $locationResults->fetch_assoc()) {
			printf('<option value="%s">%s</option>', $location["locName"], $location["locName"]);
		}
	}
	printf('</select><br>');
	printf('<input type="hidden" name="poster" value="%s">', $loggedInUser);
	printf('<input type="hidden" name="postee" value="%s">', $wallUsername);
	printf('<input type="submit" value="POST"> <br>');
	printf('</form><br>');

	//print all comments on this user's wall
	$query = sprintf('select * from post where receivingUser = "%s" order by timestamp desc', $wallUsername);
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			printf("<br><br>%s %s says:<br>", $row["timestamp"], $row["postingUser"]);
			printf("%s<br>", $row["title"]);
			printf("%s<br>", $row["textContent"]);
			//lookup location name if there is one
			if ($row["location"] != '') {
				$query = sprintf("select * from location where interactiveID = '%s'", $row["location"]);
				$locationResult = $mysqli->query($query);
				$locationRow = $locationResult->fetch_assoc();
				printf("Location: %s<br>", $locationRow["locName"]);
			}
			printf('<form action="likeOrComment.php" method="post" id="like">');
			printf('<input type="hidden" value="%s" name="likingUser">', $loggedInUser);
			printf('<input type="hidden" value="%s" name="likedUser">', $wallUsername);
			printf('<input type="hidden" value="%s" name="interactiveId">', $row["interactiveID"]);
			printf('<button type="submit" value="Like" name="Like">Like</button>');
			printf('<button type="submit" value="Dislike" name="Dislike">Dislike</button>');
			printf('<button type="submit" value="Comment" name="Comment">Comment</button>');
			printf('</form>');
			
			displayLikesAndDislikes($mysqli, $row);
			displayComments($mysqli, $row, $loggedInUser, $wallUsername);
		}
	}

	
	function displayLikesAndDislikes($mysqli, $row)
	{
		//show likes for this post
		$query = sprintf("select * from interactiveLike where likedInteractiveID = '%s' and value = 'like'", $row["interactiveID"]);
		$likeResults = $mysqli->query($query);
		if ($likeResults->num_rows > 0 ) {
			printf('liked by: ');
			while ($like = $likeResults->fetch_assoc()) {
				printf('%s, ', $like["likingUser"]);
			}
			printf("<br>");
		}
		//show dislikes for this post
		$query = sprintf("select * from interactiveLike where likedInteractiveID = '%s' and value = 'dislike'", $row["interactiveID"]);
		$dislikeResults = $mysqli->query($query);
		if ($dislikeResults->num_rows > 0 ) {
			printf('disliked by: ');
			while ($dislike = $dislikeResults->fetch_assoc()) {
				printf('%s, ', $dislike["likingUser"]);
			}
			printf("<br>");
		}
	}
	
	function displayComments($mysqli, $row, $loggedInUser, $wallUsername) {
		//show comments for this object (comment or post)
		$query = sprintf("select * from comment where commentedThing = '%s'", $row["interactiveID"]);
		$commentResults = $mysqli->query($query);
		while ($comment = $commentResults->fetch_assoc()) {
			//display the comment:
			printf("<br>&nbsp&nbsp&nbsp%s %s:&nbsp&nbsp&nbsp%s<br>", $comment["timestamp"], $comment["postingUser"], $comment["textContent"]);
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
			displayComments($mysqli, $comment, $loggedInUser, $wallUsername);
		}
	}
	
?>








