<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	echo "<link rel='stylesheet' href='login.css'>";
	
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
	
	printf("Locations:<br><br>");
	
	//form to create new location
	printf('<form action="addOrLikeLocation.php" method="post">');
	printf('Name: <input type="text" name="locName"><br>');
	printf('Longitude: <input type="text" name="longitude"><br>');
	printf('Latitude: <input type="text" name="latitude"><br>');
	printf('<input type="submit" value="Add Location" name="addLocation"> <br>');
	printf('</form><br>');
	
	//show locations with like buttons
	$query = sprintf("select * from location");
	$result = $mysqli->query($query);
	printf('<form action="addOrLikeLocation.php" method="post">');
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			printf("locname = %s, interactiveID = %s, &nbsp&nbsp", $row["locName"], $row["interactiveID"]);
			printf('<button name="likeLocation" value="%s" type="submit">Like</button><br>', $row["interactiveID"]);
		}
	}
	printf('</form>');
?>









