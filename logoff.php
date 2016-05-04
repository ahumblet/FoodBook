<?php	
	session_start();
	include_once 'externalFunctions.php';
	
	$_SESSION["loggedInUser"] = '';
	
	$headerString = sprintf("Location: %s/login.php", $urlRoot);
	header($headerString);
	exit;
?>



