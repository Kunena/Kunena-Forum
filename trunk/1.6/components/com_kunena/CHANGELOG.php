<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// no direct access
defined( '_JEXEC' ) or die();

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

20-Feb-2010 fxstein
+ [#19380] Basic attachments display scaffolding added with sample display data

19-Feb-2010 fxstein
^ [#19380] Modified attachments table to support legacy folder structure

19-Feb-2010 Matias
^ [#19690] Rename bbcode [mod] to [confidential] and make small changes to it's logic

18-Feb-2010 Matias
# [#19399] Fixed undefined variable in RSS code

18-Feb-2010 fxstein
^ [#19399] Changed remaining _LISTCAT_RSS occurances to JText
^ [#19312] Changed "Posted at" to "Posted" in language file
+ [#19380] Create new attachment table for advanced multi attachment handling

18-Feb-2010 Matias
# [#19380] Multifile upload: Fixed fixed path to silverlight, flash runtimes

17-Feb-2010 fxstein
+ [#19399] merged new RSS code (part 1 - intial merge from littlejohn branch)
^ [#19399] merged new RSS code (part 2 - language string corrections)
+ [#19399] merged new RSS code (part 3 - dedicated section for RSS settings)
- [#19399] merged new RSS code (part 4 - remove security bypass)

17-Feb-2010 severdia
^ [#19345] Added new styles for pagination, but still needs correct output

17-Feb-2010 Matias
^ [#19345] Restyle Default template: pagination
^ [#15886] Merge latest changes /branches/1.5-xillibit, added minor fixes
# [#19380] Multifile upload: Fixed logic for gears, silverlight, flash uploads

17-Feb-2010 Xillibit
+ [#19690] Add configuration report system in Kunena backend - add new bbcode [mod][/mod] for show content only for mods and admins

15-Feb-2010 Xillibit
+ [#19668] Add parser logic for map

14-Feb-2010 Matias
^ [#19380] Multifile upload: Working logic for html5 uploads (not saved into DB yet)

14-Feb-2010 severdia
# [#19356] More CSS fixes and reworked Report to Mod page

13-Feb-2010 severdia
# [#19356] More CSS fixes for default Joomla templates

13-Feb-2010 Matias
^ [#19380] Multifile upload: Yet another try with plupload 1.0 (supports flash, html5 etc)

13-Feb-2010 Xillibit
# [#19332] Change Delete behavior - add sortables on all items
^ [#19358] Apply some changes on the polls - wrong path for bar.png, remove url in javascript for vote

12-Feb-2010 Xillibit
# [#19690] Add configuration report system in Kunena backend - add function to select all text, add two configurations settings

12-Feb-2010 severdia
# [#19356] CSS fixes for Afterburner

12-Feb-2010 Matias
# [#19288] Fix regression: AJAX Upload broke up during merge

10-Feb-2010 Xillibit
+ [#19690] Add configuration report system in Kunena backend

08-Feb-2010 littlejohn
+ [#19399] New RSS feeds (part 5 - added frontend view and rss class)
- [#19399] New RSS feeds (part 4 - removed old frontend)
# [#19399] New RSS feeds (part 3 - removed trailing space from parser affecting all templates)
^ [#19399] New RSS feeds (part 2 - changed and corrected administrative options)

08-Feb-2010 Xillibit
# [#19332] Change Delete behavior - fixes to solve an issue in trash manager and now put the poll deletion in trash manager

07-Feb-2010 Xillibit
# [#19631] Re-implement quick reply by using mootools - fix a bug which create new threads instead replies
+ [#19668] Re-implement bulkactions with mootools
+ [#19668] Write javascript logic for video in editor

07-Feb-2010 severdia
# [#19356] CSS fixes, rounded tabs (CSS3 only)

07-Feb-2010 Matias
# [#19288] Fix regression: New installation did not work because of old sample data
# [#19288] Fix regression: Missing language strings in installer and in backend
# [#19288] Fix regression: Create menu item: Kunena cannot be selected
# [#19288] Fix regression: Upgrade failed if configuration did not exist

07-Feb-2010 severdia
# [#19312] Language fixes (thanks again to kmilos)

06-Feb-2010 severdia
+ [#19312] Checkbox for check toggle
# [#19356] CSS fixes

05-Feb-2010 fxstein
^ [#19345] Display page creation time in footer
+ [#19251] Jomsocial user prefetch caching to reduce query counts - showcat
! [#19657] Merge latest branch provided by xillibit

05-Feb-2010 Xillibit
# [#19631] Re-implement quick reply by using mootools - little changes
# [#19288] Fix regression - remove deprecated $mainframe put in trash manager

04-Feb-2010 Xillibit
^ [#19639] Add form-validation instead alert
# [#19639] Add form-validation instead alert - some fixes on this, now work like it should

04-Feb-2010 fxstein
^ [#19345] Re-style child board counts to match new template
^ [#19645] More language conversion changes
+ [#19345] Display page creation time in footer
- [#19634] Shorten Changelog to 1.6 changes

04-Feb-2010 severdia
# [#19312] Language fixes (thanks to kmilos)

04-Feb-2010 Matias
# [#19288] Fix regression: Posting new message did not work
# [#19561] Fix poll ajax calls: broken SQL, only first element got matched in JS
# [#19380] Many small fixes to the editor ajax calls (language strings, error handling)
^ [#19645] Convert Language files to native Joomla 1.5/6 ini's
^ [#19645] Convert all language strings to use JText::_()
# [#19645] Fix missing quotes from language strings, use new language files

03-Feb-2010 Xillibit
+ [#19631] Re-implement quick reply by using mootools

03-Feb-2010 Matias
# [#19251] Advanced special user prefetch: bugfix
# [#19380] Multifile upload: didn't work while editing a post
- [#19293] Remove deprecated PM Systems: mypms, missus, jim
- [#19295] Clean up code: remove unused code in kunena.php + PMS options
# [#19288] Fix regression: Installer does not work

03-Feb-2010 fxstein
+ [#19251] Jomsocial user prefetch caching to reduce query counts
^ [#19634] Update package file name for internal night build

03-Feb-2010 littlejohn
^ [#19399] New RSS feeds (part 1 - adding administrative options)

02-Feb-2010 fxstein
+ [#19251] Advanced special user prefetch caching to reduce query counts

02-Feb-2010 Matias
+ [#19380] Multifile upload: upload files to server by using iframe (part 2)
# [#19624] Improve sending moderator/subscription mail (check email addresses, cleanup contents, etc)
- [#19380] Multifile upload: no more Fancy Upload

01-Feb-2010 fxstein
^ [#19236] Revert category overlay colors
# [#19356] Center menu tabs and make spacing font size relative
+ [#19380] Multifile upload (part 1)

01-Feb-2010 severdia
# [#19356] Random CSS fixes for UI

31-Jan-2010 severdia
+ [#19383] Added uknown gender icon/option
+ [#19356] Moved icons to proper folders, new icons
+ [#19356] More new icons

31-Jan-2010 Matias
+ [#19383] Revise Profile Page: added Started Topics and Posted Topics tabs
+ [#19383] Revise Profile Page: show users post count
^ [#19598] Make topic icons configurable in icons.php
# [#19278] Keep topic icon after editing message (make it better)
+ [#19383] Revise Profile Page: hide moderation from myself, regular users
^ [#19383] Revise Profile Page: fix unknown location, gender
# [#19288] Fix regression: Pending messages query
# [#19288] Fix regression: undefined variable during posting
+ [#19599] Moderators should be able to see unapproved messages while reading thread and approve them

31-Jan-2010 fxstein
+ [#19592] Add JFirePHP support
+ [#19592] Add initial profiling info via JFirePHP
+ [#19592] Implement KProfiler

31-Jan-2010 severdia
# [#19356] Added missing social icons, new topic icons
# [#19356] Added CSS for red forum suffix

30-Jan-2010 Xillibit
# [#19561] Put the poll form into the new editor - little changes

29-Jan-2010 fxstein
^ [#19561] Poll ajax interface naming changes

28-Jan-2010 Xillibit
# [#19561] Put the poll form into the new editor - poll options doesn't saved when you edit a post

28-Jan-2010 fxstein
# [#19380] Preliminary video option submenu
+ [#19380] Include fancyupload libraries in project

27-Jan-2010 Xillibit
^ [#19561] Put the poll form into the new editor

27-Jan-2010 fxstein
# [#19356] Fix html regression in message.php
# [#19380] Implement new resize function and modify preview css

26-Jan-2010 severdia
# [#19380] Fixed preview splitter

26-Jan-2010 fxstein
+ [#19380] New insert link function, added poll icon to toolbar
+ [#19380] New insert image link function, new help button and function pointing at our wiki
+ [#19380] Basic split screen preview support added to bbcode editor
+ [#19380] Automatic preview update on change (every 1000ms) added
^ [#19380] Redo preview layout to use a simple div - not table
^ [#19380] Revert poll changes for now

26-Jan-2010 severdia
# [#19356] Reworked profile area on posts

26-Jan-2010 Matias
# [#19288] Fix regression: Broken/missing queries during upgrade
# [#19288] Fix regression: undefined variables, minor bugs
# [#19251] Reduce the number of SQL calls in view
# [#19448] Move code out of template: smile.class.php
- [#19295] Clean up code: remove unused code (plugins/profiletools, plugins/emoticons)
# [#19397] Fix date format in Kunena: use user/site timezone, keep saving with internal time
^ [#19345] Restyle Default template (view): remove viewcovers, change logic from online status
+ [#19303] Add new social icons to profile: ICQ, MSN
^ [#19380] Replace jQuery with Mootools 1.2: convert remaining togglers

26-Jan-2010 Xillibit
^ [#19023] Text filtering not working for title, done in every places

26-Jan-2010 @quila
^ [#19288] Fix regression - remove js folder from manifest.xml

25-Jan-2010 severdia
# [#19356] New icons, removed English folder in images (moved images up one level), refined forum colors

25-Jan-2010 Xillibit
^ [#19395] Add better captcha support with recaptcha - don't show the html tables if the puglin is unpublished, rewrite of language strings
^ [#19288] Fix regression - remove one extra query added in kunena.login.php

25-Jan-2010 fxstein
+ [#19380] New bbcode editor (part 5) - New font size selector, refactor color selector
^ [#19380] Modified text alignment and line heights for text size selector
+ [#19539] Add image sources to svn
+ [#19380] Additional bbcode editor toolbar icons

25-Jan-2010 Matias
# [#19448] Move most of the html from lib/ to default template
# [#19316] Fix remaining double SQL calls, add checks for failed queries

25-Jan-2010 @quila
+ [#19359] Color Code moderator and admin username - different css classes to moderator and admin usernames.

24-Jan-2010 Xillibit
+ [#19395] Add better captcha support with recaptcha (http://www.joomlaez.com/joomla-plugins/joomla-captcha-solution.html)
^ [#19395] Add better captcha support with recaptcha - delete old plugin captcha and set the captcha language which depends of joomla! language

24-Jan-2010 fxstein
+ [#19380] New bbcode editor (part 4) - All alt, title and helptext strings and conditionals added
^ [#19380] New bbcode editor (part 5) - Integrate and refactor base class and java script

24-Jan-2010 @quila
+ [#19233] Add Kunena Login into the new default template

23-Jan-2010 fxstein
+ [#19380] New bbcode editor (part 3) - All Smilies added, message name regression fixed

23-Jan-2010 Matias
# [#18974] Categories and sections mixed up
# [#19253] Do not allow forum parent to be it's own child
^ [#19295] Clean up code: use always new profile, improvements on moved topics
# [#19035] Call to undefined method JDocumentRAW::addCustomTag()
# [#19376] attach file when the file have is extension in capital letters, exemple : myfile.JPG doesn't work
- [#19293] Remove deprecated configuration options: default_view, numchildcolumn, cb_profile

23-Jan-2010 Xillibit
# [#19486] Remove all: die ("Hacking attempt");

22-Jan-2010 fxstein
+ [#19380] New bbcode editor (part 2) - All buttons added as css sprite

22-Jan-2010 Xillibit
^ [#19485] Add AJAX call to check if polls are allowed if category changes

21-Jan-2010 fxstein
+ [#19380] New bbcode editor (part 1)
^ [#19380] New Joomla 1.5.16 / 1.6 style framework bahvior; remove secondary mootools 1.2 libraries

21-Jan-2010 Xillibit
^ [#19332] Change Delete behavior - show in backend too the posts wrote by visitors and sortable list on the title
^ [#19066] Make jomSocial Activity stream integration configurable

20-Jan-2010 Matias
^ [#19295] Clean up code: merge showcat subcategories code with listcat
# [#19251] Do not query SQL for new topics when the information isn't used (latestx, showcat)
# [#19383] Make subscriptions and favorites to work better inside Profile page
# [#19288] Fix regression in showcat: There are no forums in this category!

20-Jan-2010 Xillibit
^ [#19358] Apply some changes on the polls - publish or unpublish a category allowed for polls directly in forum administration

20-Jan-2010 fxstein
^ [#19380] Drop jQuery; rewrite bbcode editor functions using mootools
^ [#19380] Change emoticon to color in preview
^ [#19380] Hide preview section when not active - display and grow on demand
+ [#19295] Add missing index.php files
^ [#19295] Move bbcode editor into seperate file: editor/bbcode.php
+ [#19295] Add svn:keywords Id to all new files

19-Jan-2010 svens LDA
# [#19339] Incorrect implementation of links in CKunenaLink class - Part 3

19-Jan-2010 Matias
# [#19288] Fix regression: add missing directories to manifest.xml
# [#19288] Fix regression: missing info in showcat
^ [#19295] Clean up code: latestx, flat, part of showcat, fix warnings
+ [#19383] Revise Profile Page: add rough version of favorites, subscriptions
- [#19295] Clean up code: remove plugins/fbprofile

19-Jan-2010 fxstein
# [#19358] Fix incorrect install file logic, add upgrade step and cleanup prior changes.
- [#19380] Remove Chili code high lighter as part of transition to mootools

19-Jan-2010 Xillibit
^ [#19234] Hide IP addresses from Moderators - add configuration option in backend
^ [#19358] Apply some changes on the polls - set the categories allowed for polls in forum administration
^ [#19358] Apply some changes on the polls - fix a regression introduced when you post a new thread
# [#19252] Fix slow SQL queries in RSS feed
^ [#19358] Apply some changes on the polls - fix a regression introduced when you post a new thread (part 2)

18-Jan-2010 fxstein
+ [#19067] Add messages in registered only categories to jomsocial activity stream and set access control
^ [#19479] Enable upgrade logic to work in Joomla debug mode

18-Jan-2010 Matias
^ [#19448] Move code out of template: listcat
# [#19251] Reduce the number of SQL calls in listcat

18-Jan-2010 Xillibit
^ [#19358] Apply some changes on the polls - rewrite the javascript to use mootools

18-Jan-2010 severdia
# [#19356] Change CSS and reorder fields on Recent Topics
# [#19356] Cleaned up Profile page, new generic/online buttons, fixed language strings

17-Jan-2010 fxstein
+ [#19244] Ajax/json support class scaffolding; intial autocomplete on moderation page
+ [#19244] Finished initial version of Ajax/json support class; autocomplete on moderation and search
# [#19470] Incorrect user count in stats module - included blocked users

17-Jan-2010 Matias
^ [#19243] Make profile on left/right/top/bottom configurable - move files to their new places
^ [#19448] Move code out of template: showcat - subcategories
# [#19251] Reduce the number of SQL calls in showcat - subcategories

17-Jan-2010 @quila
+ [#19243] Make profile on left/right/top/bottom configurable - Part 2

16-Jan-2010 svens LDA
# [#19339] Incorrect implementation of links in CKunenaLink class
- [#19455] Remove depriciated code in smile.class.php

15-Jan-2010 severdia
# [#19356] Reworked CSS and synced colors and fonts, added palette to CSS header, minor language string fixes

15-Jan-2010 Matias
# [#19447] Deleted messages sometimes showing up in latestx
^ [#19448] Move code out of template: view
# [#19288] Fix regression: cannot view messages if user has been deleted
^ [#19303] Social network icons: Allow values to be put anywhere in URL by using ##VALUE##
^ [#19448] Move code out of template: latestx, showcat

15-Jan-2010 Xillibit
# [#19288] Fix regression after namming changes on move/delete in class.kunena.php
# [#19358] Apply some changes on the polls - fixes regressions detected after last changes on polls
+ [#19235] Add category info to search results
# [#19358] Apply some changes on the polls - fix one another regression on polls
# [#19278] Keep topic icon after editing message

14-Jan-2010 fxstein
^ [#19345] Refactor css class names 'fb' to 'k'
^ [#19437] Update copyright dates to 2010
# [#19397] Preliminary fix for post date issue
# [#19295] Clean up code: Update template chooser
+ [#19064] Add additional common bbcodes to purify
^ [#19438] SVN keywords Id set on all files missing it
+ [#19244] Moderation page scaffolding

14-Jan-2010 Xillibit
+ [#19332] Change Delete behavior - implemented in backend (part 1)
^ [#19332] Change Delete behavior - modfied the delete function in frontend (part 2)

14-Jan-2010 Matias
# [#19288] Fix regression: Undefined fbConfig in kunena.parser.php
# [#19295] Clean up code: Remove forumtools
# [#19295] Clean up code: Remove deprecated plugins/recentposts
^ [#19303] Add social network icons to profile: use layout from profile view
^ [#19295] Clean up code: Use new rank function in view.php

13-Jan-2010 louis
^ [#19380] Added show/hide behavior for statistics and whoisonline blocks.
^ [#19380] Added show/hide behavior for any block based on an a.toggler selector and rel attribute

13-Jan-2010 fxstein
+ [#19244] New moderator tools class added
^ [#19380] Change joomla menu for Kunena to reflect new profile url format
^ [#19425] Security hardening: defined( '_JEXEC' ) or die();
^ [#19345] Modified css class prefix logic
# [#18995] Undefined variables in message.php fixed

13-Jan-2010 Matias
+ [#19380] New profile page: forum/profile. Not yet activated in menu, links
# [#19397] Fix date format in Kunena, make it configurable

13-Jan-2010 Xillibit
# [#19103] Language strings should be escaped in javascript, added in myprofile and tested in write post
+ [#19232] Add option for message numbering

12-Jan-2010 severdia
+ [#19380] Add moderator tab to Profile page, new language strings
^ [#19380] Added JS slider links, needs testing

12-Jan-2010 Xillibit
# [#19288] Fix regression, waring in zend in myprofile_summary.php
^ [#19358] Apply some changes on the polls, allow the user to change her vote
# [#19358] Apply some changes on the polls, regression undefined catid in poll.php
^ [#19377] Allow the maxlength on the personnal text to be modified easily

12-Jan-2010 Matias
# [#19251] Reduce the number of SQL calls when showing frontstats
^ [#19295] Clean up: remove unused code (statsbar) and images
^ [#19295] Clean up: replace module positions by CKunenaTools::showModulePosition()
# [#19313] Minor fix for SVN installer: always run queries
# [#19288] Fix regression: Writing new topic possible without permissions

11-Jan-2010 severdia
# [#19380] Add tabs & code to JS file, profile page
# [#19380] Removed forum section minimizers in prep for MT version, fixed tabs on profile page

11-Jan-2010 Matias
# [#19064] Finalized new bbcodes: fixed preview
# [#19288] Fix regression: warnings in backend after css file moved
- [#19293] Remove deprecated configuration options: poststats, statscolor
# [#19251] Reduce the number of SQL calls in various views
# [#19251] Reduce the number of SQL calls in various views (router in showcat, latestx)
# [#19288] Fix regression: New topic without category not working

11-Jan-2010 fxstein
+ [#19064] Finalized new bbcodes: table, th, tr, td & module (for joomla modules)
^ [#19400] Changed subheader layout, reformated category listings
^ [#19064] Separate bbcode css

10-Jan-2010 svens LDA
# [#19339] Incorrect implementation of links in CKunenaLink class

10-Jan-2010 severdia
# [#19345] Stats page cleanup, Language string cleanup
^ [#19345] Moved smilies from side of new message screen to top, language strings fixed

10-Jan-2010 Matias
# [#19383] Revise Profile Page: remove deprecated functions, minor fixes
+ [#19356] New buttons: added logic to show new buttons
- [#19293] Remove deprecated configuration option: joomlastyle
# [#19032] Moderator moving topic from thread: forum order is wrong
+ [#19298] Add category selection pull down to New Thread
# [#19371] Fix router to accept the new menu items
# [#19288] Fix regression: Fix broken html, remove unused broken code

10-Jan-2010 severdia
^ [#19383] Redesigned profile page

10-Jan-2010 fxstein
# [#19358] Cleanup of polls backend to fix broken configuration
^ [#19371] Change default page behaviour - make it work with new Joomla menus
^ [#19345] Re-style default template: replace <p> with proper <div> in listcat.php
^ [#19369] Proper headers for all views
^ [#19369] Move secondary headers into <body> for proper styling

09-Jan-2010 severdia
# [#19356] Fixed search page accessibility, cleaner CSS
+ [#19356] JS for clickable checkbox fields
+ [#19380] Added Mootools in preparation to replace jQuery

09-Jan-2010 fxstein
+ [#19371] Auto generate joomla menu from control panel and during install
# [#19371] change componentid behaviour for created menus to support sef
^ [#19379] rename faq to help
+ [#19371] Language file strings for new menu creation logic
- [#19371] Remove leagcy menu code including layout.php

08-Jan-2010 810
+ [#19303] Add twitter, facebook to profile
^ [#19303] Add twitter, facebook to profile, edited the images

08-Jan-2010 Xillibit
^# [#19358] Apply some changes on the polls

08-Jan-2010 severdia
^ [#19356] New buttons, rank icons
^ [#19345] Re-style default template

08-Jan-2010 Matias
^ [#19345] Re-style default template: Search Tab
# [#19352] Own favorite star gray when many users have favorited the same topic

08-Jan-2010 fxstein
^ [#19345] Re-style default template
- [#19251] Remove unneeded query for modified posts
# [#19303] Fixed installer regression
^ [#19345] added new column for views in flat.php
+ [#19345] formatLargeNumber(): format numbers >10,000 to 10k >1,000,000 to 1m for various outputs
^ [#19345] proper col span and width for new view column in flat.php

07-Jan-2010 fxstein
+ [#19333] New datamodel table for category subscriptions
+ [#19333] New category subscriptions logic (w/o email notification)
+ [#19333] eMail notifications for New category subscriptions
^ [#19342] Cleanup redirect after post, avoid intermediate page

07-Jan-2010 Xillibit
# [#19029] If moderator edits the post, email address gets replaced

07-Jan-2010 Matias
# [#19316] Fix double SQL calls, add checks for failed queries
# [#18862] New thread is unread after posting it
# [#19288] Fix regression: session expired on every page load
# [#19321] Message text missing from moderator emails if there is no subscriptions
# [#18994] Email to moderators does not send email to global moderators
# [#19323] Flood protection should not block Subscribe and Favorite
# [#18903] Moderator and subscribed to topic: user will receive two emails
# [#19277] Clean up and restructure post.php, part 5: rewrote permissions checks during posting
# [#19029] If moderator edits the post, email address gets replaced (part 2)

06-Jan-2010 Matias
- [#19293] Remove deprecated configuration option: View=flat/thread (leftover code and cookie)

06-Jan-2010 fxstein
+ [#19313] SVN installer option added. Added control panel button to execute installer (only in SVN mode)
# [#19200] Fixed regression in check_dberror that would crash php server due to invalid recusion
# [#19241] A little love for our new polls. Cleaned up install/upgrade, added db checks to all sql
# [#19241] Added missing id and catid parameter definitions

06-Jan-2010 Xillibit
^ [#19241] Add code from kunena.special.upgrade.poll.php to kunena.special.upgrade.1.6.0.php
# [#19304] if Enable Help Page and Show help in Kunena is on no, has not effects on boardcode link
# [#19103] Language strings should be escaped in javascript
# [#19241] Fixes some little bugs in polls (part 2)
# [#19241] Fixes some little bugs in polls
+ [#19241] Add polls feature by applying changes from /branches/1.5-xillibit

05-Jan-2010 fxstein
+ [#19294] New module position: kunena_menu to allow custom Joomla menu to override default tabs
^ [#19236] Changed behaviour of category css suffix logic. Now adds new class to overide only specific features.
^ [#19289] Cleaned up and reformatted kunena.config.class.php
+ [#19289] New config validation function before save and after load to prevent unsupported values

05-Jan-2010 810
# [#19288] Fix some minor bugs on backend part 2
^ [#19295] Clean up and restructure backend

05-Jan-2010 xillibit
# [#19287] Modification for collect_smilies() and collect_ranks() - delete the index.php in display
# [#19287] Modification for collect_smilies() and collect_ranks()
# [#19234] Hide IP addresses from Moderators

05-Jan-2010 Matias
# [#19255] Fix XHTML validation errors while posting a message
# [#19277] Clean up and restructure post.php, part 2
# [#19277] Clean up and restructure post.php, part 3, fix attachments
^ [#16390] Update English language file, trim whitespaces
# [#19277] Clean up and restructure post.php, part 4, more fixes on posting
# [#19290] Bulk delete and move returns to main page
# [#19288] Fix regression: icons and emoticons conflict
# [#19288] Fix regression: PHP5.3 fix broke avatar upload
- [#19293] Remove deprecated configuration option: Word Wrap

05-Jan-2010 @quila
- [#19243] Make profile on left/right configurable - Revert change on message.php

04-Jan-2010 fxstein
^ [#19280] New standard "Registered Users Only" error message
+ [#19236] Updated colors: -green, -red, -orange, -blue, -grey & -pink for category css class suffix

04-Jan-2010 Matias
# [#19031] Quick reply shows > as &gt; in subject (part 2)
# [#19277] Clean up and restructure post.php and fix misc bugs

04-Jan-2010 @quila
+ [#19243] Make profile on left/right configurable

04-Jan-2010 Xillibit
# [#19107] Delete deprecated templates during install (part 2)
+ [#19107] Delete deprecated templates during install

04-Jan-2010 fxstein
# [#19257] Fixed regression: categories work again in backend

03-Jan-2010 severdia
# [#19255] Fixed validation errors. Now valid XHTML.

03-Jan-2010 fxstein
+ [#19236] Add css class suffix support for categories in various views
# [#18995] Undefined variables regression in pdf fixed
^ [#19250] Refactor remaining fb_xxxx files
- [#19254] Remove bottom forumjump dropdown
+ [#19236] Add category css class suffix predefines: -green, -red, -orange, -blue, -grey & -pink

03-Jan-2010 @quila
# [#19037] Add max avatar size into user profiles

03-Jan-2010 Xillibit
# [#18995] Undefined variables
# [#19080] Revert change on myprofile for configuration option "Show join date" has no effect

03-Jan-2010 Matias
# [#19043] Deprecated links to index2.php (part 2)
# [#15946] Fixed regression in: Super Admin in the User List

03-Jan-2010 810
# [#19229] User administration Adding an order + cleaned up
# [#19230] Fix some minor bugs on backend

02-Jan-2010 Matias
# [#19065] Removed many globals, fixed minor bugs
# [#19215] Remove redundant SQL in isModerator() calls

02-Jan-2010 810
+ [#19225] add hide images/files for guests
^ [#19225] add hide images/files for guests

02-Jan-2010 severdia
^ [#18780] Reformatted CSS files
# [#18780] Commented out errors. Both CSS files now validate at W3C

02-Jan-2010 fxstein
- [#19216] Removed Clexus PM integration
# [#19065] More frontend cleanup based on code analysis - fixed various bugs
# [#19065] cleaned up and reformatted myfprofile.php
+ [#19222] New feature: 'No Replies' tab added
# [#19065] final fix for warnings inside flat.php

01-Jan-2010 fxstein
# [#19065] fixed html bugs and warnings and reformatted flat.php
- [#19214] Removed patTemplate globally
# [#19065] Reformatted kunena.php
# [#19065] fixed html bugs and warnings and reformatted fb_write.php, post.php
# [#19065] cleaned up and reformatted views.php
# [#19065] cleaned up and reformatted pathway.php, showcat.php and view.php
# [#19065] cleaned up and reformatted recentposts.php
# [#19065] More frontend cleanup based on code analysis - fixed various bugs

01-Jan-2010 810
^ [#19213] rss image isn't always displayed in config backend
^ [#15946] Fix: Super Admin in the User List

01-Jan-2010 Matias
# [#19065] More frontend cleanup based on code analysis - fixed various bugs
# [#19065] Fixed bugs while posting caused by code cleanup
# [#19065] Remove a lot of unused or deprecated code as part of cleanup
# [#19065] Use JString instead of functions from PHP

01-Jan-2010 Xillibit
^ [#19200] Replace all trigger_dberror() or trigger_dbwarning()

31-Dec-2009 fxstein
# [#19065] More frontend cleanup based on code analysis - fixed various bugs

31-Dec-2009 Xillibit
# [#19201] Fix some php notice or fatal error on the trunk

29-Dec-2009 Matias
# [#19065] More frontend cleanup based on code analysis

28-Dec-2009 fxstein
# [#19065] More frontend cleanup based on code analysis - fixed various bugs

28-Dec-2009 Matias
# [#19065] More frontend cleanup based on code analysis - fixed various bugs

27-Dec-2009 Xillibit
# [#19031] Quick reply shows > as &gt; in subject

26-Dec-2009 fxstein
# [#19065] More frontend cleanup based on code analysis - fixed various bugs

25-Dec-2009 fxstein
# [#19065] More frontend cleanup based on code analysis - fixed various bugs

24-Dec-2009 Xillibit
# [#19030] URLs using HTTPS protocol are not working in img tag

24-Dec-2009 fxstein
+ [#19065] Add definitions of external functions (e.g. CB) to prevent warnings
# [#19065] Cleanup frontend based on code analysis - fixed various bugs

23-Dec-2009 Xillibit
^ [#18975] Backend: Show Avatar on Categories list option misleading
^ [#18902] Replace all remaining deprecated functions in PHP 5.3.x

23-Dec-2009 fxstein
^ [#19065] global rename of various kunena wide variables
^ [#19065] replaced depriciated split() with explode()
^ [#19065] remove depriciated new& construct
# [#19065] Fix regression: Missing backend menu and toolbar
# [#19065] Remove borken Joomla 1.5 dtd from manifest xml

22-Dec-2009 Xillibit
# [#19080] Configuration option "Show join date" has no effect

22-Dec-2009 fxstein
^ [#19090] Combine default and default_ex
# [#19065] Fix regression in uploaded files and images browser

21-Dec-2009 Xillibit
- [#19075] Remove group from userlist / user profile

21-Dec-2009 fxstein
# [#19086] Fix language file regression from 1.5.8
# [#19065] Cleanup backend html based on code analysis - fixed various bugs
# [#19065] Cleanup installer html based on code analysis - fixed various bugs
- [#19065] Removed backend plugin directory tree to remove warnings in unused code
# [#19065] Cleanup frontend based on code analysis - fixed various bugs
- [#19065] Removed old & unused split and merge code
- [#19065] Removed old & unused code in layout.php

20-Dec-2009 Matias
# [#19079] Fix broken layout with too long strings
^ [#18763] Update version info to 1.6.0-DEV

20-Dec-2009 fxstein
+ [#19064] new bbcodes for table, th, tr & td as well as module
# [#19065] fix various warnings and errors identified by zend studio code analysis
- [#19065] remove unsued kunena.pathway.old.php as part of cleanup

19-Dec-2009 Matias
^ [#15886] Merge from /branches/1.5-xillibit:1254-1256,1303-1307,1312-1313

18-Dec-2009 Xillibit
^ [#19033] User list and count shows also disabled users
^ [#19040] Most viewed profiles should use profile integration
# [#19043] Deprecated links to index2.php

17-Dec-2009 Xillibit
^ [#18767] Conflict with sh404sef language strings
# [#19027] Debug does not show MySQL error in trace
# [#19025] Moderators list is always using username, regardless of configuration option
# [#19026] Administration: wrong translations
# [#19022] Moderation: Merge shows extra slashes in topic list
# [#18973] Wrong My profile link in AUP integration

05-Dec-2009 Xillibit
# [#18902] Fixes for all remaining deprecated warning with PHP 5.3.x and remove split() and ereg() functions

04-Dec-2009 Xillibit
# [#18902] Fixes for deprecated warning with PHP 5.3.x and deprecated usage of split() functions
-->