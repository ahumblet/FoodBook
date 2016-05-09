<?php
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	$profileUsername = $_GET["username"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	generateHTMLTop('profile');
	generateProfileHTML();
	generateHTMLBottom();
	
//===============Function definitions==============//
	
	function generateProfileHTML() {
		global $profileUsername, $mysqli;
		
		printf("<div class='pageHeader'>%s's stats</div>", $profileUsername);
		
		//number of friends
		$query = sprintf("SELECT count(A.username2) as numFriends FROM FRIENDSHIP as A JOIN FRIENDSHIP as B WHERE A.username1 = B.username2 AND A.username2 = B.username1 AND A.username1 = '%s' GROUP BY A.username1", $profileUsername);
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();
		$numFriends = $row["numFriends"];
		printf("<div class='subHeader'> %s has %d friends</div><br>", $profileUsername, $numFriends);
		
		//most liked post
		$query = sprintf("select count(likingUser) as numLikes, interactiveID, postingUser, receivingUser, receivingUser, textContent, mediaContent, visibility, post.timestamp from post join interactiveLike where postingUser = '%s' and interactiveID = likedInteractiveID and value = 'like' group by interactiveID order by count(likingUser) desc limit 1", $profileUsername);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			$mostLikedPost = $result->fetch_assoc();
			printf("<div class='subHeader'> %s's most liked post has %s likes</div>", $profileUsername, $numFriends, $mostLikedPost['numLikes']);
			displayPostOnly($mostLikedPost);
			printf("<br>");
		} else {
			printf("<div class='subHeader'> %s has no liked posts</div>", $profileUsername);
		}
		
		//most disliked post
		$query = sprintf("select count(likingUser) as numLikes, interactiveID, postingUser, receivingUser, receivingUser, textContent, mediaContent, visibility, post.timestamp from post join interactiveLike where postingUser = '%s' and interactiveID = likedInteractiveID and value = 'dislike' group by interactiveID order by count(likingUser) desc limit 1", $profileUsername);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			$mostDislikedPost = $result->fetch_assoc();
			printf("<div class='subHeader'> %s's most disliked post has %s dislikes</div>", $profileUsername, $numFriends, $mostDislikedPost['numLikes']);
			displayPostOnly($mostDislikedPost);
			printf("<br>");
		} else {
			printf("<div class='subHeader'> %s has no disliked posts</div>", $profileUsername);
		}

		//most liked comment
		$query = sprintf("select count(likingUser), interactiveID, postingUser, textContent, mediaContent, location, comment.timestamp from comment join interactiveLike where interactiveID = likedInteractiveID and value = 'like' and postingUser = '%s'group by interactiveID order by count(likingUser) desc limit 1", $profileUsername);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			$mostLikedComment = $result->fetch_assoc();
			printf("<div class='subHeader'> %s's most liked comment has %s likes</div>", $profileUsername, $numFriends, $mostLikedComment['numLikes']);
			displayPostOnly($mostLikedComment);
			printf("<br>");
		} else {
			printf("<div class='subHeader'> %s has no liked comments</div>", $profileUsername);
		}
		
		//most disliked comment
		$query = sprintf("select count(likingUser), interactiveID, postingUser, textContent, mediaContent, location, comment.timestamp from comment join interactiveLike where interactiveID = likedInteractiveID and value = 'dislike' and postingUser = '%s'group by interactiveID order by count(likingUser) desc limit 1", $profileUsername);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			$mostDislikedComment = $result->fetch_assoc();
			printf("<div class='subHeader'> %s's most disliked comment has %s dislikes</div>", $profileUsername, $numFriends, $mostDislikedComment['numLikes']);
			displayPostOnly($mostDislikedComment);
		} else {
			printf("<div class='subHeader'> %s has no disliked comments</div>", $profileUsername);
		}
	}
?>