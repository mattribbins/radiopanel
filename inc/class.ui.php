<?php
// RadioPanel UI
// 
// Note: I should probably use tpl or something but that can happen another day.
class UiService
{
  public function displayHead($title,$redirect="") {
		echo "<!DOCTYPE html>\n";
		echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
		echo "<head>\n";
		echo "<base href=\"".$_SERVER['SERVER_NAME']."\">\n";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n";
		if($redirect) {
			echo "<meta http-equiv=\"refresh\" content=\"4;url=$redirect\">"; 
		}
		echo "<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/x-icon\" />\n";
		echo "<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
		echo "<title>RadioPanel - $title</title>\n";
		echo "</head>\n<body>\n";
	}
	
	public function displayHeader($title) {
		echo "<header>\n";
		echo "<h1>RadioPanel</h1>\n";
		echo "<h2>$title</h2>\n";
		echo "<nav>\n";
		$this->displayNavigation();
		echo "</nav>\n";
		echo "</header>\n";
	}
	
	public function displayNavigation() {
		echo "<ul><li><a href=\"./\">Home</a></li></ul>";
	}
	
	public function displayFooter() {
		echo "<!-- RadioPanel is provided free and open source. The least you can do is leave this linkback to the original author of this software. :) -->";
		echo "<footer><p>RadioPanel ".$GLOBALS['ver']."&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;<a href=\"http://www.mattyribbo.co.uk/radiopanel\">mattyribbo.co.uk</a></p><footer>\n";
		echo "</body>\n</html>";
	}
	
	public function displayLoginBox() {
		echo "<div class=\"loginbox\">\n<form action=\"index.php?login\" method=\"post\">";
		echo "<div class=\"loginbox-1\">Username</div><div class=\"loginbox-2\"><input name=\"username\" maxlength=\"32\" value=\"\" type=\"text\"></div>\n";
		echo "<div class=\"loginbox-1\">Password</div><div class=\"loginbox-2\"><input name=\"password\" maxlength=\"32\" value=\"\" type=\"password\"></div>\n";
		echo "<div class=\"loginbox-1\"></div><div class=\"loginbox-2\"><input name=\"submit\" value=\"Login\" type=\"submit\"></div>\n";
		echo "</form></div>\n";
	}
	
}
?>