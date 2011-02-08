<?php
/**
 * @version $Id: CHANGELOG.php 4325 2011-01-30 09:26:53Z mahagr $
 * Kunena Discuss Plugin
 * @package Kunena Discuss
 *
 * @Copyright (C) 2010 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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

Kunena Discuss Plugin 2.0.0-DEV

8-February-2011 Matias
^ [#20084] Convert code to use K2.0.0 libraries

Kunena Discuss Plugin 1.6.2

23-January-2010 LDA(svens)
^ [#24560] update de-DE (thanks rich)
^ [#24560] update ru-RU (thanks Zarkos)

25-December-2010 fxstein
^ [#23293] Replace hard coded version info in language files with auto expansion
+ [#23293] Add version info auto expansion to builder
^ [#23293] Update min Kunena version requirements to Kunena 1.6.2

24-December-2010 fxstein
^ [#20084] Merged revisions 3803-4058 from /branches/1.6-addons-LDAsvens-language-20101030

22-December-2010 Severdia
# [#20084] More English language file cleanup and CSS issues.

22-December-2010 svens(LDA)
^ [#23293] update ru-RU (thanks Zarkos)

14-December-2010 Severdia
^ [#20084] Cleaned up English language file. Now it's real English. :)

14-December-2010 fxstein
# [#20084] Fix missing language strings due to loading them too early, before parent constructor gets called

14-December-2010 Xillibit
^ [#20084] Update french language file

13-December-2010 fxstein
+ [#20084] Proper error message if Kunena version is too old
^ [#20084] Update version info to 1.6.2
+ [#20084] Check if onPrepareContent event has been originated from within Kunena to avoid event recursion
^ [#20084] Updated table creation and migration code
^ [#20084] Rename legacy bot variables and comments to plugin
^ [#20084] de-DE (German) language updated
^ [#20084] Move up loading on language files to enable error messages

11-December-2010 svens(LDA)
^ [#23293] update de-DE (thanks rich)

19-November-2010 svens(LDA)
+ [#23293] add es-ES (thanks Alakakentu)

12-November-2010 svens(LDA)
+ [#23293] add de-DE (thanks rich)

6-November-2010 fxstein
^ [#20084] Change all references of kunena.com to kunena.org
^ [#20084] Update version info to 1.6.1

04-November-2010 svens(LDA)
^ [#22975] update ru-RU (thanks ZARKOS)

3-November-2010 Severdia
# [#20084] Fixed float bug in IE

1-November-2010 Matias
# [#20084] Do not show unapproved/deleted messages
# [#20084] In descending ordering show also newest message in the topic, ignore always first post
# [#20084] Fix white page when article gets rendered inside event

31-October-2010 fxstein
# [#20084] Fix message ordering (reverse)
+ [#20081] Display subject as part of replies
+ [#20081] Add border around discussions to separate from article
^ [#20081] Change language string when quick reply is disabled
^ [#20081] Reflect 1.6.1 min requirement in plugin description
^ [#20081] Change individual message link logic to support reverse sort order

31-October-2010 Matias
# [#20084] Fix message ordering to obey configuration

30-October-2010 svens LDA
^ [#22975] update ru-RU (thanks ZARKOS)

30-October-2010 fxstein
+ [#20084] Added missing language string in english
+ [#20081] Links to individual messages
+ [#20081] Topic and Read More links added
^ [#20081] Language string updated to reflect 1.6.0-RC3
# [#20081] Undefined variables warnings fixed
^ [#20081] Language string changed: Forever to Unlimited

30-October-2010 Xillibit
^ [#20084] When the kunena reference isn't yet created and the quick form is disabled the user will informed that there is no reference
# [#20084] Fix undefined variable on link_topic
# [#20084] Put is_object to check the things are right an object and not others things
# [#20084] Fix Fatal error: Class 'CKunenaLink' not found in /lib/kunena.posting.class.php on line 912
^ [#20084] Some languages updated because a new strings has been added

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