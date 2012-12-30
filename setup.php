<?php
// RadioPanel -  Setup File
// (C) Matt Ribbins - matt@mattyribbo.co.uk
//
// This file must be deleted once completed!

include("inc/class.db.php");
include("inc/class.user.php");

echo "<!DOCTYPE html>\n";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
echo "<head>\n";
echo "<base href=\"".$_SERVER['SERVER_NAME']."\">\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
echo "<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\" />\n";
echo "<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<link href=\"lib/grid.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<link href=\"lib/jquery-ui-1.9.2.custom.min.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<title>RadioPanel Setup </title>\n";
echo "<script src=\"lib/jquery-1.8.3.min.js\"></script>\n";
echo "<script src=\"lib/jquery-ui-1.9.2.custom.min.js\"></script>\n";
echo "<script src=\"scripts.js\"></script>\n";
echo "</head>\n<body><div id=\"main\" class=\"container_12\">\n";

if(isset($_POST['submit'])) {

}
if(isset($_POST['submit']) && ($_POST['setup'] == 1)) {
	// Database
	$db_host = $_POST['db_host'];
	$db_name = $_POST['db_name'];
	$db_user = $_POST['db_user'];
	$db_pass = $_POST['db_pass'];
	$db_config = array(	
		'host'      => $db_host,
		'username'  => $db_user,
		'database'  => $db_name,
		'password'  => $db_pass	
	);
	$db_session = new Database($db_config);	
	echo "<h1>RadioPanel Setup - Step 2</h1>\n";
	echo "<p>Radiopanel will now attempt to connect to the database and perform initial setup</p>";
	do {
		// Initial connect
		echo "<p>Connecting to database $db_host</p>";
		if(!$db_session->connect()) {
			echo "<p class=\"error\">Error: Unable to connect to database. Incorrect details were provided or MySQL server does not exist</p>";
			break;
		}
		echo "<p>Connected</p>";
		// If creating database, create
		if(isset($_POST['db_create'])) {
			echo "<p>Creating database: CREATE DATABASE `radiopanel`</p>";
			if(!$db_session->query("CREATE DATABASE `radiopanel` /*!40100 CHARACTER SET utf8 COLLATE 'utf8_general_ci' */;")) {
				echo "<p class=\"error\">Error: Unable to create database `radiopanel`. Either you do not have the rights to add databases or database already exists</p>";
				break;
			}
		}
		// Create tables
		echo "<p>Creating user table: CREATE TABLE `users`</p>";
		if(!$db_session->query("CREATE TABLE `users` (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `username` VARCHAR(50) NOT NULL DEFAULT '0', `password` VARCHAR(255) NOT NULL DEFAULT '0', `salt` VARCHAR(127) NOT NULL DEFAULT '0', `email` VARCHAR(127) NOT NULL DEFAULT '0', `access` VARCHAR(2) NOT NULL DEFAULT '0', primary key (`id`)) COMMENT='Holds user accounts' COLLATE='utf8_general_ci' ENGINE=InnoDB;")) {
			echo "<p class=\"error\">Error: Unable to create table users</p>";
			break;
		}
		echo "<p>Creating stream table: CREATE TABLE `streams`</p>";
		if(!$db_session->query("CREATE TABLE `streams` (`sid` SMALLINT UNSIGNED NULL AUTO_INCREMENT, `name` VARCHAR(64) NULL DEFAULT '0', `server` VARCHAR(256) NULL DEFAULT '0', `username` VARCHAR(64) NULL DEFAULT '0', `password` VARCHAR(64) NULL DEFAULT '0', `mountpoint` VARCHAR(64) NULL DEFAULT '0', `active` TINYINT UNSIGNED NULL DEFAULT '0', PRIMARY KEY (`sid`)) COMMENT='Holds the list of streams' COLLATE='utf8_general_ci' ENGINE=InnoDB;")) {
			echo "<p class=\"error\">Error: Unable to create table streams</p>";
			break;	
		}
		echo "<p>Creating figures table: CREATE TABLE `figures`</p>"; 
		if(!$db_session->query("CREATE TABLE `figures` (`fid` INT(16) UNSIGNED NULL AUTO_INCREMENT, `timestamp`  INT(10) UNSIGNED NULL DEFAULT NULL, `listeners` MEDIUMINT UNSIGNED NULL, PRIMARY KEY (`fid`)) COMMENT='Holds total listener figures' COLLATE='utf8_general_ci' ENGINE=InnoDB;")) {
			echo "<p class=\"error\">Error: Unable to create table figures</p>";
		}
		// Setup config.php
		echo "<p>Attempting to write config.php</p>";
		do {
			if(!$fp = fopen('config.php', 'w')) {
				echo "<p class=\"error\">Error: Unable to open config.php</p>";	
				break;
			}
			if(fwrite($fp, "<?php\n// RadioPanel - Configuration\n\n// Database\n// Hostname\n\$db_host = '$db_host';\n// Username\n\$db_user = '$db_user';\n// Database name\n\$db_name = '$db_name';\n// Password\n\$db_pass = '$db_pass';") === false) {
				echo "<p class=\"error\">Error: Unable to write to config.php</p>";	
				break;
			}
			fclose($fp);
			$config_write = true;
		} while(0);
		if($config_write === true) {
			echo "<p>Configuration successfully saved. Click 'Next' to proceed</p>";
		} else {
			echo "<p class=\"error\">Unable to save config.php</p><p>You will need to manually save and upload config.php with the lines below, or modify config.sample.php and rename config.php</p>";
			echo "<textarea rows=\"15\" cols=\"60\"><?php\n// RadioPanel - Configuration\n\n// Database\n// Hostname\n\$db_host = '$db_host';\n// Username\n\$db_user = '$db_user';\n// Database name\n\$db_name = '$db_name';\n// Password\n\$db_pass = '$db_pass';</textarea>";
		}
		echo "<form name=\"setup\" action=\"./setup.php\" method=\"post\"><input name=\"setup\" type=\"hidden\" value=\"2\"><input type=\"submit\" name=\"submit\" value=\"Next\"></form>";
		//
	} while(0);
}
else if(isset($_POST['submit']) && ($_POST['setup'] == 2)) {
	echo "<h1>RadioPanel Setup - Step 2</h1>";
	do {
		// Connect to database using config file we just set up.
		include("config.php");
		$db_config = array(	
			'host'      => $db_host,
			'username'  => $db_user,
			'database'  => $db_name,
			'password'  => $db_pass	
		);
		$db_session = new Database($db_config);
		$db_session->connect();
		// Promot for new admin user account.
		echo "<p>Database has now been set up successfully. Please create an admin user account.</p>";
		echo "<h2>Account setup</h2>";
		echo "<form name=\"setup\" action=\"./setup.php\" method=\"post\"><table>\n";
		echo "<tr><td>Username</td><td><input name=\"user\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
		echo "<tr><td>Password</td><td><input name=\"pass\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
		echo "<tr><td>Email Address</td><td><input name=\"email\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
		echo "<input name=\"setup\" type=\"hidden\" value=\"3\">";
		echo "<tr><td></td><td><input type=\"submit\" name=\"submit\" value=\"Submit\"></td></tr>";
		echo "</table></form>";
	} while(0);
} else if(isset($_POST['submit']) && ($_POST['setup'] == 3)) {
	$add_user = $_POST['user'];
	$add_pass = $_POST['pass'];
	$add_email = $_POST['email'];
	$config_write = false;
	echo "<h1>RadioPanel Setup - Step 3</h1>";
	do {
		include("config.php");
		$db_config = array(	
			'host'      => $db_host,
			'username'  => $db_user,
			'database'  => $db_name,
			'password'  => $db_pass	
		);
		$db_session = new Database($db_config);
		if(!$db_session->connect()) {
			echo "<p class=\"error\">Error: Unable to connect to database. Incorrect details were provided or MySQL server does not exist</p>";
			break;
		}
		// Add user account to database
		$add_user = mysql_real_escape_string($add_user);
		$add_pass = mysql_real_escape_string($add_pass);
		$add_email = mysql_real_escape_string($add_email);
		$user = new UserService($db_session);
		$result = $user->registerUser($add_user, $add_pass, $add_email, 99);
		if(!$result) {
			echo "<p class=\"error\">Error: Unable to add user account.</p>";	
			break;
		}
		echo "<p>User account added</p>";
		echo "<form name=\"setup\" action=\"./setup.php\" method=\"post\"><input name=\"setup\" type=\"hidden\" value=\"4\"><input type=\"submit\" name=\"submit\" value=\"Next\"></form>";
	} while(0);
} else if(isset($_POST['submit']) && ($_POST['setup'] == 4)) {
	echo "<h1>RadioPanel Setup - Step 4</h1>";
	do {
		// Promot user to set up cron job and delete this file.
		echo "<h2>Cron Job</h2>";
		echo "<p>For RadioPanel to work, you will need to setup a cron job (or scheduled task) to run periodically.<br />We recommend that the cron job is run every minute, but you may want to run less frequently.</p>";
		echo "<p>Setup cannot set this up, you need to do this manually. See the example commands below</p>";
		echo "<br /><p class=\"code\">crontab -e<br /><br />* * * * * php ".$_SERVER['DOCUMENT_ROOT']."/index.php cron_stream</p>";
		echo "<p>If you cannot run the cron job on the same server, you can do this from a remote server by calling the URL ".$_SERVER['SERVER_NAME']."/index.php?task=cron_stream.</p><br />";
		
		echo "<h2>Delete setup file</h2>";
		echo "<p>Before you can use RadioPanel, you will need to delete this setup file. Click 'Finish' below to delete the file and be taken to the RadioPanel homepage</p>";
		echo "<form name=\"setup\" action=\"./setup.php\" method=\"post\"><input name=\"setup\" type=\"hidden\" value=\"5\"><input type=\"submit\" name=\"submit\" value=\"Finish\"></form>";
	} while(0);
} else if(isset($_POST['submit']) && ($_POST['setup'] == 5)) {
	echo "<h1>RadioPanel Setup - Step 5</h1>";
	do {
		if(!unlink("setup.php")) {
			echo "<p class=\"error\">Error: Unable to delete setup.php. Please remove the setup.php file manually.</p>";
			echo "<a href=\"".$_SERVER['SERVER_NAME']."\">Click here to go to RadioPanel home</a>";
		} else {
			echo "<p>Redirecting...</p>";
			echo "<script type=\"text/javascript\">window.location.replace(\"./\");</script><noscript><a href=\"./\">Click here</a></noscript>";

		}
	} while(0);
} else {
	echo "<h1>RadioPanel Setup - Step 1</h1>\n";
	echo "<p>Welcome to RadioPanel setup. Please enter your MySQL details.</p>\n";
	echo "<form name=\"setup\" action=\"./setup.php\" method=\"post\"><table>\n";
	echo "<tr><td>Database Host</td><td><input name=\"db_host\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
	echo "<tr><td>Database Name</td><td><input name=\"db_name\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
	echo "<tr><td>Database Username</td><td><input name=\"db_user\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
	echo "<tr><td>Database Password</td><td><input name=\"db_pass\" type=\"text\" maxlength=\"254\" size=\"40\" value=\"\"></td></tr>";
	echo "<tr><td>Create database</td><td><input name=\"db_create\" type=\"checkbox\"> <strong>Note:</strong> the next step will fail if the database already exists.</td></tr>";
	echo "<input name=\"setup\" type=\"hidden\" value=\"1\">";
	echo "<tr><td></td><td><input type=\"submit\" name=\"submit\" value=\"Submit\"></td></tr>";
	echo "</table></form>";
}

echo "</div></body></html>";


?>