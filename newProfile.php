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
			$query = sprintf("select * from interactiveLike join location where interactiveLike.likedInteractiveID = location.interactiveID and likingUser = '%s'", $profileUsername);
			$result = $mysqli->query($query);
			$likedLocations = array('atleastOne');
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$location = $row["locName"];
					$interactiveID = $row["interactiveID"];
					array_push($likedLocations, $location);
				}
			}
		}
	}
	
echo <<< EOT
<html>
	<head>
		<link rel="stylesheet" href="test.css">
		<style type="text/css">a {text-decoration: none}</style>
	</head>
	<body>
		<div class="bg"></div>
		<div class="container">
			<nav>
				<div class="search-box">
				<div><i class="fa fa-search"></i>
					<input type="search" placeholder="Search"/>
				</div>
		</div>
		<ul class="menu">
			<a href="profile.php"> <li class="active"><i class="fa fa-user"></i>Profile</li> </a>
			<a href="wall.php"> <li><i class="fa fa-home"></i>Wall</li> </a>
			<a href="feed.php"> <li><i class="fa fa-home"></i>Feed</li> </a>
			<a href="friends.php"> <li><i class="fa fa-group"></i>Friends</li> </a>
			<a href="locations.php"> <li><i class="fa fa-envelope"></i>Locations</li> </a>
			<a href="logoff.php"> <li><i class="fa fa-cog"></i>Log Out</li> </a>
		</ul>
	</nav>
		<div class="header">
		<div class="userPanel">
		<div class="pic"></div>
EOT;
	
printf('<div class="welcome">Welcome %s!</div></div>', $loggedInUser);
	
echo <<< EOT
	<div class="title"></div>
	<div class="filler">
EOT;

	generateProfileHTML();
	
echo <<< EOT
	</div>
	</div>
	</div>
	</body>
	</html>
EOT;
	

function generateProfileHTML() {
	global $photo, $firstName, $lastName, $age, $email, $type, $loggedInUser, $profileUsername, $likedLocations;
	
	if ($photo != 'NULL') {
		printf('<div class="photo">');
		echo '<img src="data:image/jpeg;base64, ' . base64_encode($photo) . '" height="350" width="350" align="right"/> <br>';
		printf("</div>");
	}
	printf("First Name: %s <br>", $firstName);
	printf("Last Name: %s <br>", $lastName);
	printf("Age: %s <br>", $age);
	printf("Email: %s <br>", $email);
	printf("Type: %s <br>", $type);
	printf("Liked Locations: ");
	foreach ($likedLocations as &$location) {
		printf("one%s, ", $location);
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