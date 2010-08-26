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

KunenaLogin 1.6.0-RC2

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