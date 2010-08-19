<?php
/**
* @version $Id$
* KunenaLatest Module
* @package Kunena latest
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

// no direct access
die( '' );
?>
<!--
Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the KunenaLatest module, including beta and release candidate versions.
Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

KunenaLatest 1.6.0-RC2

19-Aug-2010 fxstein
^ [#20081] Change version to 1.6.0-RC2
# [#20081] Fix: English language file updated
+ [#20081] Add default display model if none of the valid ones have been selected.
^ [#20081] Default NEW indicator moved to language file
^ [#20081] Default NEW indicator changed to (NEW) - like Kunena
^ [#20081] Default date format override move to language file
^ [#20081] Default date format override changed to empty - sample moved to tooltip
^ [#20081] All displaymodel options lowercase
+ [#20081] Additional language strings for various errors
+ [#20081] Add category subscriptions view
- [#20081] Disabled unsupported view types for now

KunenaLatest 1.6.0-RC1

13-Aug-2010 Xillibit
^ [#20081] Little change to show properly the latest messages

11-Aug-2010 Matias
^ [#20081] Change version to 1.6.0-RC1
^ [#20081] Remove elements from module and use the ones in Kunena
^ [#20081] Format php files

08-Aug-2010 Xillibit
^ [#20081] Wrong image link for locked icon
# [#20081] Show messages in right way when you want show only messages

24-July-2010 Xillibit
^ [#20081] Not display posts when the forum is offline (thanks etusha)

11-July-2010 Xillibit
+ [#20081] Add setting to show latest messages or topics

28-Jun-2010 Xillibit
# [#20081] Change link more recent topics when you change view in module config

27-Jun-2010 Xillibit
# [#20081] Put a list to choose the different views to display
# [#20081] Add option to show nothing instead to have avatar or topicicons

27-Jun-2010 Xillibit
# [#20081] Add a configuration setting to choose the view to display (latest post, favorites, subcriptions...)

25-Jun-2010 Xillibit
# [#20081] Fix issues on topic icons and remove all contants depencies
# [#20081] Sow first part of content and add configuration settings for this

24-Jun-2010 Severdia
^ [#20081] Vertical position change, new CSS
^ [#20081] Languages fixes
^ [#20081] Changed date format

24-Jun-2010 Matias
# [#20081] Get date format from configuration option

23-Jun-2010 Matias
# [#20081] Better detection for K1.6
# [#20081] Fix Zend warnings
^ [#20081] Cleanup files
# [#20081] Fix date to use CKunenaTimeformat

19-May-2010 Xillibit
# [#20081] Set configuration setting to choose between categories in or not in
# [#20081] Fix issue after changes which doesn't take care of categories selected

18-May-2010 Xillibit
# [#20081] Leverage directly the CKunenaLatestX instead to have a copy of the class
# [#20081] Put configuration setting to choose vertical or horizontal display type (need css)
# [#20081] Strip BBCodes when you over the thread title

29-Apr-2010 Xillibit
# [#20081] Add option to choose between topic icon or avatar
# [#20081] Add option for choose which categories shows
# [#20081] Add option to define the avatar size

26-Apr-2010 Matias
# [#20081] Small layout fixes
# [#20081] Show the same items as Recent Topics in Kunena

18-Apr-2010 Matias
# [#20081] Fixed wrong avatar if user had been deleted

17-Apr-2010 Matias
# [#20081] Fixed fatal error when there are no latest messages
# [#20081] Do not limit latest messages by time, show last post time instead of thread start time
# [#20081] Even more layout fixes

15-Apr-2010 Matias
# [#20081] Removed user posts configuration option
# [#20081] Improve database error detection
^ [#20081] Show always author name
^ [#20081] More layout fixes

14-Apr-2010 Ron
^ [#20081] Layout fixes

13-Apr-2010 Matias
^ [#20081] Changed module name to mod_kunenalatest, cleanup code and css
^ [#20081] Updated language file to use J1.6 style
+ [#20081] Add missing translations, improve others
+ [#20081] Use new API and profile integration, fix links
# [#20081] Fixed bug where no latest posts were shown to the user
# [#20081] Fixed favorites

9-Apr-2010 Xillibit
^ [#20081] Little change to avoid try to list the results when there are nothing to display

2-Apr-2010 Xillibit
^ [#20081] Set new Id to all files and put build files
^ [#20081] Littles changes on the module filenames
+ [#20081] Initial module structure

-->