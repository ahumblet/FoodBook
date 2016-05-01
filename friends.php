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
	
	printf("Friends of %s<br>", $loggedInUser);
	
	//get friends
	$query = sprintf("select A.username2 as friend from friendship as A join friendship as B where A.username1 = B.username2 and A.username2 = B.username1 and A.username1 = '%s'", $loggedInUser);
	$friends = $mysqli->query($query);

	
	//print friends
	if ($friends->num_rows > 0) {
		while($row = $friends->fetch_assoc()) {
			$friend = $row["friend"];
			printf('<a href="http://localhost:8888/finalProject/profile.php?username=%s">%s</a><br>', $friend, $friend);
		}
	}
	
	
	//get pending requests
	printf("<br><br>Waiting for responses from:<br>");
	
	$query = sprintf("select username2 from friendship where username1 = '%s' and username2 not in (select A.username2 from friendship as A join friendship as B where A.username1 = B.username2 and a.username2 = b.username1 and a.username1 = '%s')", $loggedInUser, $loggedInUser);
	$pendingFriends = $mysqli->query($query);
	
	//print pending friends
	if ($pendingFriends->num_rows > 0) {
		while($row = $pendingFriends->fetch_assoc()) {
			$pendingFriend = $row["username2"];
			printf('<a href="http://localhost:8888/finalProject/profile.php?username=%s">%s</a><br>', $pendingFriend, $pendingFriend);
		}
	}
	
	//get non-friends
	printf("<br>Non-Friends of %s<br>", $loggedInUser);
	
	$query = sprintf("select username from user where username != '%s' and username not in (select A.username2 from friendship as A join friendship as B where A.username1 = B.username2 and A.username2 != B.username1 and A.username1 = '%s')", $loggedInUser, $loggedInUser);
	$nonFriends = $mysqli->query($query);
	
	//print non-friends as form
	
	printf('<form class="login-form" action="requestFriend.php" method="post">');
	if ($nonFriends->num_rows > 0) {
		while($row = $nonFriends->fetch_assoc()) {
			$nonFriend = $row["username"];
			printf('<a href="http://localhost:8888/finalProject/profile.php?username=%s">%s</a>', $nonFriend, $nonFriend);
			printf("     ");
			printf('<button name="requestedFriend" value="%s" type="submit">add friend</button><br>', $nonFriend);
		}
	}
	printf('</form>');
?>




