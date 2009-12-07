<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');
?>
<!--

Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the Kunena 1.x, including beta and release candidate versions.
The Kunena 1.x is based on the FireBoard releases but includes some
drastic technical changes.
Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Kunena 1.6.0-DEV

6-Dec-2009 fxstein
+ [#18906] Next iteration of config options - selection lists added
+ [#18906] Next iteration of config options - still missing selection lists

4-Dec-2009 fxstein
^ [#1193] Various data model changes
+ [#18840] Additional functionality for KConfig html helper class: html editor
+ [#18906] Additional config options layout in various views

3-Dec-2009 Matias
# [#18906] Backend framework: add missing file, some cleanup
# [#18912] Implement new installer: Fix bugs from installation

3-Dec-2009 fxstein
+ [#18840] Additional functionality for KConfig html helper class
+ [#18906] Initial options for security config screen

2-Dec-2009 Matias
+ [#18912] Implement new installer: add most missing logic to upgrade database, field/table renaming and custom queries still missing

1-Dec-2009 fxstein
+ [#18906] Initial draft of new backend framework

28-Nov-2009 fxstein
# [#18874] Minor fixes to klink html helper class

28-Nov-2009 Matias
- [#18869] Remove all legacy code - except libraries needed by old installer

25-Nov-2009 fxstein
+ [#18840] Initial draft of Kconfig html helper class

22-Nov-2009 Matias
+ [#17628] Make profile box to work in messages view
# [#17628] Improve userlist
+ [#17628] Add RSS format for recent discussions

08-Nov-2009 Xillibit
+ [#18628] Add view for userlist page almost done (need online status)
^ [#18628] Update view for more about stats page
+ [#18628] Add view for more about stats page

26-Oct-2009 Xillibit
+ [#18628] Add model for userlist and for more about stats

21-Oct-2009 Matias
+ [#17628] Category view (recent.category)
# [#17628] Fix most broken links and images
^ [#17628] Update credits page
+ [#17628] Add files for post view (nothing to see in there)

20-Oct-2009 Matias
+ [#17628] Basic model for post, reply, edit, subscribe and favorite

19-Oct-2009 Xillibit
+ [#17628] Add view and model for credits page

17-Oct-2009 Matias
+ [#17628] Installer: Add logic to create SQL queries from XML schema

16-Oct-2009 Matias
+ [#17628] Add missing index.html files
^ [#17628] New entry file in both frontend and backend: api.php
^ [#17628] Move some legacy code into legacy files
+ [#17628] New installer classes (not yet fully functional)
^ [#17628] New build scripts

5-Oct-2009 Matias
- [#17628] Integration class cleanup

26-Sep-2009 Matias
^ [#17628] Improve Announcements and Statistics models
+ [#17628] Add basic views for Announcements and Statistics
+ [#17628] Improve both profile boxes, show logged in user

25-Sep-2009 Matias
+ [#17628] Add integration classes for CB, JomSocial and UddeIM
+ [#17628] Add KFactory to get global objects in Kunena
# [#16823] Fix installer: kunena_messages table creation failed, Welcome thread missing
+ [#17628] Make login box, avatars and PM to work
+ [#17628] Make KUser class general (for all users)
^ [#17628] Save some SQL queries by caching results (users, acl) inside KUser class
+ [#17628] Add very basic templates to the new views: user and userlist
# [#17628] Fix title in recent, messages view

24-Sep-2009 Matias
+ [#17628] Recent view: add logic for new, sticky, favorite
+ [#17628] Messages view: handle non-existing threads

23-Sep-2009 Matias
+ [#17628] Make Recent view to obey all configuration options in menu

22-Sep-2009 Matias
+ [#17628] Add two new views: user and userlist
^ [#17628] Continue working on looks and logic for views
# [#16823] Replace of fb with kunena in plugins directory

18-Sep-2009 Matias
+ [#17628] Continue making default template for Recent and Categories views
+ [#17628] Continue making default template for Messages view

17-Sep-2009 Matias
+ [#17628] Add default_ex look to top menu, search, profile box, pathway...
+ [#17628] Add default_ex look to categories, recent, stats...

15-Sep-2009 Matias
+ [#17628] Working category list implementation
+ [#17628] Partial pathway implementation
^ [#17628] Profilebox cleanup

9-Sep-2009 Matias
+ [#17628] Added most links to categories view

8-Sep-2009 Matias
+ [#17628] Working categories logic in nested view of Categories

3-Sep-2009 Matias
+ [#17628] Implementing Recent, Categories and Messages views

2-Sep-2009 Matias
+ [#17628] Added html views for Categories and Messages
+ [#17628] Added configuration for all existing views
# [#17628] Minor bug fixes
+ [#17628] Added static legacy content to categories and messages views

20-Aug-2009 fxstein
+ [#17628] Additional JHtmlKLink methods
^ [#17628] converted default_thread.php to use new JHtmlKlink methods
+ [#17628] Initial backend configuration settings

19-Aug-2009 Louis
+ [#17628] Added JHtmlKLink class.

19-Aug-2009 fxstein
^ [#17628] Minor K1.5 legacy cleanup
+ [#17654] Initial configuration for bbcode parser and usage sample
		   in messages/view.raw.php
+ [#17628] Additional JHtmlKLink methods

18-Aug-2009 fxstein
# [#17654] Various bugfixes in new bbcode parser class
# [#17628] Various bugfixes for new MVC structures
^ [#17628] header and footer cleanup in recent
^ [#17628] Changelog content converted to comment to avoid warnings
^ [#17628] Updated COPYRIGHT.php

18-Aug-2009 Matias
+ [#17628] Add models and logic to statistics and announcements

17-Aug-2009 Louis
+ [#17628] Add example fields for configuration options.

17-Aug-2009 fxstein
- [#17628] Reverting revisions 1018 & 1017

16-Aug-2009 Louis
+ [#17628] Add scaffolding for administrator MVC structures and skeleton for config view.

16-Aug-2009 Matias
+ [#17628] Add basic logic for recent discussions (content, pagination)

16-Aug-2009 fxstein
+ [#17628] KUser class created after merging user and session tables
+ [#17628] Initial implementation of recent model
+ [#17628] Initial data population for recent and my recent view in model
+ [#17628] category view added to recent view in model
+ [#17628] Allow for stickies and complex sorting in recent model
+ [#17628] Initial implementation of messages model
+ [#17628] Initial implementation of categories model (flat only)
+ [#17654] Add new bbcode parser: nbbc

15-Aug-2009 Matias
+ [#17628] Add css file for recent discussions
+ [#17628] Add some html for recent discussions

15-Aug-2009 fxstein
# [#17375] fixed broken debug output on failed queries
^ [#17375] additional installer changes for new data model and mvc
^ [#17375] latestx.php partially converted to new threads table design
+ [#17628] KConfig, KSession & KMaintenance classes refactored
+ [#17628] Get state for view type, category and filter in recent.php
^ [#17375] Merged user and session tables

14-Aug-2009 Louis
+ [#17628] Added scaffolding for recent activity view and base library importer.

14-Aug-2009 fxstein
^ [#17628] Initial file systems changes for MVC next gen layout
^ [#17375] Changed attachement count logic for thread table during upgrade
# [#17375] Fixed broken threads table ddl

13-Aug-2009 fxstein
+ [#17375] Added name and email to thread table and upgrade logic
+ [#17375] Added attachement count to thread table and upgrade logic
^ [#17375] Initial draft of data model changes for default_ex/flat.php

12-Aug-2009 fxstein
+ [#17375] Initial upgrade logic for kunena_threads table

12-Aug-2009 Matias
+ [#17588] Add classes for all tables

28-July-2009 fxstein
^ [#17375] Additional changes to kunena_threads table - currently only in installer

27-July-2009 fxstein
^ [#16825] First draft of merged messages_text into messages, all fb tables converted
+ [#17375] Initial draft of kunena_threads table - currently only in installer

24-July-2009 Matias
^ [#15784] Merge 1.5.3 and 1.5.4 fixes from revision 878 to 936

10-July-2009 fxstein
^ [#16827] Update Changelog entires
^ [#16827] Rename version to 1.6.0dev to reflect pre-alpha status

9-July-2009 fxstein
+ [#16414] New CKunenaModeration class to encapsulate all moderation logic
! First working Kunena 1.6.0dev (dev) build
^ [#16825] New upgrade logic to convert pre 1.6 database during
  automatic upgrade
^ [#17165] Rename backend graphics files from fb* to kunena*

15-June-2009 fxstein
^ [#16823] Global replace of fb/fb_/fb- with kunena/kunena_
^ [#16824] Change MySQL minimum version to 5.0.0 for 1.6 release
^ [#16822] Update version info for new 1.6 branch

-->