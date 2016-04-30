<?php
	session_start();
	$username = $_SESSION["username"];

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
	$query = sprintf("select * from profile where username = '%s'", $username);
	$result = $mysqli->query($query);
	$entry = $result->fetch_assoc();
	
	//get the profile fields
	$query = sprintf("select column_name from information_schema.columns where table_schema = '%s' and table_name = 'profile'", $db);
	$profileFields = $mysqli->query($query);
?>

<html>
	<head>
		<link rel="stylesheet" href="login.css">
	</head>

	<body>
		<h1> Edit Profile </h1>

		<form action="submitEditProfile.php" method="post">

<?php
	$fieldValue = $entry["firstName"];
	printf('<br> First Name : <input type="text" name="firstName" value="%s"/>', $fieldValue);
	$fieldValue = $entry["lastName"];
	printf('<br> Last Name : <input type="text" name="lastName" value="%s"/>', $fieldValue);
	$fieldValue = $entry["age"];
	printf('<br> Age : <input type="text" name="age" value="%s"/>', $fieldValue);
	$fieldValue = $entry["email"];
	printf('<br> Email : <input type="text" name="email" value="%s"/>', $fieldValue);
	$fieldValue = $entry["visibility"];
	printf('<br> Visibility : <select name="visibility"> <option value="me">Me</option> <option value="friends">Friends</option> <option value="FOFs">Friends Of Friends</option> <option value="everyone">Everyone</option> </select>');
	printf('<br> Photo : <input type="text" name="photo" value=""/>');
?>

<br>
<button name="submit" type="submit" value="submit">Submit</button>
</form>


</div>
</div>
</body>
</html>