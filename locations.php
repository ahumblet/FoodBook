<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	echo "<link rel='stylesheet' href='login.css'>";
	
	include_once 'externalFunctions.php';
	startMysqli();
	
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









