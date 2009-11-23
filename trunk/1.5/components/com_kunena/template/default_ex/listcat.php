<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$app =& JFactory::getApplication();
$fbConfig =& CKunenaConfig::getInstance();
$fbSession =& CKunenaSession::getInstance();
$kunena_my =& JFactory::getUser();
if (strtolower($func) == '' ){
include (KUNENA_ABSTMPLTPATH . '/latestx.php');
} else {

//securing passed form elements
$catid = (int)$catid;

$smileyList = smile::getEmoticons(0);

//resetting some things:
$moderatedForum = 0;
$lockedForum = 0;
// Start getting the categories
$kunena_db->setQuery("SELECT * FROM #__fb_categories WHERE parent='0' AND published='1' ORDER BY ordering");
$allCat = $kunena_db->loadObjectList();
	check_dberror("Unable to load categories.");

$threadids = array ();
$categories = array ();

//meta description and keywords
$metaDesc=(_KUNENA_CATEGORIES . ' - ' . stripslashes($fbConfig->board_title));
$metaKeys=(_KUNENA_CATEGORIES . ', ' . stripslashes($fbConfig->board_title) . ', ' . $app->getCfg('sitename'));

$document =& JFactory::getDocument();
$cur = $document->get( 'description' );
$metaDesc = $cur .'. ' . $metaDesc;
$document =& JFactory::getDocument();
$document->setMetadata( 'keywords', $metaKeys );
$document->setDescription($metaDesc);

if (count($allCat) > 0)
{
    foreach ($allCat as $category)
    {
        $threadids[] = $category->id;
        $categories[$category->parent][] = $category;
    }
}

//Let's check if the only thing we need to show is 1 category
if (in_array($catid, $threadids))
{
    //Yes, so now $threadids should contain only the current $catid:
    unset ($threadids);
    $threadids[] = $catid;
    //get new categories list for this category only:
    unset ($categories);
    $kunena_db->setQuery("SELECT * FROM #__fb_categories WHERE parent='0' and published='1' and id='{$catid}' ORDER BY ordering");
    $categories[$category->parent] = $kunena_db->loadObjectList();
    	check_dberror("Unable to load categories.");
}

//get the allowed forums and turn it into an array
$allow_forum = ($fbSession->allowed <> '')?explode(',', $fbSession->allowed):array();

// (JJ) BEGIN: ANNOUNCEMENT BOX
if ($fbConfig->showannouncement > 0)
{
?>
<!-- B: announcementBox -->
<?php
    if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcementbox.php')) {
        require_once (KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcementbox.php');
    }
    else {
        require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/announcement/announcementbox.php');
    }
?>
<!-- F: announcementBox -->
<?php
}
// (JJ) FINISH: ANNOUNCEMENT BOX

// load module

if (JDocumentHTML::countModules('kunena_announcement'))
{
?>

    <div class = "fb-fb_2">
        <?php
        	$document	= &JFactory::getDocument();
        	$renderer	= $document->loadRenderer('modules');
        	$options	= array('style' => 'xhtml');
        	$position	= 'kunena_announcement';
        	echo $renderer->render($position, $options, null);
        ?>
    </div>

<?php
}
?>
<!-- B: Pathway -->
<?php
if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_pathway.php')) {
    require_once (KUNENA_ABSTMPLTPATH . '/fb_pathway.php');
}
else {
    require_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_pathway.php');
}
?>
<!-- F: Pathway -->
<!-- B: Cat list Top -->
<table class="fb_list_top" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
<?php
if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_category_list_bottom.php')) {
	include (KUNENA_ABSTMPLTPATH . '/fb_category_list_bottom.php');
}
else {
	include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_category_list_bottom.php');
}
?>
</table>
<!-- F: Cat list Top -->


<?php
if (count($categories[0]) > 0)
{
    foreach ($categories[0] as $cat)
    {
        $obj_fb_cat = new jbCategory($kunena_db, $cat->id);

        $is_Mod = fb_has_moderator_permission($kunena_db, $obj_fb_cat, $kunena_my->id, $is_admin);

        if (in_array($cat->id, $allow_forum))
        {
?>
            <!-- B: List Cat -->
<div class="<?php echo $boardclass; ?>_bt_cvr1" id="fb_block<?php echo $cat->id ; ?>">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
            <table class = "fb_blocktable<?php echo $cat->class_sfx; ?>"  width="100%" id = "fb_cat<?php echo $cat->id ; ?>" border = "0" cellspacing = "0" cellpadding = "0">
                <thead>
                    <tr>
                        <th colspan = "5">
                            <div class = "fb_title_cover fbm" >
                                <?php
                                echo CKunenaLink::GetCategoryLink('listcat', $cat->id, kunena_htmlspecialchars(stripslashes($cat->name)), 'follow', $class='fb_title fbl');

                                if ($cat->description != "") {
                                    $tmpforumdesc = stripslashes(smile::smileReplace($cat->description, 0, $fbConfig->disemoticons, $smileyList));
							        $tmpforumdesc = nl2br($tmpforumdesc);
							        $tmpforumdesc = smile::htmlwrap($tmpforumdesc, $fbConfig->wrap);
									echo $tmpforumdesc;
                                }
                                ?>
                            </div>
                            <img id = "BoxSwitch_<?php echo $cat->id ; ?>__catid_<?php echo $cat->id ; ?>" class = "hideshow" src = "<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                        </th>
                    </tr>
                </thead>
                <tbody id = "catid_<?php echo $cat->id ; ?>">
                    <tr class = "fb_sth fbs ">
                        <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader" width="1%">&nbsp;</th>
                        <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader" align="left"><?php echo _GEN_FORUM; ?></th>
                        <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader" align="center" width="5%"><?php echo _GEN_TOPICS; ?></th>

                        <th class = "th-4 <?php echo $boardclass; ?>sectiontableheader" align="center" width="5%">
<?php echo _GEN_REPLIES; ?>
                        </th>

                        <th class = "th-5 <?php echo $boardclass; ?>sectiontableheader" align="left" width="25%">
<?php echo _GEN_LAST_POST; ?>
                        </th>
                    </tr>

                    <?php
                    //    show forums within the categories
                    $kunena_db->setQuery("SELECT c.*, m.id AS mesid, m.subject, mm.catid, m.name AS mname, u.id AS userid, u.username, u.name AS uname FROM #__fb_categories AS c
                    LEFT JOIN #__fb_messages AS m ON c.id_last_msg=m.id
                    LEFT JOIN #__users AS u ON u.id=m.userid
                    LEFT JOIN #__fb_messages AS mm ON mm.id=c.id_last_msg
                    WHERE c.parent='{$cat->id}' AND c.published='1' ORDER BY ordering");
                    $rows = $kunena_db->loadObjectList();
                    	check_dberror("Unable to load categories.");

                    $tabclass = array
                    (
                        "sectiontableentry1",
                        "sectiontableentry2"
                    );

                    $k = 0;

                    if (sizeof($rows) == 0) {
                        echo '' . _GEN_NOFORUMS . '';
                    }
                    else
                    {
                        foreach ($rows as $singlerow)
                        {

                            $obj_fb_cat = new jbCategory($kunena_db, $singlerow->id);
                            $is_Mod = fb_has_moderator_permission($kunena_db, $obj_fb_cat, $kunena_my->id, $is_admin);

                            if (in_array($singlerow->id, $allow_forum))
                            {
                                //    $k=for alternating row colors:
                                $k = 1 - $k;

                                $numtopics = $singlerow->numTopics;
                                $numreplies = $singlerow->numPosts;
                                $lastPosttime = $singlerow->time_last_msg;
                                $lastptime = KUNENA_timeformat(CKunenaTools::fbGetShowTime($singlerow->time_last_msg));

                                $forumDesc = stripslashes(smile::smileReplace($singlerow->description, 0, $fbConfig->disemoticons, $smileyList));
						        $forumDesc = nl2br($forumDesc);
						        $forumDesc = smile::htmlwrap($forumDesc, $fbConfig->wrap);

                                //    Get the forumsubparent categories :: get the subcategories here
                                $kunena_db->setQuery("SELECT id, name, numTopics, numPosts FROM #__fb_categories WHERE parent='{$singlerow->id}' AND published='1' ORDER BY ordering");
                                $forumparents = $kunena_db->loadObjectList();
                                	check_dberror("Unable to load categories.");

								foreach ($forumparents as $childnum=>$childforum)
								{
									if (!in_array($childforum->id, $allow_forum)) unset ($forumparents[$childnum]);
								}
								$forumparents = array_values($forumparents);

                                if ($kunena_my->id)
                                {
                                    //    get all threads with posts after the users last visit; don't bother for guests
                                    $kunena_db->setQuery("SELECT DISTINCT thread FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='0' AND moved='0' AND time>'{$prevCheck}' GROUP BY thread");
                                    $newThreadsAll = $kunena_db->loadObjectList();
                                    	check_dberror("Unable to load messages.");

                                    if (count($newThreadsAll) == 0) {
                                        $newThreadsAll = array ();
                                    }
                                }

                                // get pending messages if user is a Moderator for that forum
                                $kunena_db->setQuery("SELECT userid FROM #__fb_moderation WHERE catid='{$singlerow->id}'");
                                $moderatorList = $kunena_db->loadObjectList();
                                	check_dberror("Unable to load moderators.");
                                $modIDs[] = array ();

                                array_splice($modIDs, 0);

                                if (count($moderatorList) > 0)
                                {
                                    foreach ($moderatorList as $ml) {
                                        $modIDs[] = $ml->userid;
                                    }
                                }

                                $nummodIDs = count($modIDs);
                                $numPending = 0;

                                if ((in_array($kunena_my->id, $modIDs)) || $is_admin == 1)
                                {
                                    $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='1'");
                                    $numPending = $kunena_db->loadResult();
                                    $is_Mod = 1;
                                }

                                $numPending = (int)$numPending;
                                //    get latest post info
                                unset($thisThread);
                                $kunena_db->setQuery(
                                "SELECT m.thread, COUNT(*) AS totalmessages
                                FROM #__fb_messages AS m
                                LEFT JOIN #__fb_messages AS mm ON m.thread=mm.thread
                                WHERE m.id='{$singlerow->id_last_msg}'
                                GROUP BY m.thread");
                                $thisThread = $kunena_db->loadObject();
                                if (!is_object($thisThread))
                                {
                                	$thisThread = new stdClass();
                                	$thisThread->totalmessages = 0;
                                	$thisThread->thread = 0;
                                }
                                $latestthreadpages = ceil($thisThread->totalmessages / $fbConfig->messages_per_page);
                                $latestthread = $thisThread->thread;
                                $latestname = kunena_htmlspecialchars(stripslashes($singlerow->mname));
                                $latestcatid = $singlerow->catid;
                                $latestid = $singlerow->id_last_msg;
                                $latestsubject = kunena_htmlspecialchars(stripslashes($singlerow->subject));
                                $latestuserid = $singlerow->userid;
                    ?>

                                <tr class = "<?php echo ''.$boardclass.'' . $tabclass[$k] . ''; ?>" id="fb_cat<?php echo $singlerow->id ?>">
                                    <td class = "td-1" align="center">
                                        <?php
                                        $tmpIcon = '';
										$cxThereisNewInForum = 0;
										if ($fbConfig->shownew && $kunena_my->id != 0)
                                        {
                                            //Check if unread threads are in any of the forums topics
                                            $newPostsAvailable = 0;

                                            foreach ($newThreadsAll as $nta)
                                            {
                                                if (!in_array($nta->thread, $read_topics)) {
                                                    $newPostsAvailable++;
                                                }
                                            }

                                            if ($newPostsAvailable > 0 && count($newThreadsAll) != 0)
                                            {
                                                $cxThereisNewInForum = 1;

                                                // Check Unread    Cat Images
                                                if (is_file(KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_on.gif"))
                                                {
                                                    $tmpIcon = '<img src="'.KUNENA_URLCATIMAGES.$singlerow->id.'_on.gif" border="0" class="forum-cat-image"alt=" " />';
                                                }
                                                else
                                                {
                                                    $tmpIcon = isset($fbIcons['unreadforum']) ? '<img src="'.KUNENA_URLICONSPATH.$fbIcons['unreadforum'].'" border="0" alt="'._GEN_FORUM_NEWPOST.'" title="'._GEN_FORUM_NEWPOST.'" />' : stripslashes($fbConfig->newchar);
                                                }
                                            }
                                            else
                                            {
                                                // Check Read Cat Images
                                                if (is_file(KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_off.gif"))
                                                {
                                                    $tmpIcon = '<img src="'.KUNENA_URLCATIMAGES.$singlerow->id.'_off.gif" border="0" class="forum-cat-image" alt=" " />';
                                                }
                                                else
                                                {
                                                    $tmpIcon = isset($fbIcons['readforum']) ? '<img src="'.KUNENA_URLICONSPATH.$fbIcons['readforum'].'" border="0" alt="'._GEN_FORUM_NOTNEW.'" title="'._GEN_FORUM_NOTNEW.'" />' : stripslashes($fbConfig->newchar);
                                                }
                                            }
                                        }
                                        // Not Login Cat Images
                                        else
                                        {
                                            if (is_file(KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_notlogin.gif")) {
                                                $tmpIcon = '<img src="'.KUNENA_URLCATIMAGES.$singlerow->id.'_notlogin.gif" border="0" class="forum-cat-image" alt=" " />';
                                            }
                                            else {
                                                $tmpIcon = isset($fbIcons['notloginforum']) ? '<img src="'.KUNENA_URLICONSPATH.$fbIcons['notloginforum'].'" border="0" alt="'._GEN_FORUM_NOTNEW.'" title="'._GEN_FORUM_NOTNEW.'" />' : stripslashes($fbConfig->newchar);
                                            }
                                        }
                                        echo CKunenaLink::GetCategoryLink('showcat', $singlerow->id, $tmpIcon);
                                        ?>
                                    </td>

                                    <td class = "td-2" align="left">
                                        <div class = "<?php echo $boardclass ?>thead-title fbl">
                                            <?php //new posts available
                                            echo CKunenaLink::GetCategoryLink('showcat', $singlerow->id, kunena_htmlspecialchars(stripslashes($singlerow->name)));

                                            if ($cxThereisNewInForum == 1 && $kunena_my->id > 0) {
                                                echo '<sup><span class="newchar">&nbsp;(' . $newPostsAvailable . ' ' . stripslashes($fbConfig->newchar) . ")</span></sup>";
                                            }

                                            $cxThereisNewInForum = 0;
                                            ?>

                                            <?php
                                            if ($singlerow->locked)
                                            {
                                                echo isset($fbIcons['forumlocked']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $fbIcons['forumlocked']
                                                         . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0" alt="' . _GEN_LOCKED_FORUM . '">';
                                                $lockedForum = 1;
                                            }

                                            if ($singlerow->review)
                                            {
                                                echo isset($fbIcons['forummoderated']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $fbIcons['forummoderated']
                                                         . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '">';
                                                $moderatedForum = 1;
                                            }
                                            ?>
                                        </div>

                                        <?php
                                        if ($forumDesc != "")
                                        {
                                        ?>

                                            <div class = "<?php echo $boardclass ?>thead-desc fbm">
<?php echo $forumDesc ?>
                                            </div>

                                        <?php
                                        }

                                        // loop over subcategories to show them under
                                        if (count($forumparents) > 0)
                                        {
											if ($fbConfig->numchildcolumn > 0) {
												$subtopicwidth = ' style="width: 99%;"';
												$subwidth = ' style="width: ' . floor(99 / $fbConfig->numchildcolumn) . '%"';
											}
											else {
												$subtopicwidth = ' style="display: inline-block;"';
												$subwidth = '';
											}

                                        	?>

                                            <div class = "<?php echo $boardclass?>thead-child">

                                                <div class = "<?php echo $boardclass?>cc-table">
	                                                <div<?php echo $subtopicwidth?> class = "<?php echo $boardclass?>cc-childcat-title">
    	                                                <?php if(count($forumparents)==1) { echo _KUNENA_CHILD_BOARD; } else { echo _KUNENA_CHILD_BOARDS; } ?>:
        	                                        </div>
                                                    <?php

                                                    for ($row_count = 0; $row_count < count($forumparents); $row_count++)
                                                    {														   
														  echo "<div{$subwidth} class=\"{$boardclass}cc-subcat fbm\">";

                                                            $forumparent = $forumparents[$row_count];

                                                            if ($forumparent)
                                                            {

                                                                //Begin: parent read unread iconset
                                                                if ($fbConfig->showchildcaticon)
                                                                {
                                                                    //
                                                                    if ($fbConfig->shownew && $kunena_my->id != 0)
                                                                    {
                                                                        //    get all threads with posts after the users last visit; don't bother for guests
                                                                        $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE catid='{$forumparent->id}' AND hold='0' AND time>'{$prevCheck}' GROUP BY thread");
                                                                        $newPThreadsAll = $kunena_db->loadObjectList();
                                                                        	check_dberror("Unable to load messages.");

                                                                        if (count($newPThreadsAll) == 0) {
                                                                            $newPThreadsAll = array ();
                                                                        }
                                                    ?>

                                                    <?php
                                                                        //Check if unread threads are in any of the forums topics
                                                                        $newPPostsAvailable = 0;

                                                                        foreach ($newPThreadsAll as $npta)
                                                                        {
                                                                            if (!in_array($npta->thread, $read_topics)) {
                                                                                $newPPostsAvailable++;
                                                                            }
                                                                        }

                                                                        if ($newPPostsAvailable > 0 && count($newPThreadsAll) != 0)
                                                                        {
                                                                            // Check Unread    Cat Images
                                                                            if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_on_childsmall.gif")) {
                                                                                echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_on_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                                                            }
                                                                            else {
                                                                                echo isset($fbIcons['unreadforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['unreadforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '" />' : stripslashes($fbConfig->newchar);
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            // Check Read Cat Images
                                                                            if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_off_childsmall.gif")) {
                                                                                echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_off_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                                                            }
                                                                            else {
                                                                                echo isset($fbIcons['readforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['readforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes($fbConfig->newchar);
                                                                            }
                                                                        }
                                                                    }
                                                                    // Not Login Cat Images
                                                                    else
                                                                    {
                                                                        if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_notlogin_childsmall.gif")) {
                                                                            echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_notlogin_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                                                        }
                                                                        else {
                                                                            echo isset($fbIcons['notloginforum']) ? '<img src="' . KUNENA_URLICONSPATH . $fbIcons['notloginforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes($fbConfig->newchar);
                                                                        }
                                                    ?>

                                                    <?php
                                                                    }
                                                                //
                                                                }
                                                                // end: parent read unread iconset
                                                    ?>

                                                    <?php
                                                                echo CKunenaLink::GetCategoryLink('showcat', $forumparent->id, kunena_htmlspecialchars(stripslashes($forumparent->name)));
                                                                echo '<span class="fb_childcount fbs">('.$forumparent->numTopics."/".$forumparent->numPosts.')</span>';
                                                            }
                                                            echo "</div>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                        <?php
                                        }

                                        //get the Moderator list for display
                                        $kunena_db->setQuery("SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='{$singlerow->id}'");
                                        $modslist = $kunena_db->loadObjectList();
                                        	check_dberror("Unable to load moderators.");

                                        // moderator list
                                        if (count($modslist) > 0)
                                        {
                                        ?>

                                            <div class = "<?php echo $boardclass ;?>thead-moderators fbs">
<?php echo _GEN_MODERATORS; ?>:

                                                <?php
                                                $mod_cnt = 0;
                                                foreach ($modslist as $mod) {
					                               	if ($mod_cnt) echo ', '; 
					                               	$mod_cnt++;
													echo CKunenaLink::GetProfileLink($fbConfig, $mod->userid, ($fbConfig->username ? $mod->username : $mod->name));
                                                }
                                                ?>
                                            </div>

                                        <?php
                                        }

                                        if ($is_Mod)
                                        {
                                            if ($numPending > 0)
                                            {
                                                echo '<div class="fbs"><font color="red"> ';
                                                echo CKunenaLink::GetPendingMessagesLink($singlerow->id, $numPending.' '._SHOWCAT_PENDING);
                                                echo '</font></div>';
                                            }
                                        }
                                        ?>
                                    </td>

                                    <td class = "td-3  fbm" align="center" ><?php echo $numtopics; ?></td>

                                    <td class = "td-4  fbm" align="center" >
<?php                                   echo $numreplies; ?>
                                    </td>

                                    <?php
                                    if ($numtopics != 0)
                                    {
                                    ?>

                                        <td class = "td-5" align="left">
                                            <div class = "<?php echo $boardclass ?>latest-subject fbm">
<?php
                                                echo CKunenaLink::GetThreadPageLink($fbConfig, 'view', $singlerow->catid, $latestthread, $latestthreadpages, $fbConfig->messages_per_page, $latestsubject, $latestid);
?>
                                            </div>

                                            <div class = "<?php echo $boardclass ?>latest-subject-by fbs">
<?php
                                                echo _GEN_BY.' ';
                                                echo CKunenaLink::GetProfileLink($fbConfig, $latestuserid, $latestname);
                                                echo ' | '.$lastptime.' ';
                                                echo CKunenaLink::GetThreadPageLink($fbConfig, 'view', $singlerow->catid, $latestthread, $latestthreadpages, $fbConfig->messages_per_page,
                                                isset($fbIcons['latestpost']) ? '<img src="'.KUNENA_URLICONSPATH.$fbIcons['latestpost'].'" border="0" alt="'._SHOW_LAST.'" title="'. _SHOW_LAST.'"/>' :
                                                                         '<img src="'.KUNENA_URLEMOTIONSPATH.'icon_newest_reply.gif" border="0"  alt="'._SHOW_LAST.'"/>', $latestid);
?>
                                            </div>
                                        </td>
                                </tr>

                                    <?php
                                    }
                                    else
                                    {
                                    ?>

                                        <td class = "td-5"  align="left">
<?php echo _NO_POSTS; ?>
                                        </td>

                                        </tr>

                    <?php
                                    }
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>


 </div>
 </div>
 </div>
 </div>
 </div>
<!-- F: List Cat -->

<?php
        }
    }

	//(JJ) BEGIN: CAT LIST BOTTOM
	echo '<!-- B: Cat list Bottom -->';
	echo '<table class="fb_list_bottom" border = "0" cellspacing = "0" cellpadding = "0" width="100%">';
	if (file_exists(KUNENA_ABSTMPLTPATH . '/fb_category_list_bottom.php')) {
		include (KUNENA_ABSTMPLTPATH . '/fb_category_list_bottom.php');
	}
	else {
		include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'fb_category_list_bottom.php');
	}
	echo '</table>';
	echo '<!-- F: Cat list Bottom -->';
	//(JJ) FINISH: CAT LIST BOTTOM

	//(JJ) BEGIN: WHOISONLINE
	if ($fbConfig->showwhoisonline > 0)
	{
		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php')) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
		}
		else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/who/whoisonline.php');
		}
	}
	//(JJ) FINISH: WHOISONLINE

	//(JJ) BEGIN: STATS
	if ($fbConfig->showstats > 0)
	{
		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php')) {
			include_once (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
		}
		else {
			include_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/stats.class.php');
		}

		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/frontstats.php')) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/stats/frontstats.php');
		}
		else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/frontstats.php');
		}
	}
	//(JJ) FINISH: STATS
?>

<?php
}
else
{
?>

    <div>
        <?php
        echo _LISTCAT_NO_CATS . '<br />';
        echo _LISTCAT_ADMIN . '<br />';
        echo _LISTCAT_PANEL . '<br /><br />';
        echo _LISTCAT_INFORM . '<br /><br />';
        echo _LISTCAT_DO . ' <img src="' . KUNENA_URLEMOTIONSPATH . 'wink.png"  alt="" border="0" />';
        ?>
    </div>

<?php
}

} // <<< -- first latest if close //
?>
