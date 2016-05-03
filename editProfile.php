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
	
	//get the profile entry
	$query = sprintf("select * from profile where username = '%s'", $loggedInUser);
	$result = $mysqli->query($query);
	$entry = $result->fetch_assoc();
?>

<html>
	<head>
		<link rel="stylesheet" href="login.css">
	</head>

	<body>
		<h1> Edit Profile </h1>

		<form action="submitEditProfile.php" method="post" enctype="multipart/form-data">

<?php
	$firstName = $entry["firstName"];
	printf('<br> First Name : <input type="text" name="firstName" value="%s"/>', $firstName);
	$lastName = $entry["lastName"];
	printf('<br> Last Name : <input type="text" name="lastName" value="%s"/>', $lastName);
	$age = $entry["age"];
	printf('<br> Age : <input type="text" name="age" value="%s"/>', $age);
	$email = $entry["email"];
	printf('<br> Email : <input type="text" name="email" value="%s"/>', $email);
	$visibility = $entry["visibility"];
	$visibilities = array('me', 'friends', 'FOFs', 'everyone');
	printf('<br> Visibility : <select name="visibility">');
	foreach ($visibilities as &$level) {
		printf('<option value="%s" ', $level);
		if ($level == $visibility) {
			printf('selected');
		}
		printf('>%s</option>', $level);
	}
	printf('</select>');
?>

<br>
<button type="submit" name="submit" value="submit">Submit</button>
</form>

<br>
<form action="submitEditProfile.php" method="post" enctype="multipart/form-data">
Photo:
<input type="file" name="fileToUpload" id="fileToUpload">
<input type="submit" name="submitPhoto" value="submitPhoto" >
</form>


</div>
</div>
</body>
</html>