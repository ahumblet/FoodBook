<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	startMysqli();
	
	//get friends
	$query = sprintf("select A.username2 as friend from friendship as A join friendship as B where A.username1 = B.username2 and A.username2 = B.username1 and A.username1 = '%s'", $loggedInUser);
	$friendsResult = $mysqli->query($query);
	$friends = array();
	if ($friendsResult->num_rows > 0) {
		while($row = $friendsResult->fetch_assoc()) {
			$friend = $row["friend"];
			array_push($friends, $friend);
		}
	}
	
	//get friend requests
	$query = sprintf("select username1 from friendship where username2 = '%s' and username1 not in (select A.username2 from friendship as A join friendship as B where A.username1 = B.username2 and a.username2 = b.username1 and a.username1 = '%s')", $loggedInUser, $loggedInUser);
	$invitesResult = $mysqli->query($query);
	$invites = array();
	if ($invitesResult->num_rows > 0) {
		while($row = $invitesResult->fetch_assoc()) {
			$invite = $row["username1"];
			array_push($invites, $invites);
		}
	}
	
	//get pending requests
	$query = sprintf("select username2 from friendship where username1 = '%s' and username2 not in (select A.username2 from friendship as A join friendship as B where A.username1 = B.username2 and a.username2 = b.username1 and a.username1 = '%s')", $loggedInUser, $loggedInUser);
	$pendingFriendsResult = $mysqli->query($query);
	$pendingFriends = array();
	if ($pendingFriendsResult->num_rows > 0) {
		while($row = $pendingFriendsResult->fetch_assoc()) {
			$pendingFriend = $row["username2"];
			array_push($pendingFriends, $pendingFriend);
		}
	}
	
	//get non-friends
	$query = sprintf("select username from user where username != '%s' and username not in (select A.username2 from friendship as A join friendship as B where A.username1 = B.username2 and A.username2 != B.username1 and A.username1 = '%s')", $loggedInUser, $loggedInUser);
	$nonFriendsResult = $mysqli->query($query);
	$nonFriends = array();
	if ($nonFriendsResult->num_rows > 0) {
		while($row = $nonFriendsResult->fetch_assoc()) {
			$nonFriend = $row["username"];
			array_push($nonFriends, $nonFriend);
		}
	}
	
	generateHTMLTop('friends');
	generateFriendsPage();
	generateHTMLBottom();

	//================ Function Definitions ===========//
	
	function generateFriendsPage() {
		global $loggedInUser, $friends, $invites, $pendingFriends, $nonFriends;
		
		//title
		printf("Friends of %s<br>", $loggedInUser);
		
		//display all friends as links
		printf("<br>You are friends with: <br>");
		foreach ($friends as &$friend) {
			printf('<a href="$urlRoot/finalProject/profile.php?username=%s">%s</a><br>', $friend, $friend);
		}
		
		//display invites with form to accept
		printf("<br>You have friendship invites from: <br>");
		printf('<form class="login-form" action="acceptFriend.php" method="post">');
		foreach ($invites as &$invite) {
			printf('<a href="$urlRoot/profile.php?username=%s">%s</a>', $invite, $invite);
			printf('<button name="acceptedFriend" value="%s" type="submit">accept friend</button>', $invite);
			printf('<button name="rejectedFriend" value="%s" type="submit">reject friend</button><br>', $invite);
		}
		printf('</form>');
		
		//display pending requests
		printf("<br>Waiting for responses from:<br>");
		foreach ($pendingFriends as $pendingFriend) {
			printf('<a href="$urlRoot/profile.php?username=%s">%s</a><br>', $pendingFriend, $pendingFriend);
		}
		
		//display non-friends
		printf("<br>Non-Friends of %s<br>", $loggedInUser);
		printf('<form class="login-form" action="requestFriend.php" method="post">');
		foreach ($nonFriends as &$nonFriend) {
			printf('<a href="http://localhost:8888/finalProject/profile.php?username=%s">%s</a>', $nonFriend, $nonFriend);
			printf('<button name="requestedFriend" value="%s" type="submit">add friend</button><br>', $nonFriend);
		}
		printf('</form>');
	}
?>





