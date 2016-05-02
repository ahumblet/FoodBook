<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
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
	
	if (isset($_POST["Like"])) {
		$interactiveId = $_POST["interactiveId"];
		$likingUser = $_POST["likingUser"];
		$likedUser = $_POST["likedUser"];
		$wallUser = $likedUser;
		$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp, value) VALUE ('%s', '%s', now(), 'like')", $interactiveId, $likingUser);
		$result = $mysqli->query($query);
	
	} elseif (isset($_POST["Dislike"])) {
		$interactiveId = $_POST["interactiveId"];
		$dislikingUser = $_POST["likingUser"];
		$dislikedUser = $_POST["likedUser"];
		$wallUser = $dislikedUser;
		$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp, value) VALUE ('%s', '%s', now(), 'dislike')", $interactiveId, $dislikingUser);
		$result = $mysqli->query($query);
		
	} elseif (isset($_POST["Comment"])) {
		$interactiveId = $_POST["interactiveId"];
		$likingUser = $_POST["likingUser"];
		$likedUser = $_POST["likedUser"];
		$wallUser = $likedUser;
		printf('<form action="likeOrComment.php" method="post" id="like">');
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
		printf('<input type="hidden" name="poster" value="%s">', $likingUser);
		printf('<input type="hidden" name="postee" value="%s">', $likedUser);
		printf('<input type="hidden" value="%s" name="interactiveId">', $interactiveId);
		printf('<button type="submit" value="Comment" name="PostComment">POST</button>');
		printf('</form>');
		
	} elseif (isset($_POST["PostComment"])) {
		$poster = $_POST["poster"];
		$postee = $_POST["postee"];
		$wallUser = $postee;
		$interactiveId = $_POST["interactiveId"];
		$textContent = $_POST["content"];
		$locationName = $_POST["location"];
		//find associated location ID to use in new post query
		$locationId = 'NULL';
		$query = sprintf("SELECT * from location where locName = '%s'", $locationName);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$locationId = $row["interactiveID"];
		}
		//slopping way of dealing with the fact that NULL can't have quotes around it
		if ($locationId = 'NULL') {
			$query = sprintf("insert into comment (interactiveID, postingUser, commentedThing, textContent, mediaContent, visibility, location, timestamp) values ('', '%s', '%s', '%s', '',  'everyone', NULL, now())", $poster, $interactiveId, $textContent);
		} else {
			$query = sprintf("insert into comment (interactiveID, postingUser, commentedThing, textContent, mediaContent, visibility, location, timestamp) values ('', '%s', '%s', '%s', '',  'everyone', '%s', now())", $poster, $interactiveId, $textContent, $locationId);
		}
		printf("query = %s<br>", $query);
		$mysqli->query($query);
		
	} else {
		echo "neither like nor comment...?";
	}
	
	//go back to the wall
	$headerString = sprintf("Location: wall.php?username=%s", $wallUser);
	header($headerString);
	exit;
?>





