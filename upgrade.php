<?php
if(!file_exists("./config.php")) {
	die("Critical Error: No configuration found, <a href=\"./setup.php\">please run setup.</a>");	
}

require("config.php");


$db_session = new mysqli($db_host, $db_user, $db_pass, $db_name);

echo "<h1>Upgrading database from 1.0 to 1.1</h1>";

echo "<p>Creating clients table: CREATE TABLE `clients`</p>";
if(!$db_session->query("CREATE TABLE IF NOT EXISTS `clients` (`cid` int(10) NOT NULL AUTO_INCREMENT, `iceid` int(12) NOT NULL, `sid` int(10) NOT NULL, `server` varchar(15) NOT NULL, `mount` varchar(80) NOT NULL, `agent` varchar(255) NOT NULL, `referrer` varchar(512) DEFAULT NULL, `ip` varchar(20) NOT NULL, `city` varchar(30) DEFAULT NULL, `country` varchar(20) DEFAULT NULL, `duration` int(11) DEFAULT NULL, `datetime_start` datetime NOT NULL, `datetime_end` datetime DEFAULT NULL, PRIMARY KEY (`cid`), UNIQUE KEY `cid` (`cid`)) COLLATE='utf8_general_ci' ENGINE=InnoDB;")) {
	echo "<p class=\"error\">Error: Unable to create table clients</p>";
	break;	
}
echo "<p>Creating settings table: CREATE TABLE `settings`</p>";
if(!$db_session->query("CREATE TABLE IF NOT EXISTS `settings` (`setting` varchar(50) NOT NULL, `value` varchar(100) NOT NULL, UNIQUE KEY `setting` (`setting`)) COLLATE='utf8_general_ci' ENGINE=InnoDB;")) {
	echo "<p class=\"error\">Error: Unable to create table settings</p>";
	break;	
}
if(!$db_session->query("INSERT INTO `settings` (`setting`, `value`) VALUES ('ver', '1.1.0.1');")) {
	echo "<p class=\"error\">Error: Unable to populate settings</p>";
	break;	
}	


$db_session->close();

if(!unlink("upgrade.php")) {
	echo "<p class=\"error\">Error: Unable to delete upgrade.php. Please remove the upgrade.php file manually!</p>";
	echo "<a href=\"".$_SERVER['SERVER_NAME']."\">Click here to go to RadioPanel home</a>";
} else {
	echo "<p>Upgrade successful...</p>";
	echo "<script type=\"text/javascript\">window.location.replace(\"./\");</script><noscript><a href=\"./\">Click here</a></noscript>";

}
	
?>