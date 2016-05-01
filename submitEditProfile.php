<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];

	$user = 'root';
	$password = 'root';
	$db = 'Nutrition';
	$host = 'localhost';
	$port = 3306;
	
	$mysqli = new mysqli("$localhost", "$user", "$password", "$db");
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	$username = $loggedInUser;
	if (isset($_POST['submit'])) {
		//response to edit-profile form
		$firstName = $_POST["firstName"];
		$lastName = $_POST["lastName"];
		$email = $_POST["email"];
		$age = $_POST["age"];
		$photo = $_POST["photo"];
		$visibility = $_POST["visibility"];
		
		//make changes to the database
		//$query = sprintf("delete from profile where username = '%s'", $username);
		//$result = $mysqli->query($query);
		
		//$query = sprintf("insert into profile (username, firstName, lastName, age, photo, visibility, email) values ('%s', '%s', '%s', '%s', '%s', '%s', '%s')", $username, $firstName, $lastName, $age, $photo, $visibility, $email);
		
		$query = sprintf("update profile set firstName = '%s', lastName = '%s', age = %s, visibility = '%s', email = '%s' where username = '%s'", $firstName, $lastName, $age, $visibility, $email, $username);
		
		$result = $mysqli->query($query);
	} elseif (isset($_FILES['fileToUpload'])) {
		$tmp_name  = $_FILES['fileToUpload']['tmp_name'];
		//echo $tmp_name;
		$file_content = addslashes(file_get_contents($tmp_name));
		//echo "<br>";
		//printf("<br> username = %s", $username);
		//echo "<br>";
		$query = sprintf("update profile set photo = '%s' where username = '%s'", $file_content, $username);
		//echo "<br>";
		//echo $query;
		$mysqli->query($query);
		//UPDATE `Nutrition`.`profile` SET `photo` = xxx
		//echo "<br>photo submitted";
	} else {
		echo "nothing";
	}
	
	//now go back to profile
	header("Location: profile.php");
	exit;
?>
