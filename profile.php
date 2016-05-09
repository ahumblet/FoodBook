<?php
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	$profileUsername = $_GET["username"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	//get the profile entry
	$query = sprintf("select * from profile natural join user where profile.username ='%s'", $profileUsername);
	$result = $mysqli->query($query);
	
	if ($result->num_rows > 0) {
		$profile = $result->fetch_assoc();
		//get liked locations
		restartMysqli();	//this seems to be necessary!
		$query = sprintf("select * from interactiveLike join location where interactiveLike.likedInteractiveID = location.interactiveID and likingUser = '%s'", $profileUsername);
		$result = $mysqli->query($query);
		$likedLocations = array();
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$location = $row["locName"];
				$interactiveID = $row["interactiveID"];
				array_push($likedLocations, $location);
			}
		}
	}
	generateHTMLTop('profile');
	generateProfileHTML();
	generateHTMLBottom();
	
//===============Function definitions==============//
	
	function generateProfileHTML() {
		global $profile, $loggedInUser, $profileUsername, $likedLocations;
		
		printf('<div class="post">');
		
		printf("<div class='pageHeader'>%s's profile</div>", $profileUsername);
		
		//check visibility
		$visibility = $profile["visibility"];
		$permission = hasPermission($loggedInUser, $profileUsername, $visibility);
		if ($permission != TRUE) {
			printf("You do not have permission to view %s's profile", $profileUsername);
		} else {
			if ($photo != '') {
				printf('<div class="profilePhoto">');
				//echo '<img src="data:image/jpeg;base64, ' . base64_encode($photo) . '"';
							echo '<img src="data:image/jpeg;base64, ' . base64_encode($photo) . '" height="270" width="270" align="right"/> <br>';
				printf("</div>");
			}
			printf("First Name: %s <br>", $profile["firstName"]);
			printf("Last Name: %s <br>", $profile["lastName"]);
			printf("Age: %s <br>", $profile["age"]);
			printf("Email: %s <br>", $profile["email"]);
			printf("Type: %s <br>", $profile["type"]);
			printf("Liked Locations: ");
			foreach ($likedLocations as &$location) {
				printf("%s, ", $location);
			}
			printf("<br><br>");
			//edit profile button
			if ($loggedInUser == $profileUsername) {
				echo '<form action="editProfile.php">';
				echo '<input type="submit" value="Edit Profile">';
				echo '</form>';
			}
			
			//link to wall
		 printf("<br><a href='http://localhost:8888/finalProject/wall.php?username=%s'>View %s's wall</a>", $profileUsername, $profileUsername);
			
			//link to wall
		 printf("<br><br><a href='http://localhost:8888/finalProject/data.php?username=%s'>View %s's stats</a>", $profileUsername, $profileUsername);
		}
		printf('</div>');
	}
?>