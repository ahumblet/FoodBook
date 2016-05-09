<?php
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	checkLoggedIn();
	startMysqli();
	
	//get locations
	$query = sprintf("select * from location order by locName");
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

		printf('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
		printf('<script SRC="locationMap.js"> </script>');
		printf('<div id="mapCanvas"></div>');
		printf('<form id="locationForm" action="addOrLikeLocation.php" method="post">');
		printf('&nbsp;&nbsp;Location Name <input type="text" name="locName"><br>');
		printf('&nbsp;&nbsp;Longitude <input type="text" name="longitude" id="Longitude"><br>');
		printf('&nbsp;&nbsp;Latitude <input type="text" name="latitude" id="Latitude"><br>');
		printf('&nbsp;&nbsp;<input type="submit" value="Add Location" name="addLocation"><br>');
		printf('</form>');
		printf('<br><br><br><br>');
		printf('</div>');
		
		//show locations with like buttons
		printf('<form action="addOrLikeLocation.php" method="post">');
		foreach ($locations as &$location) {
			printf('<div class="location">');
			printf("%s [%s, %s] ", $location["locName"], $location["longitude"], $location["latitude"]);
			printf('<button name="likeLocation" value="%s" type="submit">Like</button>', $location["interactiveID"]);
			printf('</div>');
		}
		printf('</form>');
	}
	
?>









