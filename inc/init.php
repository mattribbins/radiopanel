<?php
// RadioPanel - Init
// (C) Matt Ribbins - matt@mattyribbo.co.uk
function init($db_session) {
	// Do we need to update the database?

	// 1.0.5.0 - Add settings table, upgrade streams table to add server type
	$result = $db_session->query("SHOW TABLES LIKE 'settings';");
	if($result && !mysqli_num_rows($result)) {
		if(!$db_session->query("CREATE TABLE `settings` (setting VARCHAR(64) NOT NULL PRIMARY KEY, value VARCHAR(256) NOT NULL);")) {
			echo '<p class="error">Error: Upgrade to v2+ database FAILED. Check DB permissions, can we create tables?</p>';
		}

		if(!$db_session->query("INSERT INTO `settings` (`setting`, `value`) VALUES ('db', '1');")) {
			echo '<p class="error">Error: Unable to set up new settings DB. Quite honestly, this doesn\'t bode well...</p>';
		}
	}

	$db_version_res = mysqli_fetch_row($db_session->query("SELECT `value` FROM `settings` WHERE `setting`='db'"));
	$db_version = intval($db_version_res[0]);

	switch($db_version) {
		case 1:
		  // 1.0.5.0 - Upgrade streams table, add server type
			$db_session->query("ALTER TABLE `streams` ADD `type` INT(2)  NOT NULL  DEFAULT '1'  AFTER `server`;");
			$db_session->query("UPDATE `settings` SET `value` = '2' WHERE `setting` = 'db';");
	}

}
?>
