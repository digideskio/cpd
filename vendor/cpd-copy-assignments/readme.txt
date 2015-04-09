=== CPD Copy Assignments ===
Contributors: mwtsn, saulcoz
Donate link: 
Tags: multisite, network, continual professional development, CPD, assignments, copy, copy assignments, copy pages, journals
Requires at least: 3.3
Tested up to: 3.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plug-in that works alongside the CPD Journals plugin that allows assignments to be copied into participants journals.

== Description ==

A plug-in that works alongside the CPD Journals plugin (http://wordpress.org/plugins/cpd-journals/) that allows assignments to be copied into participants journals.

= Features =

* Copy assignments from a journal / site to one or more particpants journal
* If an assignment exists in a journal it will not be copied accross (the names are the same)
* Assignments are defined as any page that sits under a page with a slug of 'assignment'
* Assignments do not have to be published

Take a look at the screenshots page for usage guidance.

= Credits =

This plugin was developed by [Make Do](http://makedo.in) based on the original [CPD Journals plugin](http://wordpress.org/plugins/cpd-journals/) plugin by [Saul Cozens](http://saulcozens.co.uk) of [CZN Digital](http://czndigital.com), but was paid for by [Sheffield University](http://shef.ac.uk) who requested that it be written to be useful to other organisations as well and released to the community as Open Source. Thank you to them.

== Installation ==

1. Backup your WordPress install
2. Upload the plugin folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. We have the base website setup, as well as one journal. A participant and supervisor have permissions to view this journal
2. To create the assignments that are to be copied into journals in any journal, or the root website (recommended) create a page titled 'Assignments'. Under this page create the assignments (they can all be drafts, private or published).
3. In this example, the participants journal I am copying the assignments to has no current assignments in it:
4. A supervisor can log in, and they will see an option under the 'My Sites' option on the left hand menu titled 'Copy Assignments' (Administrators can also see this). Choosing this option will give a list of assignments and journals.
5. The journals are greyed out, until one or more assignment is checked. Check the assignments you wish to copy, and then check the journal you wish to copy them into.
6. Click the 'Copy Assignments' button. In the scenario above 'My assignment 1' and 'My assignment 2' will be copied into the 'CPD journal for participant'. A message is show at the top of the screen indicating what has been copied. If the assignments with the same names were already in the participants journal, they will not be copied.
7.  If you now log in as the participant and view the pages in their journal, you will see that the assignments have been copied, but are not published. They have also been created as the participant, meaning that they have full access to edit them. 

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade notice ==

There have been no breaking changes so far.