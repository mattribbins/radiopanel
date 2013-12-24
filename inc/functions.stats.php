<?php
// RadioPanel -  Stats and Figures Functions
// (C) Matt Ribbins - matt@mattyribbo.co.uk
// 
// - stats_week()   - View statistics/figures per week
// - stats_search() - Search statistics/figures by hour/time period

function stats_week() {
	global $db_session;
	display_head("Week view");
	display_header("Week view");
	
	// Do we have a commence date?
	if(isset($_GET['date']) && $_GET['date'] != "") {
		// Get time frame
		$time_start = strtotime(($_GET['date']."00:00"));
		$time_end = $time_start + 604800;
		// Vars Init
		$hour = 0;
		$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		$days_count = 0;
		$peak_figure = 0;
		$peak_hour = 0;
		$peak_day = 0;
		$top; 
		$top_peak;

		// Output strings
		$output_avg = "";
		$output_peak = "";
		$graph_string_avg = "";
		$graph_string_peak = "";
		for($i = 0; $i < 10; $i++) {
			$top[$i]['figure'] = 0;
			$top[$i]['hour'] = 0;
			$top[$i]['day'] = 0;
			$top[$i]['more'] = 0;
		}
		for($i = 0; $i < 10; $i++) {
			$top_peak[$i]['figure'] = 0;
			$top_peak[$i]['hour'] = 0;
			$top_peak[$i]['day'] = 0;
			$top_peak[$i]['more'] = 0;
		}
		echo "<div class=\"row\">";
		echo "<div class=\"col-md-8\">\n<h3>Week commencing ".$_GET['date']."</h3>\n</div>\n<div class=\"col-md-4 week-view-sel\">";
		echo "<button type=\"button\" id=\"week-view-sel-peak\" name=\"week-view-sel-peak\" class=\"btn btn-default\">Peak</button>";
		echo "<button type=\"button\" id=\"week-view-sel-avg\" name=\"week-view-sel-avg\" class=\"btn btn-default\">Average</button>";
		echo "</div>";
		// Table of stats (yay)
		$output_avg = "<table class=\"table-week table-week-avg\">\n<thead><tr><td>Hour</td><td>0</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td></tr></thead>\n";
		$output_peak = "<table class=\"table-week table-week-peak\">\n<thead><tr><td>Hour</td><td>0</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td></tr></thead>\n";
		$output_avg .= "<tbody>\n<tr><td>".$days[0]."</td>";
		$output_peak .= "<tbody>\n<tr><td>".$days[0]."</td>";
		// Big loop to dump the data into a table we're going to generate. Go from start to end of week
		$time_sel = $time_start;
		while($time_sel < $time_end) {
			// Generate query (select the day)
			$time_sel_end = $time_sel + 3600;
			$result = $db_session->query("SELECT * FROM `figures` WHERE `timestamp`>'$time_sel' AND `timestamp`<'$time_sel_end';");
			if($result) {
				// We're in a valid hour slot, generate the figures
				$listeners;
				$plots = 0;
				$total = 0;
				$peak = 0;
				while($temp = mysqli_fetch_array($result)) {
					$time_hour = round(($temp['timestamp'] - $time_sel) / 60);
					$listeners[$time_hour] = $temp['listeners'];
					$plots++;
					$total += $listeners[$time_hour];
					if($listeners[$time_hour] > $peak) {
						$peak = $listeners[$time_hour];
					}
					if($listeners[$time_hour] > $peak_figure) {
						$peak_figure = $listeners[$time_hour];
						$peak_hour = $hour;
						$peak_day = $days_count;
						$peak = $listeners[$time_hour];
					}
				}
				$average = @round($total/$plots);
				if($average > 0 ) {
					$output_avg .= "<td onclick=\"weekViewZoom(".date("'Y-m-d', 'H'",$time_sel).")\">$average</td>";
					$graph_string_avg = $graph_string_avg."['".date("Y/m/d H:i",$time_sel)."',".$average."],";
				} else {
					// Hour doesn't exist, enter nothing.
					$output_avg .= "<td>&nbsp;</td>";
				}
				if($peak > 0) {
					$output_peak .= "<td onclick=\"weekViewZoom(".date("'Y-m-d', 'H'",$time_sel).")\">$peak</td>";
					$graph_string_peak .= "['".date("Y/m/d H:i",$time_sel)."',".$peak."],";
				} else {
					$output_peak .= "<td>&nbsp;</td>";
				}
				// Record top 5 average
				for($i = 0; $i < 10; $i++) {
					if($average > $top[$i]['figure']) {
						// Move everything down.
						for($j = 4; $j > $i; $j--) {
							$top[$j] = $top[$j-1];	
						}
						// New record
						$top[$i]['figure'] = $average;
						$top[$i]['hour'] = $hour;
						$top[$i]['day'] = $days_count;
						$top[$i]['more'] = 0;
						$i = 10;
					}
					else if($average == $top[$i]['figure']) {
						// Add 'more'
						$top[$i]['more']++;
						$i = 10;	
					}
				}
				// Record top 5 peak
				for($i = 0; $i < 10; $i++) {
					if($peak > $top_peak[$i]['figure']) {
						// Move everything down.
						for($j = 4; $j > $i; $j--) {
							$top_peak[$j] = $top_peak[$j-1];	
						}
						// New record
						$top_peak[$i]['figure'] = $peak;
						$top_peak[$i]['hour'] = $hour;
						$top_peak[$i]['day'] = $days_count;
						$top_peak[$i]['more'] = 0;
						$i = 10;
					}
					else if($peak == $top_peak[$i]['figure']) {
						// Add 'more'
						$top_peak[$i]['more']++;
						$i = 10;	
					}	
				}
			} else {
				// Hour doesn't exist, enter nothing.
				$output_avg .= "<td>&nbsp;</td>";
				$output_peak .= "<td>&nbsp;</td>";	
			}
			if((++$hour >= 24) && ($days_count < 6)) {
				// New day, new row
				$days_count++;
				$output_avg .= "</tr>\n<tr><td>".$days[$days_count]."</td>";
				$output_peak .= "</tr>\n<tr><td>".$days[$days_count]."</td>";
				$hour = 0;
			}
			$time_sel += 3600;
		}
		$output_avg .= "</tr>\n</tbody></table>\n";
		$output_peak .= "</tr>\n</tbody></table>\n";
		
		// Peak 
		echo "<div class=\"week-table-peak\">\n<div class=\"col-md-12\">$output_peak</div>";
		echo "<div class=\"col-md-3\">";
		echo "<p>Top ratings:";
		for($i = 0; $i < 10; $i++) {
			if($top_peak[$i]['figure'] > 0) {
				echo "<br /><span id=\"top-".($i+1)."-str\">".($i+1).": <span id=\"top-".($i+1)."\">".$top_peak[$i]['figure']."</span> - ".$days[$top_peak[$i]['day']]." at ".$top_peak[$i]['hour'].":00</span>";
				if($top_peak[$i]['more'] > 0) echo " (+".$top_peak[$i]['more'].")";
			}
		}
		echo "</p>\n";
		echo "</div>";
		echo "<div class=\"col-md-9\"><div class=\"search_chart_figures\" id=\"chart_figures_search_peak\"></div>\n";
		echo "<script type=\"text/javascript\">\n";
		echo "$(document).ready(function(){\n";
		echo "var plot_figures_peak = $.jqplot(\"chart_figures_search_peak\", [[";
		echo $graph_string_peak;
		echo "]], { axes: { xaxis: { label: \"Time (min)\", pad: 0, renderer: $.jqplot.DateAxisRenderer, showTicks: false }, yaxis: { label: \"Listeners\", min: 0 } }, highlighter: { show: true, tooltipAxes: 'both' }, seriesDefaults: { lineWidth: 2, markerOptions: { size: 4 } } });\n";
		echo "});\n";
		echo "</script>\n";
		echo "<noscript>Sorry, to see the graph you need a javascript enabled browser!</noscript>";
		echo "</div>\n</div>"; 
		// Average
		echo "<div class=\"week-table-avg\">\n<div class=\"col-md-12\">$output_avg</div>";
		echo "<div class=\"col-md-3\">";
		echo "<p>Top ratings:";
		for($i = 0; $i < 10; $i++) {
			if($top[$i]['figure'] > 0) {
				echo "<br /><span id=\"top-".($i+1)."-str\">".($i+1).": <span id=\"top-".($i+1)."\">".$top[$i]['figure']."</span> - ".$days[$top[$i]['day']]." at ".$top[$i]['hour'].":00</span>";
				if($top[$i]['more'] > 0) echo " (+".$top[$i]['more'].")";
			}
		}
		echo "</p>\n";
		echo "</div>";
		echo "<div class=\"col-md-9\"><div class=\"search_chart_figures\" id=\"chart_figures_search_avg\"></div>\n";
		echo "<script type=\"text/javascript\">\n";
		echo "$(document).ready(function(){\n";
		echo "var plot_figures_avg = $.jqplot(\"chart_figures_search_avg\", [[";
		echo $graph_string_avg;
		echo "]], { axes: { xaxis: { label: \"Time (min)\", pad: 0, renderer: $.jqplot.DateAxisRenderer, showTicks: false }, yaxis: { label: \"Listeners\", min: 0 } }, highlighter: { show: true, tooltipAxes: 'both' }, seriesDefaults: { lineWidth: 2, markerOptions: { size: 4 } } });\n";
		echo "});\n";
		echo "</script>\n";
		echo "<noscript>Sorry, to see the graph you need a javascript enabled browser!</noscript>";
		echo "</div>\n</div>"; 
		echo "<div id=\"week-view-dialog\"></div>";		
		echo "<hr />\n";
	} else {
		
	}
	echo "<div class=\"statweek\">\n";
	echo "<form action=\"./?page=week\" method=\"get\">\n";
	echo "<input name=\"page\" value=\"week\" type=\"hidden\">\n";
	echo "<div class=\"statsearch-1\"><div class=\"week-picker\"></div>";
	echo "<input name=\"date\" id=\"startDate\" type=\"hidden\" ";
	if(isset($_GET['week'])) {
		echo "value=\"".$_GET['week']."\"";	
	}
	echo "><input class=\"statweek-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only\" value=\"Display\" type=\"submit\"></div></form></div>\n";
	
	echo "</div>";
	
	display_footer();
	
}

function stats_search() {
	global $db_session;
	display_head("Search");
	display_header("Search");

	$date = $_GET['date'];
	if(isset($_GET['todate'])) { $todate = $_GET['todate']; } else { $todate = $date; }
	$hour = $_GET['time'];
	if(isset($_GET['totime'])) { $tohour = $_GET['totime']; } else { $tohour = $hour+1; }
	stats_search_display();
		
	// Display search form
	$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
	echo "<div class=\"search_stats_box\">\n";
	echo "<form action=\"./?page=search\" method=\"get\">\n";
	echo "<input name=\"page\" value=\"search\" type=\"hidden\">\n";
	echo "<div class=\"statsearch-1\">Search by Date / Hour<br /><input name=\"date\" id=\"search-date\" maxlength=\"32\" type=\"text\" ";
	if(isset($date)) {
		echo "value=\"$date\"";
	} else {
		echo "value=\"".date("Y-m-d")."\"";
	}
	echo ">";
	echo "<select name=\"time\" id=\"search-time\">";
	foreach($hours as $myhour) {
		if($myhour == $hour) {
			echo "<option value=\"$myhour\" selected=\"selected\">$myhour:00</option>";
		} else {
			echo "<option value=\"$myhour\">$myhour:00</option>";
		}
	}
	echo "</select>";
	echo "&nbsp; &nbsp; to &nbsp; &nbsp;"; 
		echo "<input name=\"todate\" id=\"search-dateto\" maxlength=\"32\" type=\"text\" ";
	if(isset($todate)) {
		echo "value=\"$date\"";
	} else {
		echo "value=\"".date("Y-m-d")."\"";
	}
	echo ">";
	echo "<select name=\"totime\" id=\"search-timeto\">";
	foreach($hours as $myhour) {
		if($myhour == $tohour) {
			echo "<option value=\"$myhour\" selected=\"selected\">$myhour:00</option>";
		} else {
			echo "<option value=\"$myhour\">$myhour:00</option>";
		}
	}
	echo "</select>";
	echo "<input name=\"submit\" value=\"Search\" type=\"submit\" class=\"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only\"></div>\n";
	echo "<div class=\"clear\"></div></form></div>\n";
	
	display_footer();
}

function stats_search_display() {
	global $db_session;
	if(isset($_GET['date']) && isset($_GET['time'])) {
		// Get timeframe
		$date = $_GET['date'];
		if(isset($_GET['todate'])) { $todate = $_GET['todate']; } else { $todate = $date; }
		$hour = $_GET['time'];
		if(isset($_GET['totime'])) { $tohour = $_GET['totime']; } else { $tohour = $hour+1; }
		$timestamp_start = strtotime("$date $hour:00");
		$timestamp_end = strtotime("$todate $tohour:01");
		$timestamp_diff = ($timestamp_end - $timestamp_start);
		$peak = 0;
		$average = 0;
		
		echo "<h3>Figures for $date $hour:00 to $todate $tohour:00</h3>\n";
		$result = $db_session->query("SELECT * FROM `figures` WHERE `timestamp`>'$timestamp_start' AND `timestamp`<'$timestamp_end';");
		if($result) {
			// We have listening figures
			$listeners;
			$total = 0;
			$plots = 0;
			for($i = 0; $i < $timestamp_diff; $i++) {
				$listeners[$i] = 0;
			}
			// Get each result within the timeframe, add them to the array.
			while($temp = mysqli_fetch_array($result)) {
				$timestamp = round(($temp['timestamp'] - $timestamp_start) / 60);
				$listeners[$timestamp] = $temp['listeners'];
				$plots++;
				$total += $listeners[$timestamp];
				if($listeners[$timestamp] > $peak) {
					$peak = $listeners[$timestamp];
				}
			}
			$average = round($total/$plots);
			// Generate jqGraph
			echo "<div class=\"search_chart_figures\" id=\"chart_figures_search\"></div>\n";
			echo "<script type=\"text/javascript\">\n";
			echo "$(document).ready(function(){\n";
			echo "var plot_figures = $.jqplot(\"chart_figures_search\", [[";
			for($i = 0; $i < $timestamp_diff; $i++) {
				if($listeners[$i]) echo "[".$i.",".$listeners[$i]."],";
			}
			echo "]], { axes: { xaxis: { label: \"Time (min)\", pad: 0}, yaxis: { label: \"Listeners\", min: 0 } }, highlighter: { show: true, tooltipAxes: 'y' } });\n";
			echo "});\n";
			echo "</script>\n";
			echo "<noscript>Sorry, to see the graph you need a javascript enabled browser!</noscript>";
			echo "<div class=\"search_stats\">";
			// Display statistics
			echo "<br /><h4>Statistics</h4>";
			echo "<p>Peak: $peak</p><p>Average: $average</p>";
			echo "</div>";
			
		}
		echo "<hr />\n";
	}
}
?>