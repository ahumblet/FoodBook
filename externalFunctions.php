<?php
	//is user1 allowed to see user2's content based on this visibility?
	function hasPermission($user1, $user2, $level, $mysqli)
	{
		if (($user1 == $user2) || ($level == 'everyone')) {
			return True;
		}
		if ($level == "FOFs") {
			$query = sprintf("CALL isFOFsOf('%s', '%s');", $user1, $user2);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return True;
			}
		} elseif ($level == "friends") {
			$query = sprintf("CALL isFriendsOf('%s', '%s')", $user1, $user2);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return True;
			}
		} elseif ($level == "nutritionists") {
			$query = sprintf("select * from user where type = 'nutritionist' and username = '%s'", $user1);
			$result = $mysqli->query($query);
			if ($result->num_rows > 0) {
				return True;
			}
		} elseif ($level == "me") {
			if ($user1 == $user2) {
				return True;
			}
		}
		return False;
	}
?>





