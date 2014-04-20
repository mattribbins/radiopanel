<?php
// RadioPanel - Iceast join hook
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// Licenced under the BSD Simplfied licence (see licence.txt)

// We need to confirm with icecast that the "authentication" is good.
header("icecast-auth-user: 1");


if(!file_exists("../config.php")) {
	die("Critical Error: No configuration found, <a href=\"./setup.php\">please run setup.</a>");	
}
require("../config.php");

session_start();
$db_session = new mysqli($db_host, $db_user, $db_pass, $db_name);

$client = $db_session->real_escape_string($_POST['client']);
$ip = $db_session->real_escape_string($_POST['ip']);
$mount = $db_session->real_escape_string($_POST['mount']);
$agent = $db_session->real_escape_string($_POST['agent']);
$referrer = $db_session->real_escape_string($_POST['referrer']);
$server = $db_session->real_escape_string($_POST['server']);
$port = $db_session->real_escape_string($_POST['port']);


$result = $db_session->query("SELECT * FROM `streams` WHERE `server`='$server' AND `mountpoint`='$mount' AND `active`='1';");
if($result) {
	// Valid server to record data into
	while($server_info = mysqli_fetch_array($result)) {
		$sid = $server_info['sid'];
	}
	
	// Get location details
	$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
	$city = $details->city;
	$country = $details->country;
	
	// Update the DB
	$query = "INSERT INTO `clients` (`iceid`, `sid`, `ip`, `city`, `country`, `server`, `mount`, `agent`, `referrer`, `datetime_start`) VALUES ('$client', '$sid', '$ip', '$city', '$country', '$server', '$mount', '$agent', '$referrer', NOW())";
	$db_session->query($query);
}

// Bye!
$db_session->close();
?>