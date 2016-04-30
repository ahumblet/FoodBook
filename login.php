<?php
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
	
	//define a global javascript error variable
	if ($_GET["userError"] == 1) {
		echo '<script type="text/javascript">', 'var userError = 1;', '</script>';
	} else if ($_GET["userError"] == 2) {
		echo '<script type="text/javascript">', 'var userError = 2;', '</script>';
	}
	
	echo <<< EOT
	<html>
		<head>
			<link rel="stylesheet" href="login.css">
		</head>
	
		<body>
			<div class="login-page">
				<div class="form">
					<!-- java script to toggle forms -->
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
					<script>
					$(document).ready(function(){
						$("p").click(function(){
							$('form').animate({height: "toggle", opacity: "toggle"}, "slow");
						});
					});
					</script>
	
					<script type="text/javascript">
					if (userError == 1) {
						document.write('<p class="message">Invalid username and/or password. Try again</p><br>');
					} else if (userError == 2) {
						document.write('<p class="message">Username already exists. Try again</p><br>');
					}
					</script>

					<form class="login-form" action="checkLogin.php" method="post">
						<input type="text" name="username" placeholder="username"/>
						<input type="password" name="password" placeholder="password"/>
						<button name="login" type="submit">Login</button>
						<p class="message">Not registered? <a href="#">Create an account</a></p>
					</form>
					<!-- register form -->
					<form class="register-form" action="checkLogin.php" method="post">
						<input type="text" name="username" placeholder="name"/>
						<input type="password" name="password" placeholder="password"/>
						<input type="text" name="placeholder" placeholder="email address"/>
						<button name="register" type="submit">Register</button>
						<p class="message">Already registered? <a href="#" onclick="function()"> Sign In</a></p>
					</form>
				</div>
			</div>
		</body>
	</html>
EOT;
	
?>



