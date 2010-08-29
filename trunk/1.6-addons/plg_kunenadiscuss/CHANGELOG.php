<?php
/**
 * @version $Id$
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// no direct access
die( '' );
?>
<!--

Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the Kunena Discuss Plugin, including beta and release candidate versions.
Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Kunena Discuss Plugin 1.6.0-RC2

29-Aug-2010 Xillibit
# [#20084] Put db->quote() on some values in queries to avoid issues

29-Aug-2010 Matias
# [#20084] Fix application scope detection
^ [#20084] Added error message when mapping category to 0

21-Aug-2010 Matias
^ [#20084] Change version to 1.6.0-RC2
^ [#20084] Better detection on Kunena, minimum required version is Kunena 1.6.0-RC2

Kunena Discuss Plugin 1.6.0-RC1

11-Aug-2010 Matias
^ [#20084] Change version to 1.6.0-RC1

04-Aug-2010 Matias
^ [#20084] Use some new API functions from Kunena 1.6
^ [#20084] Use new table schema from Kunena 1.6
# [#20084] Fix articles which do not have tag - deleted binding between article and post

09-Apr-2010 Matias
^ [#20084] Removed $kunena_config variable from CKunenaLink functions to make it work with latest trunk

08-Apr-2010 Matias
^ [#20084] Update template

02-Apr-2010 Matias
+ [#20084] Initial version

 -->