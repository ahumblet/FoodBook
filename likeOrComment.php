<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	if (isset($_POST["Like"])) {
		$interactiveId = $_POST["interactiveId"];
		$likingUser = $_POST["likingUser"];
		$likedUser = $_POST["likedUser"];
		$wallUser = $likedUser;
		$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp, value) VALUE ('%s', '%s', now(), 'like')", $interactiveId, $likingUser);
		$mysqli->query($query);
	
	} elseif (isset($_POST["Dislike"])) {
		$interactiveId = $_POST["interactiveId"];
		$dislikingUser = $_POST["likingUser"];
		$dislikedUser = $_POST["likedUser"];
		$wallUser = $dislikedUser;
		$query = sprintf("insert into interactiveLike (likedInteractiveID, likingUser, timestamp, value) VALUE ('%s', '%s', now(), 'dislike')", $interactiveId, $dislikingUser);
		$mysqli->query($query);
		
	} elseif (isset($_POST["Comment"])) {
		$interactiveId = $_POST["interactiveId"];
		$likingUser = $_POST["likingUser"];
		$likedUser = $_POST["likedUser"];
		$returnFile = $_POST["returnFile"];
		$wallUser = $likedUser;
	
		//create array of locations
		$query = sprintf("select * from location");
		$locationResults = $mysqli->query($query);
		$locations = array();
		if ($locationResults->num_rows > 0) {
			while ($location = $locationResults->fetch_assoc()) {
				array_push($locations, $location["locName"]);
			}
		}
		
		//create array of visibilities
		$query = sprintf("select * from visibility");
		$visibilityResults = $mysqli->query($query);
		$visibilities = array();
		if ($locationResults->num_rows > 0) {
			while ($visibilityRow = $visibilityResults->fetch_assoc()) {
				array_push($visibilities, $visibilityRow["level"]);
			}
		}
		
		generateHTMLTop('feed');
		displayCommentForm();
		generateHTMLBottom();

		
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
		//sloppy way of dealing with the fact that NULL can't have quotes around it
		if ($locationId == 'NULL') {
			$query = sprintf("insert into comment (interactiveID, postingUser, commentedThing, textContent, mediaContent, visibility, location, timestamp) values ('', '%s', '%s', '%s', '',  'everyone', NULL, now())", $poster, $interactiveId, $textContent);
		} else {
			$query = sprintf("insert into comment (interactiveID, postingUser, commentedThing, textContent, mediaContent, visibility, location, timestamp) values ('', '%s', '%s', '%s', '',  'everyone', '%s', now())", $poster, $interactiveId, $textContent, $locationId);
		}
		$mysqli->query($query);
	} else {
		echo "neither like nor comment...?";
	}
	
	//go back to the wall or feed
	$returnFile = $_POST["returnFile"];
	$headerString = sprintf("Location: %s?username=%s", $returnFile, $wallUser);
	header($headerString);
	exit;

	function displayCommentForm() {
		global $locations, $visibilities, $likingUser, $likedUser, $returnFile, $interactiveId;
		
		//form to create a comment
		printf('<form action="likeOrComment.php" method="post" id="like">');
		printf('Content: <textarea name="content" style="width:250px;height:50px;"></textarea><br>');
		printf('Photo: <input type="file" name="photo"> <br>');
		//location drop down
		printf('Location: <select name="location">');
		printf('<option value=""></option>');
		foreach ($locations as &$location) {
			printf('<option value="%s">%s</option>', $location, $location);
		}
		printf('</select><br>');
		
		//drop down for visibility
		printf('Visibility: <select name="visibility">');
		foreach ($visibilities as &$visibility) {
			printf('<option value="%s">%s</option>', $visibility, $visibility);
		}
		printf('</select><br>');
		printf('<input type="hidden" name="poster" value="%s">', $likingUser);
		printf('<input type="hidden" name="postee" value="%s">', $likedUser);
		printf('<input type="hidden" name="returnFile" value="%s">', $returnFile);
		printf('<input type="hidden" value="%s" name="interactiveId">', $interactiveId);
		printf('<button type="submit" value="Comment" name="PostComment">POST</button>');
		printf('</form>');
	}
?>








