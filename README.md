RadioPlayer
===========
by Matt Ribbins (mattribbins.co.uk)
Current release: 1.0.5.0

Important
---------
This project is no longer actively maintained and needs a complete overhaul. Feel free to fork but no support or changes will be given.

Requirements
------------
 * PHP 5.4.x or above
 * MySQL/MariaDB Server
 * Icecast2 or ShoutCast DNASv2 Server(s)
 * Ability to run cron jobs either on the same server or remotely.

What?
-----
RadioPanel is a panel used for recording listener figures and displays them either in an hourly overview or week overview format. Originally created for [Hub Radio](http://www.hubradio.co.uk)

Installation
------------
Download RadioPanel to your PHP enabled webserver. Run setup.php from your web browser to run the install script and follow the instructions.

Warning
-------
RadioPanel in its current form is very basic. There is much more that could be done to RadioPanel such as more advanced listener metadata, and better displaying of analytics.

RadioPanel was also initially written back in 2012. As such, it is using some deprecated functions and old and INSECURE MySQL functions. Your use of RadioPanel is at your own risk, as mentioned in the licence.

Help!
-----
This project is no longer supported and maintaned.


Background
----------
RadioPanel was originally created for use by my university radio station, [Hub Radio](http://www.hubradio.co.uk). I am currently part of the technical team, and at one point was the Station Manager during 2012.

We wanted a tool to be able to record and view listener figures. Previously we had listening logging features from the stream provider using the proprietory CentovaCast panel. It wasn't perfect, since we were limited to graphs. After moving our streams internally we did not have access to CentovaCast. A temporary fix was using Munin via the [icecast2-munin plugin](http://www.github.com/mattyribbo/icecast2-munin) I wrote for Munin.

The goal for RadioPanel was to be able to log combined listener figures from multiple sources, and be able to view the figures either in an hourly window with a graph, average and peak listeners, or in a week overview with the top five hourly slots. RadioPanel should be able to add/remove user accounts and streams with ease.

To-do
-----
- Rewrite!
- Full screen interface
- Documentation (phpDocumentor may prove useful)
