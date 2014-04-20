RadioPanel Icecast Stats
===========
by Matt Ribbins (mattyribbo.co.uk)
Current release: 1.1.0.1

Requirements
------------
 * PHP 5.4.x or above
 * MySQL Server
 * Icecast2 Server(s)
 * Ability to run cron jobs either on the same server or remotely.
 
What's new in 1.1?
------------------
With 1.1, I'm making advantage of the information that Icecast can provide through client connect and disconnect hooks. 

Upgrading from 1.0 to 1.1
-------------------------
If you are upgrading from 1.0 to 1.1, please run upgrade.php since there are database changes! This will be a one-time process, any future updates to the database will not need upgrade.php (hopefully). 



What is RadioPanel?
-----
RadioPanel is a panel used for recording and reviewing listener statistics. Originally created for use in [Hub Radio](http://www.hubradio.co.uk)

Installation
------------
Download RadioPanel to your PHP enabled webserver. Run setup.php from your web browser to run the install script and follow the instructions.

Warning
-------
RadioPanel is pretty much a work in progress. Expect things to change, things to break, things to not work on unique setups. If you do find any bugs and fix them, please share!
 
Help!
-----
If you're stuck getting RadioPanel working or notice something drastically wrong, you're welcome to email or tweet me. 
Email matt@mattyribbo.co.uk or tweet @mattyribbo
I do aim to respond as quickly as I can. If I respond but don't hear back from me and still have a problem, do continue to poke me.

Note
----
I've not done much PHP for a while, so excuse the rustiness :)

Background
----------
RadioPanel was originally created for use by my university radio station, [Hub Radio](http://www.hubradio.co.uk). I am currently part of the technical team, and at one point was the Station Manager during 2012.

We wanted a tool to be able to record and view listener figures. Previously we had listening logging features from the stream provider using the proprietory CentovaCast panel. It wasn't perfect, since we were limited to graphs. After moving our streams internally we did not have access to CentovaCast. A temporary fix was using Munin via the [icecast2-munin plugin](http://www.github.com/mattyribbo/icecast2-munin) I wrote for Munin.

The goal for RadioPanel was to be able to log combined listener figures from multiple sources, and be able to view the figures either in an hourly window with a graph, average and peak listeners, or in a week overview with the top five hourly slots. RadioPanel should be able to add/remove user accounts and streams with ease.

To-do
-----
- General improvements
- Support for ShoutCast server
- Documentation (phpDocumentor may prove useful)