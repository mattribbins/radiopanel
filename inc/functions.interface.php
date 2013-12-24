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
	?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <base href="".$_SERVER['SERVER_NAME']."">
    <meta http-equiv="Content-Type" content="text/html charset=utf-8" />
    <?php
    if($redirect) {
		echo "<meta http-equiv=\"refresh\" content=\"1;url=$redirect\">"; 
	}
	?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link href="style.css" rel="stylesheet" type="text/css" />
    <link href="lib/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="lib/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
    <link href="lib/jquery-ui-1.9.2.custom.min.css" rel="stylesheet" type="text/css" />
    <link href="lib/jquery.jqplot.min.css" rel="stylesheet" type="text/css" />
    <link href=\"lib/jquery.ui.timepicker.css" rel="stylesheet" type="text/css" />


    <title>RadioPanel - <?php echo $title; ?></title>
    <script src="lib/jquery-1.8.3.min.js"></script>
    <script src="lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="lib/bootstrap.min.js"></script>
    <!--[if lt IE 9]><script language=\"javascript\" type=\"text/javascript\" src=\"lib/excanvas.js\"></script><![endif]-->
	<script type="text/javascript" src="lib/jquery.jqplot.min.js"></script>;
	<script type="text/javascript" src="lib/plugins/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="lib/plugins/jqplot.cursor.min.js"></script>
	<script type="text/javascript" src="lib/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script src="scripts.js"></script>

</head>
<body>

<div id="wrap">
<?php
}
	
function display_header($title) {
	global $user_session;
	$access = $user_session->getUserAccess();
	
	?>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="./">RadioPanel</a>
		</div>
		<div class="collapse navbar-collapse">
			<?php display_navigation($access); ?>
		</div><!--/.nav-collapse -->
	</div>
</div>
    
<div class="container">

	<?php
}
	
function display_navigation($access = 0) {
	global $user_session;
	
	echo "<ul class=\"nav navbar-nav\">";
	echo "<li><a href=\"./\">Home</a></li>";
	if($access) {
		//echo "<li><a href=\"./?page=live\">Live</a></li>";
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
	?>
    
    </div>

</div>
<div id="footer">
    <div class="container">
    	<!-- Don't remove the link back. Or you can feel guilty. Your choice. -->
   		<p class="text-muted">Powered by RadioPanel<?php echo _VER; ?> &nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;<a href="http://www.mattyribbo.co.uk/radiopanel">mattyribbo.co.uk</a>&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;<a href="./?page=credits">Credits</a></p>
    </div>
</div>

</body>
</html>
	
	<?php
}

function display_loginbox() {
	?> 
    <form class="form-signin" action="index.php?page=login" method="post">
    <h2 class="form-signin-heading">Please sign in</h2>
	<input name="username" maxlength="32" value="" type="text" placeholder="Username" class="form-control" reqired autofocus>
	<input name="password" maxlength="32" value="" type="password" placeholder="Password" class="form-control" required>
    <label class="checkbox"><input type="checkbox" value="remember-me">Remember me</label>
	<input class="btn btn-lg btn-primary btn-block" name="submit" value="Login" type="submit">
	</form>
    <?php
}

function display_homepage() {
	display_head("Home");
	display_header("Home");
	echo "<h3>Welcome to RadioPanel</h3>\n";
	// Display live figures
	echo "<h4>Live figures</h4>";
	echo "<div id=\"streamstats\">";
	stream_getlivestats();
	echo "</div><br />";
	echo "<p style=\"font-style:italic;\">Note: figures are live and will automatically update<br />Do <strong>not</strong> disclose live figures on-air under any circumstances!</p><br /><a href=\"#\" id=\"dialog_link\"  class=\"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only\"><span class=\"ui-icon ui-icon-newwin\"></span>Open live stats in a popup window</a>\n";
	display_footer();
}


function display_credits() {
	display_head();
	display_header();
	?>
    <p>RadioPanel version <?php echo _VER; ?></p>
    <p>Developed by <a href="http://www.mattyribbo.co.uk" target="_blank">Matt Ribbins</a> for Hub Radio, station at UWE Students' Union, University of the West of England, Bristol.</p>
    <p>Licenced under the <a href="./LICENCE.txt">BSD Simplified Licence</a></p>
    <p>RadioPanel uses the following packages:</p>
    <ul>
    	<li>Icons from the <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">Silk Icon Pack by famfamfam.com</a></li>
        <li><a href="http://www.getbootstrap.com" target="_blank">Bootstrap</a> interface from Twitter</li>
        <li>jQuery & jQuery UI, licenced under the MIT Licence.</li>
	    <li>jqGraph, licenced under the MIT Licence.</li>
	</ul>
    <?php
	display_footer();
}
?>