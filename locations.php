<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	startMysqli();
	
	//get locations
	$query = sprintf("select * from location");
	$result = $mysqli->query($query);
	$locations = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			array_push($locations, $row);
		}
	}
	
	generateHTMLTop('locations');
	generateLocationsPage();
	generateHTMLBottom();
	
	//=============== Function Definitions ============//
	
	function generateLocationsPage() {
		global $loggedInUser, $locations, $locationIds;
		
		printf("<div class='pageHeader'>Locations</div>");
		
		//form to create new location
		printf('<div class="location">');
		printf('<div class="subHeader">New Location</div>');
		printf('<form action="addOrLikeLocation.php" method="post">');
		printf('Name: <input type="text" name="locName"><br>');
		printf('Longitude: <input type="text" name="longitude"><br>');
		printf('Latitude: <input type="text" name="latitude"><br>');
		printf('<input type="submit" value="Add Location" name="addLocation">');
		printf('</form>');
		printf("</div>");
		
		//show locations with like buttons
		printf('<form action="addOrLikeLocation.php" method="post">');
		foreach ($locations as &$location) {
			printf('<div class="location">');
			printf("%s [%s, %s] ", $location["locName"], $location["longitude"], $location["latitude"]);
			printf('<button name="likeLocation" value="%s" type="submit">Like</button>', $location["interactiveID"]);
			printf('</div>');
		}
		printf('</form>');
		
		//google map
		printf('<iframe width="425" height="350" align="right" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.co.nz/maps?q=120+Mayoral+Dr,+Auckland+University+of+Technology+-+City+Campus,+Auckland,+1010&amp;ie=UTF8&amp;hq=&amp;hnear=120+Mayoral+Dr,+Auckland,+1010&amp;t=m&amp;z=13&amp;ll=-36.859383,174.777836&amp;output=embed"></iframe><br />');
	}
	
?>









