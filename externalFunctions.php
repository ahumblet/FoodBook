<?php
	//establish connection and global variables
	$dbUser = 'root';
	$dbPassword = 'root';
	$db = 'Nutrition';
	$dbHost = 'localhost';
	$dbPort = 3306;
	$mysqli;
	$urlRoot = "http://localhost:8888/finalProject";
	
	function startMysqli() {
		global $dbUser, $dbPassword, $db, $dbHost, $dbPort, $mysqli;
		$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $db);
	}
	
	function restartMysqli() {
		global $dbUser, $dbPassword, $db, $dbHost, $dbPort, $mysqli;
		$mysqli->kill();
		$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $db);
	}
	
	function checkLoggedIn() {
		global $loggedInUser;
		if ($loggedInUser == '') {
			$headerString = sprintf("Location: login.php");
			header($headerString);
			exit;
		}
	}
	
	
	//is user1 allowed to see user2's content based on this visibility?
	function hasPermission($user1, $user2, $level)
	{
		global $mysqli;
		
		restartMysqli();
		if (($user1 == $user2) || ($level == 'everyone')) {
			return TRUE;
		}
		if ($level == "FOFs") {
			$query = sprintf("CALL isFOFsOf('%s', '%s');", $user1, $user2);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return TRUE;
			}
		} elseif ($level == "friends") {
			$query = sprintf("CALL isFriendsOf('%s', '%s')", $user1, $user2);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return TRUE;
			}
		} elseif ($level == "nutritionists") {
			$query = sprintf("select * from user where type = 'nutritionist' and username = '%s'", $user1);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return TRUE;
			}
		} elseif ($level == "me") {
			if ($user1 == $user2) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	function newPostForm($returnFile) {
		//form to create a new post
		global $mysqli, $loggedInUser, $wallUsername;
		
		restartMysqli();
		
		printf("<div class='post'>");
		printf("<div class='postHeader'>Write on %s's wall: </div>", $wallUsername);
		printf('<form action="addPost.php" method="post">');
		printf('Title: <input type="text" name="title"><br>');

		printf('Content: <input type="textarea" name="content"><br>');
		printf('Embed Photo: <input type="text" name="photo"> <br>');
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
		printf('<input type="hidden" name="returnFile" value="%s">', $returnFile);
		printf('<input type="submit" value="POST">');
		printf('</form>');
		printf("</div>");
	}
	
	function displayPostWithButtons($post, $returnFile) {
		global $mysqli, $loggedInUser, $wallUsername;
		
		//not sure this is needed
		restartMysqli();
		
		printf("<div class='post'>");
		printf("<div class='postHeader'>%s %s says:<br></div>", $post["timestamp"], $post["postingUser"]);
		printf("%s<br>", $post["title"]);
		printf("%s<br>", $post["textContent"]);
		//display embedded photo based on url
		if ($post["mediaContent"] != "") {
			printf('<img src="%s" alt="alternative text" width="200" height="200"><br>', $post["mediaContent"]);
		}
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
		printf('<input type="hidden" value="%s" name="returnFile">', $returnFile);
		printf('<input type="submit" value="Like" name="Like">');
		printf('<input type="submit" value="Dislike" name="Dislike">');
		printf('<input type="submit" value="Comment" name="Comment">');
		printf('</form>');
		displayLikesAndDislikes($post);
		printf("</div>");
	}
	
	function displayPostOnly($post) {
		global $mysqli, $loggedInUser, $wallUsername;
		
		printf("<div class='post'>");
		printf("<div class='postHeader'>%s %s says:<br></div>", $post["timestamp"], $post["postingUser"]);
		printf("%s<br>", $post["title"]);
		printf("%s<br>", $post["textContent"]);
		//display embedded photo based on url
		if ($post["mediaContent"] != "") {
			printf('<img src="%s" alt="alternative text" width="200" height="200"><br>', $post["mediaContent"]);
		}
		//lookup location name if there is one
		if ($post["location"] != '') {
			$query = sprintf("select * from location where interactiveID = '%s'", $post["location"]);
			$locationResult = $mysqli->query($query);
			$locationRow = $locationResult->fetch_assoc();
			printf("Location: %s<br>", $locationRow["locName"]);
		}
		printf("</div>");
	}
	
	
	function displayLikesAndDislikes($item) {
		global $mysqli;
		
		//not sure this is needed
		restartMysqli();
		
		//show likes for this post
		printf('<div class="likeSection">');
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
		printf("</div>");
	}
	
	function displayCommentWithButtons($comment, $returnFile) {
		global $mysqli, $loggedInUser, $wallUsername;
		
		//not sure this is needed
		restartMysqli();
		
		printf("<div class='postHeader'> %s %s:</div>",$comment["timestamp"], $comment["postingUser"]);
		printf("%s<br>", $comment["textContent"]);
		//display embedded photo based on url
		if ($comment["mediaContent"] != "") {
			printf('<img src="%s" alt="alternative text" width="200" height="200"><br>', $comment["mediaContent"]);
		}
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
		printf('<input type="hidden" value="%s" name="returnFile">', $returnFile);
		printf('<input type="submit" value="Like" name="Like">');
		printf('<input type="submit" value="Dislike" name="Dislike">');
		printf('<input type="submit" value="Comment" name="Comment">');
		printf('</form>');
	}
	
	function displayCommentOnly($comment) {
		global $mysqli, $loggedInUser, $wallUsername;
		printf('<div class="comment">');
		printf("<div class='postHeader'> %s %s:</div>",$comment["timestamp"], $comment["postingUser"]);
		printf("%s<br>", $comment["textContent"]);
		//display embedded photo based on url
		if ($comment["mediaContent"] != "") {
			printf('<img src="%s" alt="alternative text" width="200" height="200"><br>', $comment["mediaContent"]);
		}
		//print location if there is one, requires looking up the location name
		if ($comment["location"] != '') {
			$query = sprintf("select * from location where interactiveID = '%s'", $comment["location"]);
			$locationResult = $mysqli->query($query);
			$locationRow = $locationResult->fetch_assoc();
			printf("Location: %s<br>", $locationRow["locName"]);
		}
		printf("</div><br>");
	}
	
	function displayComments($item, $returnFile) {
		global $mysqli, $loggedInUser, $wallUsername;
		
		//show comments for this object (comment or post)
		restartMysqli();
		
		$query = sprintf("select * from comment where commentedThing = '%s'", $item["interactiveID"]);
		$commentResults = $mysqli->query($query);
		while ($comment = $commentResults->fetch_assoc()) {
			$visibility = $comment["visibility"];
			$postingUser = $comment["postingUser"];
			$permission = hasPermission($loggedInUser, $postingUser, $visibility);
			if ($permission == TRUE) {
				//display the comment:
				printf('<div class="comment">');
				displayCommentWithButtons($comment, $returnFile);
				//display likes, dislikes
				displayLikesAndDislikes($comment);
				displayComments($comment, $returnFile);
				printf("</div>");
			}
		}
	}
	
	function displayLimitedProfile($jointEntry) {
		printf('<div class="post">');
		printf("<div class='subHeader'>%s's profile</div>", $jointEntry["username"]);
		printf("First Name: %s <br>", $jointEntry["firstName"]);
		printf("Last Name: %s <br>", $jointEntry["lastName"]);
		printf("Age: %s <br>", $jointEntry["age"]);
		printf("Email: %s <br>", $jointEntry["email"]);
		printf("Type: %s <br>", $jointEntry["type"]);		
		printf('</div>');
	}
	
	function generateHTMLTop($activeLink) {
		global $urlRoot, $loggedInUser;
		
echo <<< EOT
		<html>
		<head>
		<link rel="stylesheet" href="foodbook.css">
		<style type="text/css">a {text-decoration: none}</style>
		</head>
		<body>
		<div class="bg"></div>
		<div class="container">
		<nav>
		<div class="search-box">
		
		
		<form action="search.php" method="post">
		<div><i class="fa fa-search"></i>
		<input type="text" name="searchTerm" placeholder="Search"/>
		<input type="submit" id="invisibleSubmit"/>
		</div>
		</form>
		</div>
		<ul class="menu">
EOT;
	
	if ($activeLink == 'profile') {
		printf('<a href="%s/profile.php?username=%s"> <li class="active"><i class="fa fa-user"></i>Profile</li></a>', $urlRoot, $loggedInUser);
	} else {
		printf('<a href="%s/profile.php?username=%s"> <li><i class="fa fa-user"></i>Profile</li> </a>', $urlRoot, $loggedInUser);
	}
	if ($activeLink == 'wall') {
		printf('<a href="%s/wall.php?username=%s"> <li class="active"><i class="fa fa-home"></i>Wall</li> </a>', $urlRoot, $loggedInUser);
	} else {
		printf('<a href="%s/wall.php?username=%s"> <li><i class="fa fa-home"></i>Wall</li> </a>', $urlRoot, $loggedInUser);
	}
	if ($activeLink == 'feed') {
		printf('<a href="%s/feed.php"> <li class="active"><i class="fa fa-home"></i>Feed</li> </a>', $urlRoot);
	} else {
		printf('<a href="%s/feed.php"> <li><i class="fa fa-home"></i>Feed</li> </a>', $urlRoot);
	}
	if ($activeLink == 'friends') {
		printf('<a href="%s/friends.php"> <li class="active"><i class="fa fa-group"></i>Friends</li> </a>', $urlRoot, $loggedInUser);
	} else {
		printf('<a href="%s/friends.php"> <li><i class="fa fa-group"></i>Friends</li> </a>', $urlRoot, $loggedInUser);
	}
	if ($activeLink == 'locations') {
		printf('<a href="%s/locations.php"> <li class="active"><i class="fa fa-envelope"></i>Locations</li> </a>', $urlRoot);
	} else {
		printf('<a href="%s/locations.php"> <li><i class="fa fa-envelope"></i>Locations</li> </a>', $urlRoot);
	}
	printf('<a href="%s/logoff.php"> <li><i class="fa fa-cog"></i>Log Out</li> </a>', $urlRoot);
		
echo <<< EOT
		</ul>
		</nav>
		<div class="header">
		<div class="userPanel">
		<div class="pic"></div>
EOT;
		
		printf('<div class="welcome">Welcome %s!</div></div>', $loggedInUser);
		printf('<div class="title"></div>');
		printf('<div class="filler">');
	}
	
	function generateHTMLBottom() {
		printf('</div>');
		printf('</div>');
		printf('</div>');
		printf('</body>');
		printf('</html>');
	}
	
?>