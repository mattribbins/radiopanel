<?php
// RadioPanel - Iceast leave hook
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// Licenced under the BSD Simplfied licence (see licence.txt)

if(!file_exists("../config.php")) {
	die("Critical Error: No configuration found, <a href=\"./setup.php\">please run setup.</a>");	
}
require("../config.php");

session_start();
$db_session = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Get the POST data
$client = $db_session->real_escape_string($_POST['client']);
$duration = $db_session->real_escape_string($_POST['duration']);

// Update the record. If it doesn't exist, query fails, not the end of the world.
$db_session->query("UPDATE `clients` SET duration = '$duration', datetime_end = NOW() WHERE iceid='$client' ORDER BY cid DESC LIMIT 1");

// Bye!
$db_session->close();
?>