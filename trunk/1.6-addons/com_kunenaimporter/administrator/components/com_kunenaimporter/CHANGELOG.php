<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
die();
?>
<!--

Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the Kunena Importer, including beta and release candidate versions.

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

KunenaImporter 1.6.0-RC2

6-September-2010 Matias
# [#20178] User mapping: Misc fixes and optimizations
# [#20178] User mapping: Update all user information when user gets mapped manually (no need to re-run importer)

4-September-2010 Matias
# [#20178] User mapping: Greatly improve performance by making own task from basic user mapping
# [#20178] User mapping: Improve performance by preloading needed user mapping when migrating tables
# [#20178] User mapping: Better user mapping reports
# [#20178] User mapping: Improve algorithm to map users who have slightly different information in Joomla
# [#20178] User: Fix last logged in info if user has posts, but has never logged in (lost during previous migration?)
+ [#20178] User mapping: Add filters by ignored users and never logged in

3-September-2010 Matias
# [#20178] SMF2: Fix subscriptions export
# [#20178] Import: Map all userids from external to Joomla ids
# [#20178] Import: Use negative userids if user isn't mapped (allows late mapping)
# [#20178] User mapping: Fatal error, trying to access protected variable
# [#20178] User mapping: Fix pagination
# [#20178] User mapping: Filter by mapped/unmapped/all

2-September-2010 Matias
# [#20178] SMF2: Better version detection
# [#20178] SMF2: Import some configuration options
# [#20178] SMF2: Cleanup text on categories, messages

1-September-2010 Matias
+ [#20178] Create new exporter for SMF2 (standalone)
# [#20178] phpBB3: Fix subscriptions export
- [#20178] Remove database options from configuration

KunenaImporter 1.6.0-RC1

24-August-2010 Matias
^ [#20178] phpBB3: Update exporter for Kunena 1.6 (add some missing fields, do not add slashes)
+ [#20178] phpBB3: Use rokbridge to map users between phpBB and Joomla (most reliable way)
# [#20178] phpBB3: Use right field for username if migrated from SMF
^ [#20178] Minimum Kunena version requirement: 1.6.0RC2 build 3251

22-August-2010 Matias
^ [#20178] Cleanup all files, remove empty directories, files, improve installer etc

16-August-2010 Xillibit
+ [#20178] Support partial for ccboard and agora

22-Apr-2010 Matias
# [#20178] Fix [url] tag (contained extra information and were not parsed)
# [#20178] Some posts were modified 0 minutes ago
# [#20178] Importer status was broken (showing 0% all the time)
# [#20178] Modified by (userid) was not mapped to Joomla

21-Apr-2010 Matias
# [#20178] Add views to aid on users import
# [#20178] phpBB3: Fix slashes on category names
# [#20178] phpBB3: Fix never logged in date
# [#20178] Fix installer: usermap table was not created

19-Apr-2010 Matias
+ [#20178] Detect rokbridge (phpBB3)

17-Apr-2010 Matias
# [#20178] Make more stable against database errors, including failed detection of external forum
# [#20178] Detect and prevent importing Kunena into itself

03-Apr-2010 Matias
# [#20178] Restructuring, add build system, add keywords for every file etc

30-Aug-2009 Matias
# [#17875] Fix all errors and warnings found in Eclipse

4-Aug-2009 Matias
+ [#17485] Initial version of Kunena Importer

 -->