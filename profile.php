<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	echo "<link rel='stylesheet' href='login.css'>";
	
	$profileUsername = $_GET["username"];
	
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

	//get the profile entry
	$query = sprintf("select * from profile where username = '%s'", $profileUsername);
	$result = $mysqli->query($query);
	
	printf("%s's profile: <br><br>", $profileUsername);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		
		//check visibility
		$visibility = $row["visibility"];
		$permission = hasPermission($loggedInUser, $profileUsername, $visibility, $mysqli);
		$mysqli->kill();
		$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
		if ($permission != True) {
			printf("You do not have permission to view %s's profile", $profileUsername);
		} else {
		
			$photo = $row["photo"];
			if ($photo != 'NULL') {
				echo '<img src="data:image/jpeg;base64, ' . base64_encode($photo) . '" height="200" width="200"/> <br>';
			}
			$firstName = $row["firstName"];
			printf("First Name: %s <br>", $firstName);
			$lastName = $row["lastName"];
			printf("Last Name: %s <br>", $lastName);
			$age = $row["age"];
			printf("Age: %s <br>", $age);
			$email = $row["email"];
			printf("Email: %s <br>", $email);
		
			//show all liked locations, clear sqli first
			$query = sprintf("select * from interactiveLike join location where interactiveLike.likedInteractiveID = location.interactiveID and likingUser = '%s'", $profileUsername);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				printf("Liked Locations: ");
				while ($row = $result->fetch_assoc()) {
					$location = $row["locName"];
					$interactiveID = $row["interactiveID"];
					printf("%s, ", $location);
				}
				printf("<br><br>");
			}
		
			//edit profile button
			if ($loggedInUser == $profileUsername) {
				echo '<form action="editProfile.php">';
				echo '<input type="submit" value="Edit Profile">';
				echo '</form>';
			}
			
			//link to wall
			printf("<br><br><a href='http://localhost:8888/finalProject/wall.php?username=%s'>View %s's wall</a>", $profileUsername, $profileUsername);
		}
	}

	
	
	
	//THIS SHOULD BE MOVED TO A COMMON FUNCTION AREA
	//is user1 allowed to see user2's content based on this visibility?
	function hasPermission($user1, $user2, $level, $mysqli)
	{
		if (($user1 == $user2) || ($level == 'everyone')) {
			return True;
		}
		if ($level == "FOFs") {
			$query = sprintf("CALL isFOFsOf('%s', '%s');", $user1, $user2);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return True;
			}
		} elseif ($level == "friends") {
			$query = sprintf("CALL isFriendsOf('%s', '%s')", $user1, $user2);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return True;
			}
		} elseif ($level == "nutritionists") {
			$query = sprintf("select * from user where type = 'nutritionist' and username = '%s'", $user1);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return True;
			}
		} elseif ($level == "me") {
			if ($user1 == $user2) {
				return True;
			}
		}
		return False;
	}
?>





