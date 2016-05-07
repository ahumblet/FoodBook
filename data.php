<?php
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	$profileUsername = $_GET["username"];
	
	include_once 'externalFunctions.php';
	startMysqli();
	
	//get the profile entry
	$query = sprintf("select * from profile natural join user where profile.username ='%s'", $profileUsername);
	$result = $mysqli->query($query);
	
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
	}
	
	//number of friends
/*	SELECT count(A.username2)
	FROM FRIENDSHIP as A
	JOIN FRIENDSHIP as B
	WHERE A.username1 = B.username2
	AND A.username2 = B.username1
	AND A.username1 = 'adrienne'
	GROUP BY A.username1   */
	
	////
	///most liked post
	///number of friends
	///most disliked post
	///most commented post

	
	
	generateHTMLTop('profile');
	generateProfileHTML();
	generateHTMLBottom();
	
//===============Function definitions==============//
	
	function generateProfileHTML() {
		global $photo, $firstName, $lastName, $age, $email, $type, $loggedInUser, $profileUsername, $likedLocations;
		
		printf("<div class='pageHeader'>%s's stats</div>", $profileUsername);
	
	}
?>