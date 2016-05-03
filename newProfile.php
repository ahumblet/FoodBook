<?php
	
	session_start();
	$loggedInUser = $_SESSION["loggedInUser"];
	
	include_once 'externalFunctions.php';
	startMysqli();
	
echo <<< EOT
<html>
	<head>
		<link rel="stylesheet" href="test.css">
		<style type="text/css">a {text-decoration: none}</style>
	</head>
	<body>
		<div class="bg"></div>
		<div class="container">
			<nav>
				<div class="search-box">
				<div><i class="fa fa-search"></i>
					<input type="search" placeholder="Search"/>
				</div>
		</div>
		<ul class="menu">
			<a href="login.php"> <li><i class="fa fa-user"></i>Profile</li> </a>
			<a href="login.php"> <li><i class="fa fa-home"></i>Wall</li> </a>
			<a href="login.php"> <li><i class="fa fa-home"></i>Feed</li> </a>
			<a href="login.php"> <li class="active"><i class="fa fa-group"></i>Friends</li> </a>
			<a href="login.php"> <li><i class="fa fa-envelope"></i>Locations</li> </a>
			<a href="login.php"> <li><i class="fa fa-cog"></i>Log Out</li> </a>
		</ul>
	</nav>
		<div class="header">
		<div class="userPanel">
		<div class="pic"></div>
EOT;
	
printf('<div class="welcome"><span>Welcome</span><span class="name">%s!</span></div></div>', $loggedInUser);
	
echo <<< EOT
	<div class="title"></div>
	<div class="filler">
EOT;
	
printf("This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.This should be after the header.");
	
echo <<< EOT
	</div>
	</div>
	</div>
	</body>
	</html>
EOT;
	
	?>