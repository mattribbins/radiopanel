<?php
// RadioPanel
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// Licenced under the BSD Simplfied licence (see licence.txt)
//
// Refer to the ReadMe file (readme.txt) for installation instructions.

// Error reporting (used for debugging. I don't suggest turning those on)
error_reporting(0);
ini_set("display_errors", "0"); 

// Includes 
require("config.php");
require("inc/init.php");
require("inc/functions.php");
require("inc/functions.stream.php");
require("inc/functions.stats.php");
require("inc/functions.users.php");
require("inc/functions.interface.php");
require("inc/class.db.php");
require("inc/class.stream.php");
require("inc/class.user.php");

// Defines
define('_VER', '1.0b2');

// Initialisation
session_start();
$db_config = array(	
	'host'      => $db_host,
	'username'  => $db_user,
	'database'  => $db_name,
	'password'  => $db_pass	
);
// Delete setup.php if for some reason it exists. If this file cannot be deleted, die!
if(file_exists("setup.php")) {
	if(!unlink("setup.php")) {
		die("Critical Error: setup.php is still present! You must remove this file before using RadioPanel!");	
	}
}
$db_session = new Database($db_config);
$db_session->connect();
$user_session = new UserService($db_session);
$user_session->init();

// Check if we're a web page or using the cli (i.e. for cron)
if ((isset($_GET['page']) && ($_GET['page']) != "")) {
        $page = $_GET['page'];
} else {
        $page = $_SERVER['argv'][1];
}


// Choose the page determined by ?page=etc
// Pages only available if logged in
if($user_session->isLoggedIn()) {
	switch($page) {
		case "logout":
			$user_session->logout();
			header("Location: ./");
			break;
		case "live":
			stream_livestats();
			break;
		case "search":
			stats_search();
			break;
		case "cron_stream":
			stream_recordcron();
			break;
		// User restricted pages below
		case "week":
			if($user_session->getUserAccess() >= 20) {
				stats_week();
				break;
			}
		case "streams":
			if($user_session->getUserAccess() >= 30) {
				stream_interface();
				break;	
			}
		case "users":
			if($user_session->getUserAccess() >= 40) {
				user_interface();
				break;
			}
		case "api":
			api_call();
			break;
		case "credits":
			display_credits();
			break;
		default:
			display_head("Home");
			display_header("Home");
			echo "Hello";
			display_footer();
			break;
	}
} else {
	switch($page) {
		case "cron_stream":
			stream_recordcron();
			break;
		case "credits":
			display_credits();
			break;
		case "login":
			$user_session->login();
			header("Location: ./");
			break;
		default:
			display_head("Home");
			display_header("Home");
			echo "RadioPanel - Please log in<br />";
			display_loginbox();
			display_footer();
			break;
	}
}


?>