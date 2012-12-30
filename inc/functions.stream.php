<?php
// RadioPanel -  Stream Functions
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// 
// - stream_interface()    - Web interface and task selector
// - stream_recordcron()   - Cron API call, logs listener figures from active streams to the database
// - stream_livestats()    - Display live listener figures
// - stream_getlivestats() - API AJAX call for live listener figures
// - stream_list()         - List streams registered in the database
// - stream_delete()       - Delete a stream
// - stream_edit()         - Edit a current stream or add a new stream

// Stream Interface
function stream_interface() {
	global $user_session;
	$task = mysql_real_escape_string($_GET['task']);
	$sid = mysql_real_escape_string($_GET['sid']);
	
	display_head("Stream Management");
	display_header("Stream Management");
	switch($task) {
		case "edit":
			stream_edit($sid);
			break;
		case "delete":
			stream_delete($sid);
			break;
		case "home":
		default:
			stream_list();
			break;
	}
	display_footer();
}

// Stat recording cron
function stream_recordcron() {
	global $db_session;
	$total = 0;
	$time = time();
	echo $time;
	
	// Get live figures
	$result = $db_session->query("SELECT * FROM `streams` WHERE `active`='1';");
	if($result) {
		// Each server post details along with edit buttons.
		while($server = mysql_fetch_array($result)) {
			$stream = new Stream($server['server'], $server['username'], $server['password'], $server['mountpoint']);
			if($stream->isLive()) {
				$temp = $stream->getCurrentListenersCount();
				$total += $temp;
			}
		}
	}
	if(!$result) {
		echo "Error: No active streams running.";	
	}
	
	// Save into database
	$result = $db_session->query("INSERT INTO `figures` (`timestamp`, `listeners`) VALUES ('$time', '$total');");
	if(!$result) {
		echo "Error: Unable to save into database!";	
	}
}

function stream_livestats() {
	display_head("Live Stats");
	display_header("Live Stats");
	echo "<div id=\"streamstats\">";
	stream_getlivestats();
	echo "</div><br />";
	echo "<p style=\"font-style:italic;\">Note: figures are live and will automatically update</p><br /><a href=\"#\" id=\"dialog_link\"  class=\"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only\"><span class=\"ui-icon ui-icon-newwin\"></span>Open live stats in a popup window</a>";
	display_footer();
}

function stream_getlivestats() {
	global $db_session;
	$total = 0;
	
	$result = $db_session->query("SELECT * FROM `streams` WHERE `active`='1';");
	if($result) {
		// Each server post details along with edit buttons.
		while($server = mysql_fetch_array($result)) {
			$stream = new Stream($server['server'], $server['username'], $server['password'], $server['mountpoint']);
			if($stream->isLive()) {
				$temp = $stream->getCurrentListenersCount();
				echo $server['name']." - $temp<br />";
				$total += $temp;
			}
		}
	}
	echo "<h3>Total: <strong>$total</strong></h3>\n";
}
// List all streams
function stream_list() {
	global $db_session;
	echo "<p><a href=\"./?page=streams&task=edit&sid=0\"><img src=\"img/add.png\" alt=\"Add\">Add new server</a></p>\n";
	echo "<table><tr class=\"streamlist-header\"><td class=\"streamlist-sid\">SID</td><td class=\"streamlist-name\">Name</td><td class=\"streamlist-server\">Server</td><td class=\"streamlist-mount\">Mountpoint</td><td class=\"streamlist-active\">Act.</td><td class=\"streamlist-options\">Online</td><td>Options</td></tr>\n";
	// Get list from database
	$result = $db_session->query("SELECT * FROM `streams` LIMIT 100;");
	if($result) {
		// Each server post details along with edit buttons.
		while($server = mysql_fetch_array($result)) {
			$stream = new Stream($server['server'], $server['username'], $server['password'], $server['mountpoint']);
			if($stream->isLive()) {
				$status = "<img src=\"img/bullet_green.png\" title=\"Server Online\" />"; 
			} else {
				$status = "<img src=\"img/bullet_red.png\" title=\"Server Offline\" />"; 
			}

			if($server['active'] == 1) { 
				$active = "<img src=\"img/bullet_green.png\" title=\"Server Active\" />"; 
			} else { 
				$active = "<img src=\"img/bullet_red.png\" title=\"Server Inactive\" />"; 
			}
			echo "<tr><td>".$server['sid']."</td><td>".$server['name']."</td><td>".$server['server']."</td><td>".$server['mountpoint']."</td><td>$active</td><td>$status</td><td><a href=\"./?streams&task=edit&sid=".$server['sid']."\"><img src=\"img/pencil.png\" title=\"Edit\" alt=\"Edit\" /></a>&nbsp;<a href=\"./?page=streams&task=delete&sid=".$server['sid']."\"><img src=\"img/delete.png\" title=\"Remove\" alt=\"Remove\" /></a></td></tr>\n";	
		}
	}
	echo "</table>\n";
}

// Delete a stream
function stream_delete($sid) {
	global $db_session;
	echo "<p><a href=\".\?page=streams\">Back...</a></p>";
	if(isset($_POST['submit'])) {
		// We have had a confirmation to delete. Delete!
		$result = $db_session->query("DELETE from `streams` WHERE sid='$sid';");
		if($result) {
			echo "<h3>Deleted.</h3><p>$sid is no more.</p>\n";	
		} else {
			echo "<h3 class=\"error\">Error: Not deleted</h3>\n";
			echo "<p>For some unknown reason, $sid was not deleted. Sorry about that, you must be disappointed.</p>";	
		}
	} else {
		// Confirm to the user
		echo "<h3>Are you sure?</h3>";
		echo "<p>Confirm you want to remove server $sid</p>";
		echo "<form action=\"./?page=streams&task=delete&sid=$sid\" method=\"post\">";
		echo "<input type=\"hidden\" value=\"$sid\" ><input type=\"submit\" name=\"submit\" value=\"Delete $sid\">";
		echo "</form>\n";
			
	}
}
function stream_edit($sid) {
	global $db_session;
	echo "<p><a href=\".\?page=streams\">Back...</a></p>";
	if(isset($_POST['submit'])) {
		// We're making ammendments to a stream
		$name = mysql_real_escape_string($_POST['name']);
		$server = mysql_real_escape_string($_POST['server']);
		$username = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		$mountpoint = mysql_real_escape_string($_POST['mountpoint']);
		if(mysql_real_escape_string($_POST['active']) == "on") { $active = TRUE; } else { $active = FALSE; }
		$sid = mysql_real_escape_string($_POST['sid']);
		
		$result = $db_session->query("UPDATE `streams` SET `name`='$name', `server`='$server', `username`='$username', `password`='$password', `mountpoint`='$mountpoint', `active`='$active' WHERE `sid`='$sid' LIMIT 1;");
		if($result) {
			echo "<h3>Update successful.</h3>\n";
		} else {
			echo "<h3 class=\"error\">Error: Unable to save details. Changes not saved.</h3>\n";	
		}
	} 
	if(isset($_POST['submit-new']) && isset($_POST['sid-new'])) {
		$name = mysql_real_escape_string($_POST['name']);
		$server = mysql_real_escape_string($_POST['server']);
		$username = mysql_real_escape_string($_POST['username']);
		$password = mysql_real_escape_string($_POST['password']);
		$mountpoint = mysql_real_escape_string($_POST['mountpoint']);
		if(mysql_real_escape_string($_POST['active']) == "on") { $active = TRUE; } else { $active = FALSE; }
		if(!$name || !$server || !$username || !$password || !$mountpoint) {
			echo "<h3 class=\"error\">Error: A field was left empty.</h3>\n";
		} else {
			$result = $db_session->query("INSERT INTO `streams` (`name`, `server`, `username`, `password`, `mountpoint`, `active`) VALUES ('$name', '$server', '$username', '$password', '$mountpoint', '$active');");
			if($result) {
				echo "<h3>Server added successfully.</h3>\n";	
			}
		}
	}
	// Display the editor. We will get the updated record from the database.
	// If SID=0 or incorrect, show form for new server.
	$result = $db_session->query("SELECT * FROM `streams` WHERE `sid` = '$sid' LIMIT 1;");
	if(mysql_num_rows($result)) {
		$server = mysql_fetch_array($result);
		echo "<h3>Server $sid</h3>\n";
		echo "<div class=\"editstream\">\n";
		echo "<form action=\"./?page=streams&task=edit&sid=$sid\" method=\"post\">\n";
		echo "<div class=\"editstream-1\">Name</div><div class=\"editstream-2-long\"><input name=\"name\" maxlength=\"64\" value=\"".$server['name']."\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Server</div><div class=\"editstream-2-long\"><input name=\"server\" maxlength=\"256\" value=\"".$server['server']."\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Username</div><div class=\"editstream-2-long\"><input name=\"username\" maxlength=\"64\" value=\"".$server['username']."\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Password</div><div class=\"editstream-2-long\"><input name=\"password\" maxlength=\"64\" value=\"".$server['password']."\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Mountpoint</div><div class=\"editstream-2-long\"><input name=\"mountpoint\" maxlength=\"64\" value=\"".$server['mountpoint']."\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Active</div><div class=\"editstream-2\"><input name=\"active\" type=\"checkbox\"";
		if($server['active']) echo " checked=\"checked\"";
		echo "></div>\n";
		echo "<div class=\"editstream-1\"></div><div class=\"editstream-2\"><input name=\"submit\" value=\"Update details\" type=\"submit\"></div>\n";
		echo "<input name=\"sid\" type=\"hidden\" value=\"$sid\">\n";
		echo "<div class=\"clear\"></div></form></div>\n";
		echo "";
	} else {
		// Add new server
		echo "<h3>Add new server</h3>\n";
		echo "<div class=\"editstream\">\n";
		echo "<form action=\"./?page=streams&task=edit&sid=$sid\" method=\"post\">";
		echo "<div class=\"editstream-1\">Name</div><div class=\"editstream-2-long\"><input name=\"name\" maxlength=\"64\" value=\"\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Server</div><div class=\"editstream-2-long\"><input name=\"server\" maxlength=\"256\" value=\"\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Username</div><div class=\"editstream-2-long\"><input name=\"username\" maxlength=\"64\" value=\"\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Password</div><div class=\"editstream-2-long\"><input name=\"password\" maxlength=\"64\" value=\"\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Mountpoint</div><div class=\"editstream-2-long\"><input name=\"mountpoint\" maxlength=\"64\" value=\"\" type=\"text\"></div>\n";
		echo "<div class=\"editstream-1\">Active</div><div class=\"editstream-2\"><input name=\"active\" type=\"checkbox\"></div>\n";
		echo "<div class=\"editstream-1\"></div><div class=\"editstream-2\"><input name=\"submit-new\" value=\"Add server\" type=\"submit\"></div>\n";
		echo "<input name=\"sid-new\" type=\"hidden\" value=\"true\">\n";
		echo "<div class=\"clear\"></div></form></div>\n";
		echo "";
	}
}
?>