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
		$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp) VALUE ('%s', '%s', now())", $interactiveId, $likingUser);
		$result = $mysqli->query($query);
	} elseif (isset($_POST["Comment"])) {
		$interactiveId = $_POST["interactiveId"];
		$likingUser = $_POST["likingUser"];
		$likedUser = $_POST["likedUser"];
		$wallUser = $likedUser;
		printf('<form action="likeOrComment.php" method="post" id="like">');
		printf('Content: <textarea name="content" style="width:250px;height:50px;"></textarea><br>');
		printf('Photo: <input type="file" name="photo"> <br>');
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
		$query = sprintf("insert into comment (interactiveID, postingUser, commentedThing, textContent, mediaContent, visibility, location, timestamp) values ('', '%s', '%s', '%s', '',  'everyone', '', now())", $poster, $interactiveId, $textContent);
		//echo $query; //the commented thing id is not being passed in
		$mysqli->query($query);
	} else {
		echo "neither like nor comment...?";
	}
	
	//go back to the wall
	$headerString = sprintf("Location: wall.php?username=%s", $wallUser);
	header($headerString);
	exit;
?>





