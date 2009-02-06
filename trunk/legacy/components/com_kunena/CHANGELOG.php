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
defined( '_VALID_MOS' ) or die( 'Restricted access' );
?>

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

Kunena 1.0.8

6-February-2009 fxstein
+ additional jomSocial CSS integration for better looking PM windows

6-February-2009 Matias
# Use meaningful page titles, add missing page titles
^ Small fixes to CSS

5-February-2009 Matias
# Try 2: Work around IE bug which prevented jump to last message
# Removed odd number that was sometimes showing up
^ Added Kunena Copyright to all php files

4-February-2009 Noel Hunter
^ Changes to colors in kunena.forum.css to prevent inheritance of colors
  from joomla templates making text unreadable
^ Changes to kunena.forum.css to expand whos-online in pathway for
  longer lists, reduce line height, additional color fixes
^ Remove centering from code tags in parser, to fix ie bug

4-February-2009 fxstein
^ font size regression fix: reply counts in default_ex back to x-large
^ New ad module position logic. Much Simplified with support for n module positions: kunena_msg_1
  through kunena_msg_n. n being the number of posts per page.

4-February-2009 Matias
+ First version of CKunenaUser(s) class
# Backend, User Profile: include path fixed
^ Backend, User Profile: Removed bbcode, it didn't work
^ Removed flat/threaded setting, it wasn't used
# Backend, Ranks: fixed bug when you had no ranks
# You may now have more than one announcement moderator
# Removed all short tags: < ?=
# Fixed My Profile / Forum Settings / Look and Layout

3-February-2009 fxstein
# Reverse sort bug fix. Newest messages first now work in threads.
# Minor regression and syntax fixes
# Correct last message link when reverse order is selected by the user

2-February-2009 Noel Hunter
^ Change all references from forum.css to kunena.forum.css
+ If kunena.forum.css is present in the current Joomla template css directory,
  load it instead of Kunena template's kunena.forum.css
^ Change font sizes in kunena.forum.css for default_ex from px to relative sizes (small, medium, etc)
^ Change names in for forum tools in kunena.forum.css from fireboard to kunena, add z-index:2 to menu
^ Fix css typos for forum tools menu, add z-index
- Removed unused group styles from kunena.forum.css, and associated images files from default_ex images

2-February-2009 Matias
^ Move forced width from message text to [code] tag
^ Remove confusing link from avatar upload
^ default_ex: Update latestx redirect to use CKunenaLink class

2-February-2009 fxstein
^ Removed addition left over HTML tags and text for prior threaded view support in profile
# htmlspecialchars_decode on 301 redirects to remove &amps from getting into the browser URL
^ fb_Config class changed to CKunenaConfig, boj_Config class changed to CKunenaConfigBase
+ new CKunenaConfig class functionality to support user specific settings
^ kunena_authetication changed to CKunenaAuthentication

1-February-2009 Noel Hunter
^ Use default_ex if current template is missing
+ Add title tags to reply and other buttons in "default" template
^ Work around ie bug which prevented jump to last message

1-February-2009 Matias
# xhtml fixes
# My Messages will redirect to Last Messages if user has logged out
# Regression: Fix broken icon in Joomla Backend

31-January-2009 fxstein
^ default_ex jscript and image cleanup

31-January-2009 Matias
# Additional BBCode fixes

30-January-2009 fxstein
# Additional jQuery fixes
- Removed outdated jquery.chili 1.9 libraries (different file structure)
+ Added new jquery.chili 2.2 libraries
^ Moved jquery.chili jscripts to load at the bottom of the page for faster pageloads
+ add jomSocial css in header when integration is on to enable floating PM window

30-January-2009 Matias
# Regression: favorite star didn't usually show up
+ default_ex: Added grey favorite icon for other peoples favorite threads

29-January-2009 fxstein
# Fixed incorrect MyProfile link logic with various integration options
- Removed unsusable threaded view option

29-January-2009 Matias
# Regression: Backend won't be translated

28-January-2009 fxstein
# Fixed broken display with wide code
# Fixed jQuery conflicts caused by $() usage
+ PHP and MYSQL version checks during install

28-January-2009 Matias
# Replace all occurences of jos_fb_ with #__fb_
# Don't allow anonymous users to subscribe/favorite
# Do not send email on new post if the category is moderated
# Fix broken tables fb_favorites and fb_subscriptions
# Regression from Kunena 1.0.7b: avatar upload page internal error
# Avatar upload was broken if you didn't use profile integration
# default_ex: My Profile internal link was wrong

27-January-2009 fxstein
# BBCode fix for legacy [code:1] support

Kunena 1.0.7 beta

26-January-2009 fxstein
+ JomSocial userlist integration for Kunena userlist link in front stats
- Remove old unused legacy code
^ Fixed broken PDF display
^ Corrected upgrade logic order

26-January-2009 Matias
# default_ex: Link to first unread message was sometimes broken
^ view: Message is marked new only if thread hasn't been read
+ kunena.credits.php: Added myself
# Stats should work again (typos fixed)
* My Profile: My Avatar didn't have security check for anonymous users

25-January-2009 fxstein
+ Basic JomSocial Integration
^ updated jquery to latest 1.3.1 minimized
^ fb_link class changes to CKunenaLinks
# Minor typo in include paths fixed
^ kunena.credits.php: Updated credits page
^ Various links updated
+ Kunena logos added to default and default_ex tamplates
# smile.class.php: parser references fixed

25-January-2009 Matias
# Stats: Visible even if they were disabled
# Stats: Wrong count in topics and messages
# Stats: Today/yesterday stats didn't include messages between 23:59
  and 00:01.
^ Stats: Optimized SQL queries for speed and saved 11-20 queries
! DATABASE UPDATED: new keys added to fb_messages and fb_users
# Emoticons: Broken "more emoticons" pop up in IE7.
# Forum Tools: Fixed CSS rules in default_ex
^ Anonymous user cannot be admin, saves many SQL queries
# Removing moved thread (or written by anonymous user) didn't
  work in showcat
+ view: Make new messages visible (green topic icon).
+ default_ex: Show number of new messages (just like in category view).
+ default_ex: Jump to first new message by clicking new message indicator.
! Current behaviour is "first message after logout or mark all forums read".
^ showcat, latestx: Use faster query to find all messages in a thread.
# Message posted notification page redirects after you click a link

24-January-2009 Matias
# Fixed over 100 xhtml bugs
^ No default size for [img]
^ Category parent list: jump to Board Categories with "Go" button
^ Forum stats show users in alphabetical order

01-January-2009 fxstein
+ Initial fork from FireBoard 1.0.5RC3

