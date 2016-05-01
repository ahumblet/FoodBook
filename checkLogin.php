<?php
	session_start();
	
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
	
	if(isset($_POST['login'])) {
		//response to LOGIN form
		$username = $_POST["username"];
		$password = $_POST["password"];
		
		//check for valid username and password
		$query = sprintf("select username from user where (username = '%s') and (password = MD5('%s'))", $username, $password);
		$result = $mysqli->query($query);
		if ($result->num_rows == 1) {
			//set the session username, go to home profile
			$_SESSION["loggedInUser"] = $username;
			$headerString = sprintf("Location: profile.php?username=%s", $username);
			header($headerString);
			exit;
		} else {
			header("Location: login.php?userError=1");
			exit;
		}
	} else {
		//response to REGISTER form
		$username = $_POST["username"];
		$password = $_POST["password"];
		$email = $_POST["email"];
		
		//check if username already exists
		$query = sprintf("select username from user where username = '%s'", $username);
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {
			header("Location: login.php?userError=2");
			exit;
		} else {
			//create entry
			$query = sprintf("INSERT INTO user (username, password, type) VALUES ('%s', '%s', 'client')", $username, MD5($password));
			$result = $mysqli->query($query);
			
			//create profile
			$query = sprintf("INSERT INTO profile (username, firstName, lastName, age, photo, visibility, email) values ('%s', '', '', NULL, NULL, '', '%s')", $username, $email);
			$result = $mysqli->query($query);
			
			//set the session username, go to home profile
			$_SESSION["loggedInUser"] = $username;
			$headerString = sprintf("Location: profile.php?username=%s", $username);
			header($headerString);
			exit;
		}
	}
	
	?>
