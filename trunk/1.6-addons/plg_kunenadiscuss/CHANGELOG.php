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

Kunena Discuss Plugin 1.6.0-RC3

27-October-2010 Matias
+ [#20084] Migrate existing data from old discussbots
+ [#20084] Add option to create topic on first reply
+ [#20084] Add option to restrict topic creation only to new articles (by publish date)
+ [#20084] Add option to prevent answers on old topics (by creation / last post)
+ [#20084] Add CAPTCHA support
* [#20084] Add Token protection
+ [#20084] Add check for banned users
+ [#20084] Add check for flood
+ [#20084] Send subscriptions
+ [#20084] Add possibility to change contents of the first message

22-October-2010 Xillibit
# [#20084] Set article creation date for first message of article discussion in kunena
+ [#20084] New configuration settings to define when the topic discussion will be created and how to diplay the first message content

22-October-2010 Xillibit
+ [#20084] Added el-GR translation (thanks mijalis)

18-October-2010 fxstein
^ [#20084] Minimum Kunena verison number increased to 1.6.0
# [#20084] Fixed install file for new languages

10-October-2010 Matias
+ [#20084] Added ru-RU (thanks ZARKOS) and hu-HU translation

17-September-2010 Xillibit
+ [#20084] Added fr-FR translation

Kunena Discuss Plugin 1.6.0-RC2

01-Sep-2010 @quila
# [#20084] fixed page number from the article is creeping up

31-Aug-2010 fxstein
^ [#20084] Load kunena language through API
^ [#20084] Updated plugin description
^ [#20084] Updated language strings to 'Comments'

31-Aug-2010 @quila
# [#20084] Removed table in message.php
^ [#20084] Added more css style to show messages
+ [#20084] Added comment.gif

31-Aug-2010 Matias
+ [#20084] Post first message as article owner (or fixed user)
# [#20084] If category is not allowed, article can still use custom topic

30-Aug-2010 Matias
+ [#20084] Disable discussion by {kunena_discuss:0}
# [#20084] Fix cross reference logic
# [#20084] Much improved handling on merged topics and changed custom topics
# [#20084] Fix category mapping issues
# [#20084] Fix allow and deny list handling

29-Aug-2010 Xillibit
# [#20084] Put db->quote() on some values in queries to avoid issues

29-Aug-2010 Matias
# [#20084] Fix application scope detection

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