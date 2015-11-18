# Aspire CPD

A single plugin to transform a WordPress Multisite install into a CPD (Continuous Professional Development) management platform. Built in association with The [University of Sheffield](http://www.sheffield.ac.uk/).

[Got an issue? Make sure you report it to us](https://github.com/mkdo/cpd/issues).

## Theme

To get the most out of this plugin you will need to install the companion [Aspire CPD Theme](https://github.com/mkdo/aspire-cpd). You will be prompted to do this automatically when you install the plugin.

## Documentation

[Documentation is available in the Wiki](https://github.com/mkdo/cpd/wiki).

## Credits

We believe in giving credit where credit is due. The following plugins and people helped us get a kick start in delivering this project. Thank you to them!

The foundations of this project are based on the [CPD Journals](http://wordpress.org/plugins/cpd-journals/) WordPress plugin originally by [Saul Cozens](http://saulcozens.co.uk) - licensed under the GPLv2 license.

The way that new Journals are created with base 'Journal Templates' could not be done without code taken from the [Blog Copier](https://wordpress.org/plugins/blog-copier) WordPress plugin by [MODERN TRIBE](https://tri.be/) - licensed under the GPLv2 license.

To create meta boxes we use the excellent [Custom Meta Boxes](https://github.com/humanmade/Custom-Meta-Boxes) plugin by [Human Made](https://hmn.md/) - licensed under the GPLv2 license.

The ability to login with email was done by following examples in the [WP Email Login](https://wordpress.org/plugins/wp-email-login/) plugin by [Beau Lebans](https://profiles.wordpress.org/beaulebens/), [r-a-y](https://profiles.wordpress.org/r-a-y/) and [andykillen](https://profiles.wordpress.org/andykillen/).

To update the plugin directly from GitHub we use the awesome [WordPress GitHub Plugin Updator](https://github.com/radishconcepts/WordPress-GitHub-Plugin-Updater) by [Radish Concepts](http://www.radishconcepts.com/) - licensed under the GPLv2 license. We also borrowed heavily from this to allow the associated CPD theme(s) to be updated directly from GitHub also.

## Version Control

Here are all the great features that have been implemented so far!

### 2.4.7
- Issues fixed where an elevated admin can not manage other elevated admins

### 2.4.6
- Issues with wp_new_user_notification fixed

### 2.4.5
- Profile update bug fix

### 2.4.4
- Updated User Guide links

### 2.4.3
- Fixed an error that was causing WordPress to declare an array invalid

### 2.4.2.1
- Altered code to make the plugin compatible with older versions of PHP

### 2.4.2

- Forced journal entries to have comments enabled
- Super Admins can no longer be supervisors
- Stopped participants from managing privacy options by default, a cap of `manage_privacy` has been added to the filter `filter_cpd_remove_participant_capabilities` in admin/cpd-users.php

### 2.4.1

- Re-enabled WordPress comments dashboard widget
- Replaced 'howdy' text with user name and role notification
- Fixed issue with plugin updater

### 2.4.0

- New membership management area

### 2.3.0

- New Journal Templating System
- Retire 'Assignments' copy, use pages only
- Create copy page for each content type

### 2.2.1

- Added Aspire CPD branding to system
- Theme installer
- GitHub Version Control

### 2.2.0

- Competencies Functionality (Assessments)
- New dashboard widgets to ease custom journey
- Bug Fix: Journal Entries by... widgets were not linking to correct Journal Entries

### 2.1.1

- Improved Privacy Options
- Dashboard Widget(s) for Template Authors
- Made root site reference 'site' (not journal)
- Tidy up of root site menus and widgets
- Login with email

### 2.1.0

- PPD Custom Post Type (Activity Log)
- Activities Widget
- Development Category Taxonomy
- Customizer Integration
- Categories Dashboard Widget
- Master Template on activation
- New Journals use a Master Template

### 2.0.2

- Added subscriber dashboard widget
- If supervisor, subscriber dashboard widget advises of correct journals
- Added network admin dashboard widget

### 2.0.1

- Participants switch to main blog on login
- Participants can no longer un-tick allow comments
- Remove theme customizer menus for supervisors and participants

### 2.0.0

- Complete Refactor

### 1.0.0

- Initial Prototype

## Feature Roadmap

Here are all the great features that we plan to implement. If you have a feature request you can add it by [creating a new issue](https://github.com/mkdo/cpd/issues).

### 2.5.0

- Personal Data Entry (Portfolio)
- Email Options (Opt in/out)

### 2.6.0

- Supervisor can add Alerts to participant journals
