<?php
// RadioPanel -  User Functions
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// 

function user_interface() {
	global $user_session;
	if(isset($_GET['task'])) { $task = ($_GET['task']); } else { $task = ""; }
	if(isset($_GET['sid'])) { $sid = ($_GET['sid']); } else { $sid = 0; }
	
	display_head("User Management");
	display_header("User Management");
	switch($task) {
		case "add":
			user_add();
			break;
		case "edit":
			user_edit($uid);
			break;
		case "delete":
			user_delete($uid);
			break;
		case "home":
		default:
			user_list();
			break;
	}
	display_footer();
}

// List all streams
function user_list() {
	global $db_session;
	echo "<p><a href=\"./?page=users&task=add\"><img src=\"img/user_add.png\" alt=\"Add\">Add new user</a></p>\n";
	echo "<table><tr class=\"streamlist-header\"><td class=\"userlist-sid\">UID</td><td class=\"userlist-name\">Username</td><td class=\"userlist-email\">Server</td><td class=\"userlist-access\">Access Level</td><td>Options</td></tr>\n";
	// Get list from database
	$result = $db_session->query("SELECT `id`, `username`, `email`, `access` FROM `users`;");
	if($result) {
		// Each server post details along with edit buttons.
		while($user = mysqli_fetch_array($result)) {
			echo "<tr><td>".$user['id']."</td><td>".$user['username']."</td><td>".$user['email']."</td><td>".$user['access']."</td><td><a href=\"./?users&task=edit&uid=".$user['id']."\"><img src=\"img/pencil.png\" title=\"Edit\" alt=\"Edit\" /></a>&nbsp;<a href=\"./?page=users&task=delete&uid=".$user['id']."\"><img src=\"img/delete.png\" title=\"Remove\" alt=\"Remove\" /></a></td></tr>\n";	
		}
	}
	echo "</table>\n";
}

function user_delete($uid) {
	global $db_session;
	echo "<p><a href=\".\?page=users\">Back...</a></p>";
	if(isset($_POST['submit'])) {
		// We have had a confirmation to delete. Delete!
		$result = $db_session->query("DELETE from `users` WHERE id='$uid';");
		if($result) {
			echo "<h3>Deleted.</h3><p>User $uid is no more.</p>\n";	
		} else {
			echo "<h3 class=\"error\">Error: Not deleted</h3>\n";
			echo "<p>For some unknown reason, $uid was not deleted. Sorry about that, you must be disappointed.</p>";	
		}
	} else {
		// Confirm to the user
		echo "<h3>Are you sure?</h3>";
		echo "<p>Confirm you want to remove user $uid</p>";
		echo "<form action=\"./?page=users&task=delete&uid=$uid\" method=\"post\">";
		echo "<input type=\"hidden\" value=\"$uid\" ><input type=\"submit\" name=\"submit\" value=\"Delete $uid\">";
		echo "</form>\n";
			
	}
}

function user_add() {
	global $db_session;
	$account = new UserService($db_session);
	echo "<p><a href=\".\?page=users\">Back...</a></p>";
	if(isset($_POST['submit'])) {
		// We're adding a stream
		$username = $db_session->real_escape_string($_POST['username']);
		$email = $db_session->real_escape_string($_POST['email']);
		$password_1 = $db_session->real_escape_string($_POST['password_1']);
		$password_2 = $db_session->real_escape_string($_POST['password_2']);
		$access = $db_session->real_escape_string($_POST['access']);
		if(!$access) $access = 10;
		
		do {
			if(!$username || !$email || !$password_1 || !$password_2) {
				echo "<p class=\"error\">Error: One or more fields were left blank</p>\n";
				break;
			}
			if(!($password_1 === $password_2)) {
				echo "<p class=\"error\">Error: Password mismatch. Please ensure the password is entered correctly twice.</p>\n";
				break;
			}
			$result = $account->registerUser($username, $password_1, $email, $access);
			if($result) {
				echo "<h3>User added successfully.</h3><hr />\n";	
				// Blank out vars
				$username = "";
				$password_1 = "";
				$password_2 = "";
				$email = "";
				$access = 10;
			} else {
				echo "<p class=\"error\">Error: Unable to add user account.</p>";	
			}
		} while(0);
	}
	// Display new user form
	echo "<h3>Add new user</h3>\n";
	echo "<div class=\"edituser\">\n";
	echo "<form action=\"./?page=users&task=add\" method=\"post\">\n";
	echo "<div class=\"edituser-1\">Username</div><div class=\"edituser-2\"><input name=\"username\" maxlength=\"64\" value=\"".$username."\" type=\"text\"></div>\n";
	echo "<div class=\"edituser-1\">Password</div><div class=\"edituser-2\"><input name=\"password_1\" maxlength=\"64\" value=\"\" type=\"password\"></div>\n";
	echo "<div class=\"edituser-1\">Password (repeat)</div><div class=\"edituser-2\"><input name=\"password_2\" maxlength=\"64\" value=\"\" type=\"password\"></div>\n";
	echo "<div class=\"edituser-1\">Email</div><div class=\"edituser-2\"><input name=\"email\" maxlength=\"64\" value=\"".$email."\" type=\"text\"></div>\n";
	echo "<div class=\"edituser-1\">Access Level</div><div class=\"edituser-2\" id=\"user-access-slider-desc\">0 (Account Disabled)</div>\n";
	echo "<input name=\"access\" id=\"user-access-slider-input\" value=\"".$access."\" type=\"hidden\">";
	echo "<div class=\"edituser-1\"></div><div class=\"edituser-2\" id=\"user-access-slider\"></div>\n";
	echo "<div class=\"edituser-1\"></div><div class=\"edituser-2\"><input name=\"submit\" value=\"Add user account\" type=\"submit\"></div>\n";
	echo "<div class=\"clear\"></div></form></div>\n";
	echo "";
}
?>