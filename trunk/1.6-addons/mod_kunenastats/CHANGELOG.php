<?php
/**
* @version $Id$
* KunenaStats Module
* @package Kunena Stats
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
?>
<!--
Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the KunenaStats module, including beta and release candidate versions.
Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

KunenaStats 1.6.3

17-February-2011 Matias
^ [#19002] Updated version to 1.6.3-DEV
# [#24959] Always initialize session if allowed=na

KunenaStats 1.6.2

23-January-2010 LDA(svens)
^ [#24560] update de-DE (thanks rich)
^ [#24560] update ru-RU (thanks Zarkos)
^ [#24560] update fi-FI (thanks Mortti)

25-December-2010 fxstein
^ [#20095] Updated version info to 1.6.2 stable
^ [#23293] Replace hard coded version info in language files with auto expansion
+ [#23293] Add version info auto expansion to builder
^ [#23293] Update min Kunena version requirements to Kunena 1.6.2

24-December-2010 fxstein
^ [#20095] Merged revisions 3803-4058 from /branches/1.6-addons-LDAsvens-language-20101030

21-December-2010 Severdia
^ [#23900] Made file naming and folder structure consistent among Kunena modules
# [#23900] Made CSS class names consistent with prefix

31-October-2010 Xillibit
# [#20095] Profile links in topthanks doesn't put you to the right user
^ [#20095] Update version info to 1.6.0RC3

22-December-2010 svens(LDA)
+ [#23293] add fi-FI (thanks Mortti)

19-November-2010 svens(LDA)
+ [#23293] add es-ES (thanks Alakakentu)

12-Nov-2010 svens(LDA)
+ [#23293] add de-DE (thanks rich)

30-October-2010 svens LDA
^ [#22975] update ru-RU (thanks ZARKOS)

10-October-2010 Matias
+ [#20095] Added ru-RU translation (thanks ZARKOS)

17-September-2010 Xillibit
+ [#20095] Added fr-FR translation

KunenaStats 1.6.0-RC2

31-Aug-2010 fxstein
^ [#20095] Updated build files for RC2 release

30-Aug-2010 fxstein
+ [#20095] Add missing Kunena language load
+ [#20095] New parameter to limit title output (topic subject or username)
^ [#20095] Update module description
^ [#20095] Update version info to 1.6.0RC2

29-Aug-2010 fxstein
+ [#20095] Additional language string for posts as table header for top posters
+ [#20095] Format large numbers

29-Aug-2010 @quila
+ [#20095] Added images bar_bg and change to show full grey bar
+ [#20095] Added images arrow and bar
+ [#20095] Added mod_kunenastats.css file
+ [#20095] Added style to various display type

29-August-2010 Matias
# [#20095] Fix Undefined property in Top Thank You

21-August-2010 Matias
# [#20095] Changed language strings to obey J1.6 naming convensions
# [#20095] Better formatting in php and html files
# [#20095] Updated version to 1.6.0-RC1
# [#20095] Simplify code, better API usage

08-August 2010 Xillibit
# [#20095] Show messages no datas when there is nothing ot display

11-July 2010 Xillibit
# [#20095] Lot of changes to show thank you, some fixes and changes on language file

9-Apr 2010 Xillibit
^ [#20095] Removed $kunena_config variable from CKunenaLink functions

3-Apr 2010 Xillibit
^ [#20095] Set keyword ID on all files
+ [#20095] Create initial structure

-->