<?php
/**
 * @version $Id$
 * KunenaLogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
die ();
?>
<!--
Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the KunenaLogin module, including beta and release candidate versions.
Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

KunenaLogin 1.6.3-DEV

17-February-2011 Matias
^ [#19002] Updated version to 1.6.3-DEV
# [#24959] Always initialize session if allowed=na

KunenaLogin 1.6.2
20-February-2011 svens(LDA)
^ [#24847] update hu-HU (thanks pedrohsi)

18-February-2011 svens(LDA)
^ [#24847] update fi-FI (thanks Mortti)

23-January-2010 LDA(svens)
^ [#24560] update de-DE (thanks rich)
^ [#24560] update ru-RU (thanks Zarkos)
^ [#24560] update fi-FI (thanks Mortti)

25-December-2010 fxstein
^ [#22073] Update version info to 1.6.2 stable
^ [#22073] Replace hard coded version info in language files with auto expansion
+ [#22073] Add version info auto expansion to builder
^ [#22073] Update min Kunena version requirements to Kunena 1.6.2

24-December-2010 fxstein
^ [#22073] Merged revisions 3803-4058 from /branches/1.6-addons-LDAsvens-language-20101030

21-December-2010 Severdia
^ [#23900] Made file naming and folder structure consistent among Kunena modules
# [#23900] Made CSS class names consistent with prefix

22-December-2010 svens(LDA)
^ [#23293] updated fi-FI (thanks Mortti)

12-Nov-2010 svens(LDA)
^ [#23293] updated de-DE (thanks rich)

04-Nov-2010 svens(LDA)
+ [#22975] add de-DE (thanks rich)

30-October-2010 svens LDA
^ [#22975] update ru-RU (thanks ZARKOS)

17-September-2010
+ [#20178] Added fr-FR translation

16-September-2010 Matias
+ [#20178] Added fi-FI (thanks Mortti), hu-HU (pedrohsi), pl-PL (Colly), ru-RU (ZARKOS)

10-September-2010 Xillibit
^ [#22073] Update language because some stings are presents two times

31-Aug-2010 fxstein
^ [#22073] Updated build files for RC2 release

26-Aug-2010 fxstein
- [#22073] Remove duplicate language load call in display()

29-Aug-2010 @quila
- [#22073] Removed hardcoded ID
+ [#22073] Added horizontal style

29-Aug-2010 Matias
# [#22073] Fix logout showing up even if not logged in

26-Aug-2010 fxstein
+ [#22073] Load Kunena language file during init to allow full access to API and language strings
# [#22073] Updated language strings
- [#22073] Remove realname vs username display setting and leverage Kunena and its own config
^ [#22073] Updated default avatar size to 128px
^ [#22073] Updated description to match other Kunena modules in style and format
^ [#22073] Use KunenaUser vs JUser to get proper name style in all cases

26-Aug-2010 Matias
^ [#22073] Fix fatal error when module is loaded twice (mode code into ModKunenaLogin class)

25-Aug-2010 Matias
^ [#22073] Remove Zend warnings, small improvements
^ [#22073] Change minimum required Kunena build to 3296

25-Aug-2010 @quila
+ [#22073] Initial module version

-->