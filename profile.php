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
		//check visibility
		$visibility = $row["visibility"];
		$permission = hasPermission($loggedInUser, $profileUsername, $visibility);
		$mysqli->kill();
		$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
		if ($permission != True) {
			printf("You do not have permission to view %s's profile", $profileUsername);
		} else {
			//get all the info for the profile
			$photo = $row["photo"];
			$firstName = $row["firstName"];
			$lastName = $row["lastName"];
			$age = $row["age"];
			$email = $row["email"];
			$type = $row["type"];
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
	}
	generateHTMLTop('profile');
	generateProfileHTML();
	generateHTMLBottom();
	
//===============Function definitions==============//
	
	function generateProfileHTML() {
		global $photo, $firstName, $lastName, $age, $email, $type, $loggedInUser, $profileUsername, $likedLocations;
		
		if ($photo != 'NULL') {
			printf('<div class="photo">');
			echo '<img src="data:image/jpeg;base64, ' . base64_encode($photo) . '" height="400" width="400" align="right"/> <br>';
			printf("</div>");
		}
		printf("First Name: %s <br>", $firstName);
		printf("Last Name: %s <br>", $lastName);
		printf("Age: %s <br>", $age);
		printf("Email: %s <br>", $email);
		printf("Type: %s <br>", $type);
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
	 printf("<br><br><a href='http://localhost:8888/finalProject/wall.php?username=%s'>View %s's wall</a>", $profileUsername, $profileUsername);
	}
?>