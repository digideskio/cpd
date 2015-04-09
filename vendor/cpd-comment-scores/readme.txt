=== CPD Comment Scores ===
Contributors: mwtsn, saulcoz
Donate link: 
Tags: ,ultisite, network, continual professional development, CPD, scores, comments, comment scores
Requires at least: 3.3
Tested up to: 3.9
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plug-in that works alongside the CPD Journals plugin that allows supervisors to score a participants assignment through the comments system.

== Description ==

A plug-in that works alongside the CPD Journals plugin (http://wordpress.org/plugins/cpd-journals/) that allows supervisors to score a participants assignment through the comments system.

= Features =

* Forces a user to login to leave a comment
* Enables TinyMCE as the comment editor
* Allows images and uploads in comments
* Adds a 'Score' comment field for supervisors
* Prevents a participant from editing a supervisors comment

Take a look at the screenshots page for usage guidance.

= Credits =

This plugin was developed by [Make Do](http://makedo.in) based on the original [CPD Journals plugin](http://wordpress.org/plugins/cpd-journals/) plugin by [Saul Cozens](http://saulcozens.co.uk) of [CZN Digital](http://czndigital.com), but was paid for by [Sheffield University](http://shef.ac.uk) who requested that it be written to be useful to other organisations as well and released to the community as Open Source. Thank you to them.

== Installation ==

1. Backup your WordPress install
2. Upload the plugin folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. You will note that our participant has a published assignment titled 'My assignment 1' in the journal 'CPD Journal for participant', it does not yet have any comments.
2. If we look in the comments section within the journal, you will note there are no comments at all.
3. If we log out of WordPress and view the assignment on the front end of the website, you can see that we can leave a comment (labelled leave a reply). Note that users who are not logged in are required to leave their name, email address and optionally a url of their website
4. If we log in as a participant, we now only see the 'Comment' box, as by default WordPress pulls the other details will automatically be pulled from the participants profile.
5. Lets leave a comment as a participant. You will note that we cannot leave a score. This is because we are not a supervisor.
6. The comment appears under the assignment.
7. Now lets login as a supervisor and view the same page. You will note that the comment box now allows us to enter a score. 
8. This score is required, and we will be made to enter it if we leave it blank, so lets fill out the score and leave a comment.
9. You will note that the comment and the score appear in the comments list under the assignment
10. Now lets look at editing the comments in the back end of the site. If we remain logged in as a supervisor and look at the comments in the 'CPD Journal for participant' journal, we can see the two comments. You will note that you are able to edit the comment that was made by the supervisor. The view also has the additional 'Assignment Score' column, and also the score appears under the text of the comment.
11. As a supervisor I can log in and change the comment and the score
12. Now lets look at the list of comments as the participant. You will note that I cannot edit the comment that was made by the supervisor.
13. But I can edit the comments that were left by people who were not a supervisor
14. And even if I manage to get to the edit screen of the supervisors comment, I am still unable to make a change

== Changelog ==

= 1.0.0 =
* Initial release

= 1.0.1	=
* Prevent participant from disabling comments by removing the menu

== Upgrade notice ==

There have been no breaking changes so far.