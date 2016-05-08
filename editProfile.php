<?php
	session_start();
	include_once 'externalFunctions.php';
	startMysqli();
	
	$loggedInUser = $_SESSION["loggedInUser"];
	checkLoggedIn();
	
	//get the profile entry
	$query = sprintf("select * from profile natural join user where profile.username ='%s'", $loggedInUser);
	$result = $mysqli->query($query);
	$entry = $result->fetch_assoc();

	$firstName = $entry["firstName"];
	$lastName = $entry["lastName"];
	$age = $entry["age"];
	$email = $entry["email"];
	$type = $entry["type"];
	$visibility = $entry["visibility"];
	$visibilities = array('me', 'friends', 'FOFs', 'everyone');
	
	generateHTMLTop('profile');
	displayEditProfilePage();
	generateHTMLBottom();

	
	//==================== Function Definitions ======================//
	
	function displayEditProfilePage() {
		
		global $firstName, $lastName, $age, $email, $visibility, $visibilities;
		
		printf("<h1> Edit Profile </h1><br>");
		
		//edit profile form
		printf('<form action="submitEditProfile.php" method="post" enctype="multipart/form-data">');
		printf('<br> First Name : <input type="text" name="firstName" value="%s"/>', $firstName);
		printf('<br> Last Name : <input type="text" name="lastName" value="%s"/>', $lastName);
		printf('<br> Age : <input type="text" name="age" value="%s"/>', $age);
		printf('<br> Email : <input type="text" name="email" value="%s"/>', $email);
		printf('<br> Type: <select name="type"> <option value="client">Client</option> <option value="nutritionist">Nutritionist</option> </select>');
		printf('<br> Visibility : <select name="visibility">');
		foreach ($visibilities as &$level) {
			printf('<option value="%s" ', $level);
			if ($level == $visibility) {
				printf('selected');
			}
			printf('>%s</option>', $level);
		}
		printf('</select>');
	
		//photo upload and submission form
		printf('<br> <input type="submit" name="submit" value="Submit"> </form>');
		printf('<form action="submitEditProfile.php" method="post" enctype="multipart/form-data"> Photo:');
		printf('<input type="file" name="fileToUpload" id="fileToUpload">');
		printf('<input type="submit" name="submitPhoto" value="submitPhoto" >');
		printf('</form>');
	}
?>