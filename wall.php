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
			
			printf('<form action="likeOrComment.php" method="post" id="like">');
			printf('<input type="hidden" value="%s" name="likingUser">', $loggedInUser);
			printf('<input type="hidden" value="%s" name="likedUser">', $wallUsername);
			printf('<input type="hidden" value="%s" name="interactiveId">', $row["interactiveID"]);
			printf('<button type="submit" value="Like" name="Like">Like</button>');
			printf('<button type="submit" value="Comment" name="Comment">Comment</button>');
			printf('</form>');
			
			$query = sprintf("select * from interactiveLike where likedInteractiveID = '%s'", $row["interactiveID"]);
			$likeResults = $mysqli->query($query);
			while ($like = $likeResults->fetch_assoc()) {
				printf('liked by %s <br>', $like["likingUser"]);
			}
			
		
		}
	}
	
	//to display 
	
?>








