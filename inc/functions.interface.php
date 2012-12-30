<?php
// RadioPanel - Interface
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// 
// - display_head()       - Displays head (<head>) and starts main div
// - display_header()     - Displays header (<header>)
// - display_navigation() - Displays navigation
// - display_footer()     - Displays footer (after main div)
// - display_credits()    - Displays credits

// Note: I should probably use tpl or something but that can happen another day.
function display_head($title,$redirect="") {
	echo "<!DOCTYPE html>\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	echo "<head>\n";
	echo "<base href=\"".$_SERVER['SERVER_NAME']."\">\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
	if($redirect) {
		echo "<meta http-equiv=\"refresh\" content=\"1;url=$redirect\">"; 
	}
	echo "<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\" />\n";
	echo "<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	echo "<link href=\"lib/grid.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	echo "<link href=\"lib/jquery-ui-1.9.2.custom.min.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	echo "<link href=\"lib/jquery.ui.timepicker.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	echo "<link href=\"lib/jquery.jqplot.min.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	echo "<title>RadioPanel - $title</title>\n";
	echo "<script src=\"lib/jquery-1.8.3.min.js\"></script>\n";
	echo "<script src=\"lib/jquery-ui-1.9.2.custom.min.js\"></script>\n";
	echo "<!--[if lt IE 9]><script language=\"javascript\" type=\"text/javascript\" src=\"lib/excanvas.js\"></script><![endif]-->\n";
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"lib/jquery.jqplot.min.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"lib/plugins/jqplot.highlighter.min.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"lib/plugins/jqplot.cursor.min.js\"></script>\n";
	echo "<script type=\"text/javascript\" src=\"lib/plugins/jqplot.dateAxisRenderer.min.js\"></script>\n";
	echo "<script src=\"scripts.js\"></script>\n";
	echo "</head>\n<body><div id=\"main\" class=\"container_12\">\n";
}
	
function display_header($title) {
	global $user_session;
	$access = $user_session->getUserAccess();
	
	echo "<header class=\"container_12\">\n";
	echo "<h1>RadioPanel</h1>\n";
	echo "<nav>\n";
	display_navigation($access);
	echo "</nav>\n";
	echo "<h2>$title</h2>\n";
	echo "</header>\n";
	echo "<div id=\"content\" class=\"container_12\">\n";
}
	
function display_navigation($access = 0) {
	global $user_session;
	echo "<ul>";
	echo "<li><a href=\"./\">Home</a></li>";
	if($access) {
		echo "<li><a href=\"./?page=live\">Live</a></li>";
		if($access >= 10) {
			echo "<li><a href=\"./?page=search\">Search</a></li>";
		}
		if($access >= 20) {
			echo "<li><a href=\"./?page=week\">Week View</a></li>";
			echo "<li><a href=\"./?page=stats\">Stats</a></li>";
		}
		if($access >= 30) {
			echo "<li><a href=\"./?page=streams\">Streams</a></li>";	
		}
		if($access >= 40) {
			echo "<li><a href=\"./?page=users\">Users</a></li>";
		}
		echo "<li><a href=\"./?page=logout\">Logout</a></li>";
	} else {
		echo "<li><a href=\"./\">Login</a></li>";
	}
	echo "</ul>\n";
}
	
function display_footer() {
	echo "<div class=\"clear\"></div>\n</div>\n";
	echo "<!-- RadioPanel is provided free and open source. The least you can do is leave this linkback to the original author of this software. :) -->\n";
	echo "<footer class=\"container_12\"><div class=\"grid_12\"><p>RadioPanel "._VER."&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;<a href=\"http://www.mattyribbo.co.uk/radiopanel\">mattyribbo.co.uk</a>&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;<a href=\"./?page=credits\">Credits</a></p></div><footer>\n";
	echo "</div></body>\n</html>";
}

function display_loginbox() {
	echo "<div class=\"loginbox\">\n<form action=\"index.php?page=login\" method=\"post\">";
	echo "<div class=\"loginbox-1\">Username</div><div class=\"loginbox-2\"><input name=\"username\" maxlength=\"32\" value=\"\" type=\"text\"></div>\n";
	echo "<div class=\"loginbox-1\">Password</div><div class=\"loginbox-2\"><input name=\"password\" maxlength=\"32\" value=\"\" type=\"password\"></div>\n";
	echo "<div class=\"loginbox-1\"></div><div class=\"loginbox-2\"><input name=\"submit\" value=\"Login\" type=\"submit\"></div>\n";
	echo "</form></div>\n";
}
	
function display_credits() {
	display_head();
	display_header();
	echo "<p>RadioPanel by Matt Ribbins (<a href=\"http://www.mattyribbo.co.uk\">mattyribbo.co.uk</a>)</p><br />\n";
	echo "<p>RadioPanel uses the following:</p>\n";
	echo "<p> - Icons from the <a href=\"http://www.famfamfam.com/lab/icons/silk/\">Silk Icon Pack by famfamfam.com</a></p>\n";
	echo "<p> - jQuery & jQuery UI, licenced under the MIT Licence.</p>\n";
	echo "<p> - jqGraph, licenced under the MIT Licence.</p>\n";
	echo "<p> - 960 Grid System, licenced under the MIT Licence.</p>\n";
	echo "<br /><p>Originally developed for <a href=\"http://www.hubradio.co.uk\">Hub Radio</a>, student radio station at UWE Students' Union, University of the West of England, Bristol.</p>\n";
	display_footer();
}
?>