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

Kunena 1.5.6

26-Aug-2009 Matias
# [#17785] JQuery is not loaded if JomSocial integration is enabled
# [#17786] Fix broken img tags in messages

Kunena 1.5.5

16-Aug-2009 Matias
^ [#15798] Update credits page
* [#17635] Fix Blind SQL Injection Exploit
* [#17636] Check that img tags contain allowed file extension

4-Aug-2009 Matias
+ [#17482] Add basic support for GroupJive permission handling

22-July-2009 fxstein 
^ [#17303] Update version info to 1.5.5
^ [#17629] JomSocial: Better compatibility with JQuery loads

Kunena 1.5.4

10-July-2009 Matias
# [#17168] My Profile: Uploading avatar does not work in PHP < 5.2.1
^ [#15798] Update credits page

8-July-2009 Matias
* [#17139] XSS vulnerability in BBCode parser

7-July-2009 fxstein
^ [#17127] Update backend error messages for failed installs and add link to wiki

7-July-2009 Matias
# [#17114] Extra backslash is not saved into database (missing \ in messages)
# [#17116] Latest post link in moved message does not work
^ [#15798] Update credits page

28-June-2009 Matias
# [#16994] Default_red/green/gray: pat-Warning and no styling
# [#16997] RSS Feed: all links are broken in K1.5
# [#16999] Registration link in profile box not working
# [#17001] Fix a set of small bugs reported in Kunena forums
# [#17002] JQuery loaded too late for JomSocial integration
# [#17003] Fix notice: Undefined variable: rImg
# [#17004] Pagination doesn't work on My Messages (My Profile)

17-June-2009 fxstein
# [#16877] Migrated rest of session logic into CKunenaSession class for additional cleanup (Merge from /trunk/1.0)

16-June-2009 fxstein
^ [#16835] Update package version info; new version name: Fale - portuguese for Speak

16-June-2009 Matias
# [#16829] Announcements: Description with some special characters does not work
# [#16833] Call to undefined method CKunenaTools::isjoomla15()

Kunena 1.5.3

15-June-2009 Matias
^ [#16326] Change minumum PHP requirement to 5.0.3 and MySQL requirement to 4.1.20
^ [#16390] Update English language file for K1.0.11 and K1.5.3
# [#16815] Kunena 1.5.2RC: Sub categories title floats on the right side (IE8)
# [#16814] Kunena 1.5.2RC: Editor: Multiple attachments not working
# [#16812] Kunena 1.5.2RC: SEF: Broken anchor links when viewing messages
# [#16811] Kunena 1.5.1b: Broken URL in emails (subscriptions, moderators, report message)

14-June-2009 fxstein
^ [#16807] Update version info to reflect 1.5.3 stable

14-June-2009 Matias
# [#16805] Internal: Minor layout issue in categories view

Kunena 1.5.2RC

13-June-2009 fxstein
+ [#16803] Update credits page
^ [#16501] Allow for menu link variations for JomSocial to get correct item id
# [#16793] Fix regression in manifest.xml that prevents successful installs
^ [#16260] Version info updated: New version name: Hable (Spanish for speak)

12-June-2009 Matias
# [#16120] Detect failed upgrades
^ [#16782] Add warning to the backend if installed version is SVN, DEV, APLHA, BETA, RC
# [#16790] Make installation more robust when there are errors
^ [#16791] Make defines which contain version information

11-June-2009 Matias
# [#16745] Collection of small bugs/typos
# [#16078] Remove broken Bad Words component support
^ [#16475] Upgrade jQuery to version 1.3.2
# [#16748] Add page number in meta description (For better Google results)
# [#16755] No images are shown in Windows server (backslashes in URL)
# [#16757] Forum stats: Popular 5 Threads shows threads where you don't have access to
# [#16775] Use better URL detection in BBCode parser
# [#16778] Internal: Preferred Message Ordering = Last post first didn't work

10-June-2009 Matias
# [#16728] Path missing in include (kunena.parser.php)
# [#16670] Category / Subcategory layout is slightly broken in IE

9-June-2009 Matias
# [#16721] Use always UTF8 while creating database tables
# [#16722] User Profile shows messages in categories where user has no permissions
+ [#16723] Add support for JoomFish
# [#16724] Fatal database error in default_ex/listcat.php:269

8-June-2009 Matias
# [#16695] Change URL query parameter in reply / quote from 'replyto' to 'id' for better SEF (accept also old one)
+ [#16696] SEF: use Joomla! 1.5 Router

7-June-2009 Matias
# [#16671] Board Categories have &nbsp; showing in them (PHP <5.2.3 issue?)
# [#16672] RSS Feed was broken: JDocumentRAW::addCustomTag() missing
# [#16677] BBCode parser bug crashes Kunena
# [#16678] Announcements: Read more if there is nothing to be read
# [#16679] Announcements: Calendar shows warning message in Add New
# [#16680] Administrator: Add missing translation to Rank management
# [#16664] Fix notices in BBCode parser, permissions handling and profile
# [#16689] My Profile: Fix urls for avatar upload
# [#16690] My Profile: Use redirect with message instead of extra page with timeout

6-June-2009 Matias
# [#16641] The User List Is Limited To 10 (out of #) Registered Users
# [#16642] Forum jump does not work if you go to Board Categories
# [#16645] Broken BBCode editor in My Profile (use simple version of Rolands editor instead)
# [#16662] Editor: Opening Boardcode link to the same window causes written message to disappear
# [#16663] Fix broken kunenaforum.min.js (used in CB integration)
# [#16526] Search: mb_substr() Unknown encoding warning, message bodies missing from results
# [#16666] Search doesn't work reliably for registered only+ forums
# [#16667] Administrator: Editing CSS does not work in Windows
# [#16668] Small fixes for CB integration
# [#16669] Internal: Spoiler tag broken

5-June-2009 Matias
+ [#16633] Allow many attachments in one message (no GUI yet, use edit instead)
# [#16636] Fix attachments to use Joomla! 1.5 API (add new CKunenaFile class)
# [#16596] Workaround for Joomla bug: Wrong attachment file permissions in FTP mode
# [#16634] Workaround for Joomla Bug: Overwriting files fail in FTP mode if they were not created by using FTP mode
# [#16632] Administrator: Replacing image with dummy image never worked in Uploaded Images Browser
# [#16635] Administrator: Hide index.php in Uploaded Files/Images Browser
# [#16637] Administrator: Defaut_red/green/gray should not be shown in Image Set list as they contain no images
# [#16638] Administrator: Editing CSS fails in FTP mode with strict permissions
# [#16640] Fix broken redirects

2-June-2009 Matias
# [#16567] Warn/detect possible problems in Community Builder integration
# [#16588] Avatar upload fails in FTP mode with strict permissions
# [#16590] If imageprocessor=none, large avatar can break layout
# [#16591] Configuration options for allowing avatars and upload do not work

31-May-2009 fxstein
^ [#16260] Version info updated to 1.5.2rc release candidate
# [#16260] Regression - Syntax error in recentpost.php fixed

25-May-2009 Matias
^ [#15784] Merge 1.0.10 fixes from revision 707 to 790
# [#15784] Internal: Replace all legacy calls after merge

21-May-2009 fxstein
# [#16490] mb_string related Joomla error on certain Joomla 1.5 installs

20-May-2009 fxstein
- [#16413] Remove empty version record logic
# [#16387] Remove notices from undefined variables in install class

20-May-2009 Matias
# [#16455] Editor does not work with notices turned on (and if translations are missing)
# [#16387] Remove many notices from undefined variables
# [#16477] If attachment upload fails (wrong filetype), you cannot add another attachment to your message
# [#16478] Font size in new editor does not work

19-May-2009 Matias
# [#16456] Quick reply: Empty subject if topic contains: "
# [#16460] Extra slashes in username for anonymous users or if Allow Name Change = yes
# [#16458] Search: Searching 'foo' is broken -- Result: \\\'foo\\\'
- [#16459] Moderator tools: Disable broken split
! Split will be enabled after it has been fixed. It didn't work and could break threads.
# [#16438] Session: allowed forums should be updated when user or category has been modified in backend
! All other cases should be covered except of changing user group in Joomla User Manager
# [#16457] Warnings during install if there is no permissions to copy files
+ [#16437] Show link to advanced search
# [#16466] Better UTF8 handling in many places in the code, many missing htmlspecialchars() added

18-May-2009 Matias
# [#16436] Internal: Default: Fix sub forums in category view, PHP error and CB integration in view.php
# [#16439] Missing translations in Kunena admin toolbar

17-May-2009 Matias
# [#16431] Wordwrap breaks Spoiler BBCode
^ [#16061] Roland's BBCode Editor
# [#16432] More smileys button on post message does not work
# [#16433] Posting message without name/email/subject: warning, but still load page
# [#15812] bbcode parser bug crashes server

16-May-2009 Matias
# [#16415] Deleted users show up in Forum Stats without a name
# [#16416] Do not allow users to send empty reports to moderators
# [#16418] Welcome message contains broken link to the documentation
# [#16419] Format birthday, register date and last login time
# [#16420] Changing karma from user profile redirects to invalid URL
# [#16387] Remove many notices from undefined variables
# [#16421] A few Kunena CSS rules gets overridden by the template

15-May-2009 Matias
# [#16409] Internal: Fix compatibility problems with CKunenaLink::GetLatestPageAutoRedirectURL() / HTML()
# [#16387] Remove many notices from undefined variables
# [#16411] PDF: Fix broken permission check
# [#16378] Community Builder integration: solve jQuery conflict (try 2)

14-May-2009 Matias
# [#16395] If you are not logged in, there are icons "New thread" and "Reply topic" in a locked forum
# [#16399] Pathway does not show locked forums as it should
# [#16400] Post new thread into moderated forum: do not forward into front page!
# [#16402] Internal: viewcat: small layout fix of subforums, warning in default theme

13-May-2009 Matias
# [#16378] Community Builder integration: solve jQuery conflict and small bug in profile links
# [#16379] Breadcrumb has no style in default green, gray and red templates
# [#16382] Internal: Translation is not working for unauthorized message if Registered Users Only = Yes
# [#16387] Remove many notices from undefined variables
# [#16388] Internal: Forum Jump forwards you to front page if SEF is turned off
+ [#16389] Community Builder integration: add profile integration to default theme

12-May-2009 Matias
^ [#16360] Replace $mainframe with $app =& JFactory::getApplication();
^ [#16361] Do not trust that $option has been set (use JRequest::getCmd('option') instead)
# [#16364] Parse error in admin.kunena.html.php

11-May-2009 Matias
^ [#15784] Merge 1.0.10 fixes from revision 681 to 706
# [#16340] Category list is missing in bottom of viewcat (default_ex)
# [#16106] Cannot reorder forums/subforums/categories in backend
# [#16355] Backend User Profile Manager uses a lot of memory if limit is not set
# [#16356] Showcat: Moving and deleting threads fails

10-May-2009 Matias
# [#16335] Internal: Moderator tools in Forum view do not work for global moderators

9-May-2009 Matias
# [#16268] Wrong redirect in Backend: Saving CSS failed
# [#16277] CSS issue: Large empty area with some Joomla themes
# [#16282] Internal: Auto overflow in messages broken in IE on some environments
# [#16281] Internal: Every thread contains word "Categories" in the title
# [#16289] Category with moved topic contains 1 NEW! message in Category View
# [#16321] Topic icons can be set in replies, but they have no effect
# [#16323] Internal: Category is missing from pathway in default_ex
# [#16324] External link to old location does not work if category has been removed but thread exists
# [#16325] Small changes to the English language file
^ [#16251] Disable content plugins by default (also if updating Kunena)
# [#16267] Upgrade thinks that 1.0.10 is older than 1.0.9

6-May-2009 Matias
# [#16283] Internal: Editing user in Kunena Backend Fails

5-May-2009 fxstein
^ [#16260] Update package version info

Kunena 1.5.1b

5-May-2009 Matias
+ [#16249] Community Builder integration: add trigger to modify userinfo
# [#16239] Missing argument 5 for KUNENA_get_menu()
# [#16237] Fix broken profile links
# [#16250] Fix slightly broken session handling
! Internal: Broken search fixed
! Added new class CKunenaSession
# [#16258] Internal: Missing stripslashes() in fb_write.html.php form
^ [#15784] Merge 1.0.10 fixes from revision 658 to 680
# [#15784] Internal: Fix few broken features after merge

4-May-2009 Matias
# [#16247] Internal: Sticky favorite thread in mylatest does not have styling
# [#16238] Internal: New installation fails on fatal error

3-May-2009 Matias
+ [#16227] Feature: Highlight sticky message in Recent Discussions
# [#16231] Fix Broken URLs for moved threads
+ [#16089] Community Builder integration improvements
# [#16107] Rank images not showing in backend

2-May-2009 Matias
# [#16221] Internal: fix crash in message.php, remove some PHP notices
# [#15953] Delete the Display Moderator, Guest etc above Avatar

30-April-2009 Matias
# [#16168] Remove HTML entities in the subjects of replies
# [#16185] Fix global $fbConfigs

28-April-2009 Matias
# [#16053] Remove PHP Notices from the frontend (part 2)
! Fixed many J!1.5 related bugs, frontend should be mostly working now
# [#15952] Delete a user and he gets the rank of admin or moderator

27-April-2009 Matias
^ [#15784] Merge 1.0.10 fixes from revision 602 to 651
^ [#15784] Merge from branch/1.5 from revision 624 to 627
# [#15784] Internal: Fix a few missing lines from merges

26-April-2009 Matias
# [#16117] Search: html_entity_decode() throws warning in some environments
# [#16074] Display real names in list of moderators when Username = no
# [#16096] Subcategories in Forum View do not obey ACL rules
# [#15640] Typos and misspellings in admin panel
# [#16140] Subforum list in Category View takes too much space
! Changes needed for default_ex CSS
# [#15956] Fix missing /div tags in post.php
# [#16069] Editor Preview does not work with sh404SEF
# [#16085] Fix mambot support in Joomla! 1.5
# [#15955] Partial fix for oversized words and linked images in signature
! Changes needed for default_ex CSS
# [#16105] Check if installation has failed and have better error messages

22-April-2009 Matias
# [#16082] Search: umlauts and other special chars in redirects and URL's are broken
# [#16080] Advanced Search: when there are selected cats, childforums will not be searched
# [#16090] BBCode is not working for subforum descriptions

21-April-2009 Matias
# [#15945] Updating My Profile does not work
# [#16068] Announcements: links are broken in sh404SEF
# [#16070] Forum Jump: links are broken in sh404SEF
# [#16092] File upload does not work in some servers
# [#16084] Search: Call to undefined function mb_substr()
# [#16081] Fatal error in kunena.config.class.php

20-April-2009 Matias
# [#16047] Replace all legacy functions which were introduced in 1.0.9 merge
! Fixed: Search and Advanced Search do not work
! Fixed: Community Builder: Integration not working
! Fixed: Community Builder: Default avatar is missing
! Fixed: User Profile not working
# [#16049] Global variables like $database conflicts with Legacy mode
! Renamed: $acl -> $kunena_acl, $database -> $kunena_db, $my -> $kunena_my
# [#16048] PHP notices are still hidden in frontend
# [#16053] Remove PHP Notices from the frontend
! Fixed: Missing translations
! Fixed: Saving configuration in backend fails
! Fixed: Kunena Authentication: Joomla group check is broken
! Fixed: Announcements: Smileys don't work
! Fixed: My Profile: Smileys don't work in signature
! Fixed: Joomla Plugins for content are not working

6-April-2009 fxstein
# [#15808] Fix broken New Category button in admin backend
# [#15840] Fix invalid document object on jomsocial integration

5-April-2009 fxstein
^ [#15801] Update package version info
^ [#15798] Updated credits page
# [#15800] Fix broken smilie class includes
# [#15805] Fix incorrect sort description in backend
# [#15807] Fix broken database upgrade script for version 1.0.5
^ [#15798] Updated credits page
# [#15800] Fix broken smilie class includes
# [#15805] Fix incorrect sort description in backend

Kunena 1.5.0a

3-April-2009 fxstein
^ [#15784] Merge final 1.0.9 fixes from revision 450 to 601

24-March-2009 fxstein
^ [#15627] Prepare for 1.5.0a Alpha release

24-March-2009 Matias
# [#15131] Changing password works again

21-March-2009 Matias
# [#15131] Attachment uploads are working again
# [#15131] Fixes for JRequest::getVar()

17-March-2009 Matias
# [#15131] Massive renaming of defines
# [#15131] Replaced many legacy functions
# [#15131] Replaced all occurences of $my_id with $my->id
# [#15131] Replaced global $database with local reference of JFactory::getDBO()
# [#15131] Replaced global $my with local reference of JFactory::getUser()
# [#15131] Replaced global $acl with local reference of JFactory::getACL()
# [#15131] Replaced remaining mosGetParam() with JRequest::getVar()

14-March-2009 Matias
# [#15131] Replaced many legacy functions, removed misplaced JText::_()

7-March-2009 fxstein
- [#15378] Drop CKunenaTemplate - not longer needed/supported
# [#15378] Re-apply fixes to class.kunena.php that did not make the merge

6-March-2009 Matias
# [#15378] Post merge cleanup: PHP errors

6-March-2009 Matias
# [#15378] Merge new changes from branch 1.5 to a tree with history (frontend)
# [#15378] Merge latest revision from Branch 1.0 back into 1.5 (reverse merge)

5-March-2009 fxstein
^ [#15378] Kunena branch 1.0.9 merged into 1.5 (r498)
# [#15378] Post merge cleanup: Incorrect list assignement in configuration

25-February-2009 fxstein
# [#15131] Initial cut at working Kunena 1.5 native backend

23-February-2009 fxstein
# Various syntax errors after initial port fixed


Kunena 1.0.9

3-April-2009 fxstein
# [#15781] Minor typo in language file: Missing closing tag ] for twitter url
# [#15782] Session category check regression

3-April-2009 Matias
# [#15671] CB integration: do not pass array() as reference
# [#15671] CB integration: extra parameters 'subject', 'messagetext' and 'messageobject' to be passed as reference into showProfile()

2-April-2009 fxstein
+ [#15724] Added bbcode and smilie support for forum headers AND descriptions in default AND default_ex
+ [#15724] Added bbcode and smilie support to forum announcements
^ [#15771] Minor change: Update sample data on fresh installs to contain bbcode in forum headers & descriptions instead of html

2-April-2009 Matias
^ [#15671] CB integration: Show Profile: Provide all needed information to CB plugin (Kunena Profile, username from message)
# [#15761] Added missing php close tags in lib/ where they were missing
# [#15567] 1.0.9 internal regression: security issue fixed in search

1-April-2009 fxstein
# [#15761] Regression fix: Added missing php close tag to class.kunena.php

1-April-2009 Matias
^ [#15671] CB integration: Changed CB Migration API
^ [#15671] CB integration: If internal fbprofile page is accessed, forward request to CB
+ [#15671] CB integration: New class CKunenaVersion, make lib/kunena.version.php safe to be included by external components
! New translations: make version string localized
+ [#15671] CB integration: make lib/kunena.user.class.php and lib/kunena.config.class.php self-contained

31-March-2009 Matias
# [#15567] Implement working advanced search: fix user search without search words
+ [#15671] CB integration: Source code documentation for CKunenaUserprofile class variables
+ [#15671] CB integration: Added lib/kunena.communitybuilder.php for CB compability
# [#15671] CB integration: Workaround for Community Builder: don't redefine $database
+ [#15671] CB integration: New class CKunenaCBProfile, use it for integration
+ [#15671] CB integration: Added callback for profile integration changes, code cleanup

30-March-2009 Matias
# [#15638] Latest member profile link causes fatal error
! Regression: fixed user count and latest user from Forum Stats (1.0.8 behaviour)
! Create new profile with default values if profile does not exist

30-March-2009 fxstein
+ [#15724] Add bbcode and smilie support to forum headers in default_ex
# [#15139] Fixed broken IP address lookup link

29-March-2009 Matias
# [#15677] Fix UI issues: Showcat does not validate
# [#15677] Fix UI issues: Latestx shows "Show last visit" option for anonymous users
# [#15677] Fix UI issues: Latestx "x time ago" options are not fully ordered
# [#15677] Fix UI issues: Latestx, showcat do not validate for moderators
# [#15677] Fix UI issues: Message view does not mark new messages by green icon
# [#15677] Fix UI issues: Message view contains some code for Quick Reply even if it's disabled

28-March-2009 fxstein
# [#15702] Fix broken RSS feed on Joomla 1.0.x without SEF
+ [#15705] Short names for external module positions for Joomla 1.0.x
  kunena_profilebox -> kna_pbox, kunena_announcement -> kna_ancmt,
  kunena_msg_'n' -> kna_msg'n', kunena_bottom -> kna_btm

28-March-2009 Matias
# [#15638] Latest member profile link causes fatal error
! User profile detects now nonexistent users and user profiles
# [#15639] User list incomplete
! Only users who have Kunena user profile are listed
# [#15567] Implement working advanced search: add backwards compability for old templates

27-March-2009 Matias
# [#15677] Fix UI issues: IE7 bug having collapsed tabs
# [#15677] Fix UI issues: Big empty space on some templates
# [#15677] Fix UI issues: Spoiler icon has wrong URL if Joomla is not in document root
# [#15677] Fix UI issues: Open Kunena.com to a new window/tab (=external link)
# [#15677] Fix UI issues: Show announcements also in latestx

26-March-2009 Matias
# [#15567] Fix pagination in search
+ [#15671] Add API for changing user settings in CB (part 1)
! Renamed fbUserprofile to CKunenaUserprofile. It can be found from lib/kunena.user.class.php
# [#15667] Missing argument 3 for mb_convert_encoding
# [#15154] Auto linked email addresses contain two slashes in front of the address

24-March-2009 fxstein
# [#15625] Prepare for 1.0.9 build
# [#15624] Fix language file re-declaration

22-March-2009 Matias
# [#15565] Board Categories showed only public categories
# [#15566] Fireboard 1.0.1 didn't contain directory com_kunena/uploaded
+ [#15567] Implement working advanced search

22-February-2009 Matias
# [#15157] Empty messages in Joomla 1.0 part 2
# [#15170] Add backward compability to FB 1.0.5: Same meaning for 0 in latestcategory

21-February-2009 Matias
# [#15157] Empty messages in Joomla 1.0
# [#15162] Two same smileys in a row do not work
# [#15163] Fetch Kunena template from Joomla theme fails with warnings

20-Februray-2009 fxstein
# [#15151] Ensure fbConfig is array during legacy config file write - Thx JoniJnm!

20-February-2009 Matias
# [#15148] Post emails for moderators: name missing
# [#15150] Don't send email to banned users

19-Februray-2009 fxstein
# Incorrect permissions handling fixed

19-Februray-2009 Matias
# Search: Fixed SEF/no SEF issues. Pagination and links should now work for all
# Pathway: comma (,) was missing between usernames if there were no guests
# Thread View: Minor fix in pagination

18-Februray-2009 Matias
# Broke PHP4 compability, added emulation for htmlspecialchars_decode()
# Pathway: removed multiple instances of one user, use alphabetical order

Kunena 1.0.8

17-Februray-2009 fxstein
# Missing category check added to default_ex Recent Discussions tab
  Backend Show Category setting in Recent Posts can now limit the categories displayed
+ Added category id to display in backend forum administration
# Integration dependent myprofile page fixes
- Remove broken "Close all tags" when writing the message in all places
+ Installer upgrade added for recent posts categories setting
# minor naming change in 1.0.8 upgrade procedure

17-Februray-2009 Matias
# Strip extra slashes in preview
# Regression: Quick Reply Cancel button does not work
# Backend: you removing user's avatar dind't work
# My Profile: Wrong click here text after uploading avatar

16-Februray-2009 fxstein
# Fix the fix - url tags now have http added only when needed but then for sure
# jquery Cookie error: Prevent JomSocial from loading their jquery library

16-February-2009 Matias
# Fix broken link in "Mark all forums read"
# Regression: Moderator tools in showcat didn't work
# Changed all "Hacking attempt!" messages to be less radical and not to die().
# Regression: Unsticky and Unlock didn't work
^ Changed behavior of Mark all forums read, Mark forum read - no more extra screen
^ Changed behavior of Subscribe, Favorite, Sticky, Lock - no more extra screen
# Fixed broken layout in FireFox 2

15-February-2009 Matias
^ Change time formating in announcements
# Regression: Removed &#32 ; from report emails
# Fixed report URLs
# Regression: Typos in template exists checks
# Regression: Missing css class in default template
# Removed extra slashes in headerdesc, moved it to the right place in showcat
^ Tweaks in css, fix dark themes
# Missing define for _POST_NO_FAVORITED_TOPIC in kunena.english.php
# Show user only once in pathway
# Fix broken search pagination
# Fix Search contents

15-Februray-2009 fxstein
# Proper favicon in menu for Joomla 1.0.x
+ Add missing user sync language string to language file
- load and remove sample data images removed as functionality has been depriciated
# Regression: Proper Joomla 1.5 vs 1.0 detection during install
^ Backend Kunena Information updated
^ Initial base for new 3rd party profile integration framework
- Removal of legacy CB integration for profile fields. New functionality
  through plugin for all 3rd party profile providers
# Missing http:// on url codes for url that do not start with www

14-Februray-2009 fxstein
# Added missing SEF call to mark all forums read button
+ Replace com_fireboard with com_kunena in all messages and signatures
^ Preview button label now with capital 'P'
# Incorrect button css classes on write message screen fixed
^ Extra spacing for text buttons to conform with Joomla button style
# Regression: Disabled the submit button because of incorrect type - fixed
# Regression: Moderator tools got disabled during relocation - fixed
+ Missing search icon in default_ex userlist added
+ Missing css styling added to forum header description
^ Forumjump: put Go button to the right side of the drop down category list

14-February-2009 Matias
# Try 2: Use default_ex template if current template is missing
^ Changed Quick Reply icon.
^ Use the same style in all buttons. CSS simplifications, fixes

13-Februray-2009 fxstein
# Minor bug fix in automatic upgrade that re-ran 1.0.6 portion unneccesarily

13-February-2009 Matias
# Regression in r381: New pathway was slightly broken, also some css was missing
# Fixed sender in all emails. It's now "BOARD_TITLE Forum"

12-Februray-2009 fxstein
^ TOOLBAR_simpleBoard renamed to CKunenaToolbar
- Legacy FB sample data code removed
+ New Kunena sample data added for new installs

12-February-2009 Riba
^ Pathway: Removed hardcoded styling
^ Pathway: Edited html output and CSS styles for easier customization
# Pathway: Removed comma separator after last user

12-February-2009 Noel Hunter
# Changes to icons in default_ex for transparency, visibility

12-February-2009 Matias
^ Improved pagination in latestx
^ Improved pagination in showcat
^ Improved pagination in view
+ Added pathway to the bottom of the showcat page
+ Added pathway to the bottom of the view page
^ Improved looks of the showcat page
^ Improved looks of the listcat page
^ Improved looks of the view page
# Missing addslashes for signature in admin.kunena.php
# Regression in r362: Broke UTF-8 letters in many places
^ Moved Thread specific moderator tools from message contents to action list

11-February-2009 fxstein
# fixed and rename various Joomla module positions for Kunena:
  kunena_profilebox, kunena_announcement, kunena_bottom
  in addtion to the previously changed kunena_msg_1 ... n
+ Increase php timepout and memory setting once Kunena install starts
# updated database error handling for upgrade base class
# minor language file cleanup, removed none ascii characters
+ additional language strings for initial board setup on fresh install

11-February-2009 Matias
# default: No menu entry pointed to Categories if default Kunena page wasn't Categories
# Huge amount of missing slashes added and extra slashes removed from templates
# Fixed broken timed redirects

10-February-2009 fxstein
# Incorrect error message on version table creation for new installs

10-February-2009 Matias
# Regression in r338: Broke My Profile
# Regression in r338: Broke FB Profile
# Regression in r246: Broke Quick Reply for UTF-8 strings
^ Show Topic in Quick Reply
# Do not add smiley if it is attached to a letter, for example TV:s, TV:seen

9-February-2009 fxstein
# Broken RSS feed in Joomla 1.0.x fixed
^ FBTools Changed to CKunenaTools
# Updated README
# Regression: Accidentially modified MyPMSTools::getProfileLink parameters

9-February-2009 Noel Hunter
# Significant leading and trailing spaces in language file replaced with
  &#32; to avoid inadvertant omission in translation

9-February-2009 severdia
# English: Spelling & grammar corrected
# README.txt: Spelling & grammar corrected

9-February-2009 Matias
^ Changed email notification sent to subscribed users
^ Changed email notification sent to moderators
^ Changed email when someone reports message to moderators
# Topic was slightly broken in default_ex (moved, unregistered)
^ Shadow message (MOVED) will now have moderator as its author
# Regression: moving messages in viewcat didn't work for admins
# New user gets PHP warning
# No ordering for child boards

8-February-2009 fxstein
+ Community Builder 1.2 basic integration
+ Make images clickable and enable lightbox/slimbox if present in template
^ changed $obj_KUNENA_search to $KunenaSearch to match new naming convention
^ clickable images and lightboxes only on non nested images; images within URL
  codes link to the URL specified
^ fb_1 module position renamed to kunena_profilebox to match new module position naming
# Avoid forum crash when JomSocial is selected in config but not installed on system

8-February-2009 Matias
# Image and file attachments should now work in Windows too
# Fix error when deleting message(s) with missing attachment files
# Fix error when deleting message(s) written by anonymous user
# Regression: fixed an old bbCode bug again..
# Fixed error in search when there are no categories

7-February-2009 Matias
# Moderators can now move messages outside their own area (no more Hacking Attempt!)
# Remove users name and email address from every message in the view (Quick Reply)
# Fix "Post a new message" form when email is mandatory
# Allow messages to be sent even if user has no email address
# Require email address setting wasn't enforced when you posted a message

6-February-2009 fxstein
+ additional jomSocial CSS integration for better looking PM windows
^ $fbversion is now $KunenaDbVersion
+ additional db check in class.kunena.php
+ basic version info on credits page
+ enhanced version info including php and mysql on debug screen
# added default values for various user fields in backend save function
# fix broken viewtypes during upgrade and reset to flat
# modified logic to detect Kunena user profiles to avoid forum crash in rare cases
# remove avatar update from backend save to avoid user profile corruption
^ Search class renamed to CKunenaSearch
- Removed depriciated threaded view option from forum tools menu

6-February-2009 Matias
# Use meaningful page titles, add missing page titles
^ Small fixes to CSS
# Regression, done this again: Removed all short tags: < ?=

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

