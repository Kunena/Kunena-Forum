<?php
/**
* @version $Id: listcat.php 1210 2009-11-23 06:51:41Z mahagr $
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

global $kunena_is_admin;
global $kunena_emoticons;

$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();
$kunena_config =& CKunenaConfig::getInstance();
$kunena_session =& CKunenaSession::getInstance();
$kunena_my =& JFactory::getUser();

$func = JString::strtolower(JRequest::getCmd('func', ''));
if (JString::strtolower($func) == '' ){
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
$metaDesc=(_KUNENA_CATEGORIES . ' - ' . stripslashes($kunena_config->board_title));
$metaKeys=(_KUNENA_CATEGORIES . ', ' . stripslashes($kunena_config->board_title) . ', ' . $kunena_app->getCfg('sitename'));

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
    $threadids[] = $catid;
    //get new categories list for this category only:
    $kunena_db->setQuery("SELECT * FROM #__fb_categories WHERE parent='0' and published='1' and id='{$catid}' ORDER BY ordering");
    $categories[$category->parent] = $kunena_db->loadObjectList();
    	check_dberror("Unable to load categories.");
}

//get the allowed forums and turn it into an array
$allow_forum = ($kunena_session->allowed <> '')?explode(',', $kunena_session->allowed):array();
$kunena_is_admin = CKunenaTools::isAdmin();

// (JJ) BEGIN: ANNOUNCEMENT BOX
if ($kunena_config->showannouncement > 0)
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
        if (in_array($cat->id, $allow_forum))
        {
?>
            <!-- B: List Cat -->
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr1" id="fb_block<?php echo $cat->id ; ?>">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr2">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr3">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr4">
<div class="<?php echo KUNENA_BOARD_CLASS; ?>_bt_cvr5">
            <table class = "fb_blocktable<?php echo $cat->class_sfx; ?>"  width="100%" id = "fb_cat<?php echo $cat->id ; ?>" border = "0" cellspacing = "0" cellpadding = "0">
                <thead>
                    <tr>
                        <th colspan = "5">
                            <div class = "fb_title_cover fbm" >
                                <?php
                                echo CKunenaLink::GetCategoryLink('listcat', $cat->id, kunena_htmlspecialchars(stripslashes($cat->name)), 'follow', $class='fb_title fbl');

                                if ($cat->description != "") {
                                    $tmpforumdesc = stripslashes(smile::smileReplace($cat->description, 0, $kunena_config->disemoticons, $smileyList));
							        $tmpforumdesc = nl2br($tmpforumdesc);
							        $tmpforumdesc = smile::htmlwrap($tmpforumdesc, $kunena_config->wrap);
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
                        <th class = "th-1 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" width="1%">&nbsp;</th>
                        <th class = "th-2 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="left"><?php echo _GEN_FORUM; ?></th>
                        <th class = "th-3 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center" width="5%"><?php echo _GEN_TOPICS; ?></th>

                        <th class = "th-4 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="center" width="5%">
<?php echo _GEN_REPLIES; ?>
                        </th>

                        <th class = "th-5 <?php echo KUNENA_BOARD_CLASS; ?>sectiontableheader" align="left" width="25%">
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
                            if (in_array($singlerow->id, $allow_forum))
                            {
                                //    $k=for alternating row colors:
                                $k = 1 - $k;

                                $numtopics = $singlerow->numTopics;
                                $numreplies = $singlerow->numPosts;
                                $lastPosttime = $singlerow->time_last_msg;
                                $lastptime = KUNENA_timeformat(CKunenaTools::fbGetShowTime($singlerow->time_last_msg));

                                $forumDesc = stripslashes(smile::smileReplace($singlerow->description, 0, $kunena_config->disemoticons, $smileyList));
						        $forumDesc = nl2br($forumDesc);
						        $forumDesc = smile::htmlwrap($forumDesc, $kunena_config->wrap);

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
                                    $kunena_db->setQuery("SELECT DISTINCT thread FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='0' AND moved='0' AND time>'{$this->prevCheck}' GROUP BY thread");
                                    $newThreadsAll = $kunena_db->loadObjectList();
                                    	check_dberror("Unable to load messages.");

                                    if (count($newThreadsAll) == 0) {
                                        $newThreadsAll = array ();
                                    }
                                }

                                //    get latest post info
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
                                $latestthreadpages = ceil($thisThread->totalmessages / $kunena_config->messages_per_page);
                                $latestthread = $thisThread->thread;
                                $latestname = kunena_htmlspecialchars(stripslashes($singlerow->mname));
                                $latestcatid = $singlerow->catid;
                                $latestid = $singlerow->id_last_msg;
                                $latestsubject = kunena_htmlspecialchars(stripslashes($singlerow->subject));
                                $latestuserid = $singlerow->userid;
                    ?>

                                <tr class = "<?php echo ''.KUNENA_BOARD_CLASS.'' . $tabclass[$k] . ''; ?>" id="fb_cat<?php echo $singlerow->id ?>">
                                    <td class = "td-1" align="center">
                                        <?php
                                        $tmpIcon = '';
										$cxThereisNewInForum = 0;
										if ($kunena_config->shownew && $kunena_my->id != 0)
                                        {
                                            //Check if unread threads are in any of the forums topics
                                            $newPostsAvailable = 0;

                                            foreach ($newThreadsAll as $nta)
                                            {
                                                if (!in_array($nta->thread, $this->read_topics)) {
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
                                                    $tmpIcon = isset($kunena_emoticons['unreadforum']) ? '<img src="'.KUNENA_URLICONSPATH.$kunena_emoticons['unreadforum'].'" border="0" alt="'._GEN_FORUM_NEWPOST.'" title="'._GEN_FORUM_NEWPOST.'" />' : stripslashes($kunena_config->newchar);
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
                                                    $tmpIcon = isset($kunena_emoticons['readforum']) ? '<img src="'.KUNENA_URLICONSPATH.$kunena_emoticons['readforum'].'" border="0" alt="'._GEN_FORUM_NOTNEW.'" title="'._GEN_FORUM_NOTNEW.'" />' : stripslashes($kunena_config->newchar);
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
                                                $tmpIcon = isset($kunena_emoticons['notloginforum']) ? '<img src="'.KUNENA_URLICONSPATH.$kunena_emoticons['notloginforum'].'" border="0" alt="'._GEN_FORUM_NOTNEW.'" title="'._GEN_FORUM_NOTNEW.'" />' : stripslashes($kunena_config->newchar);
                                            }
                                        }
                                        echo CKunenaLink::GetCategoryLink('showcat', $singlerow->id, $tmpIcon);
                                        ?>
                                    </td>

                                    <td class = "td-2" align="left">
                                        <div class = "<?php echo KUNENA_BOARD_CLASS ?>thead-title fbl">
                                            <?php //new posts available
                                            echo CKunenaLink::GetCategoryLink('showcat', $singlerow->id, kunena_htmlspecialchars(stripslashes($singlerow->name)));

                                            if ($cxThereisNewInForum == 1 && $kunena_my->id > 0) {
                                                echo '<sup><span class="newchar">&nbsp;(' . $newPostsAvailable . ' ' . stripslashes($kunena_config->newchar) . ")</span></sup>";
                                            }

                                            $cxThereisNewInForum = 0;
                                            ?>

                                            <?php
                                            if ($singlerow->locked)
                                            {
                                                echo isset($kunena_emoticons['forumlocked']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['forumlocked']
                                                         . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0" alt="' . _GEN_LOCKED_FORUM . '">';
                                                $lockedForum = 1;
                                            }

                                            if ($singlerow->review)
                                            {
                                                echo isset($kunena_emoticons['forummoderated']) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['forummoderated']
                                                         . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '">';
                                                $moderatedForum = 1;
                                            }
                                            ?>
                                        </div>

                                        <?php
                                        if ($forumDesc != "")
                                        {
                                        ?>

                                            <div class = "<?php echo KUNENA_BOARD_CLASS ?>thead-desc fbm">
<?php echo $forumDesc ?>
                                            </div>

                                        <?php
                                        }

                                        // loop over subcategories to show them under
                                        if (count($forumparents) > 0)
                                        {
											if ($kunena_config->numchildcolumn > 0) {
												$subtopicwidth = ' style="width: 99%;"';
												$subwidth = ' style="width: ' . floor(99 / $kunena_config->numchildcolumn) . '%"';
											}
											else {
												$subtopicwidth = ' style="display: inline-block;"';
												$subwidth = '';
											}

                                        	?>

                                            <div class = "<?php echo KUNENA_BOARD_CLASS?>thead-child">

                                                <div class = "<?php echo KUNENA_BOARD_CLASS?>cc-table">
	                                                <div<?php echo $subtopicwidth?> class = "<?php echo KUNENA_BOARD_CLASS?>cc-childcat-title">
    	                                                <?php if(count($forumparents)==1) { echo _KUNENA_CHILD_BOARD; } else { echo _KUNENA_CHILD_BOARDS; } ?>:
        	                                        </div>
                                                    <?php

                                                    for ($row_count = 0; $row_count < count($forumparents); $row_count++)
                                                    {
														  echo "<div{$subwidth} class=\"{KUNENA_BOARD_CLASS}cc-subcat fbm\">";

                                                            $forumparent = $forumparents[$row_count];

                                                            if ($forumparent)
                                                            {

                                                                //Begin: parent read unread iconset
                                                                if ($kunena_config->showchildcaticon)
                                                                {
                                                                    //
                                                                    if ($kunena_config->shownew && $kunena_my->id != 0)
                                                                    {
                                                                        //    get all threads with posts after the users last visit; don't bother for guests
                                                                        $kunena_db->setQuery("SELECT thread FROM #__fb_messages WHERE catid='{$forumparent->id}' AND hold='0' AND time>'{$this->prevCheck}' GROUP BY thread");
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
                                                                            if (!in_array($npta->thread, $this->read_topics)) {
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
                                                                                echo isset($kunena_emoticons['unreadforum']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['unreadforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '" />' : stripslashes($kunena_config->newchar);
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            // Check Read Cat Images
                                                                            if (is_file(KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_off_childsmall.gif")) {
                                                                                echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_off_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
                                                                            }
                                                                            else {
                                                                                echo isset($kunena_emoticons['readforum']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['readforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes($kunena_config->newchar);
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
                                                                            echo isset($kunena_emoticons['notloginforum']) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons['notloginforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes($kunena_config->newchar);
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

                                            <div class = "<?php echo KUNENA_BOARD_CLASS ;?>thead-moderators fbs">
<?php echo _GEN_MODERATORS; ?>:

                                                <?php
                                                $mod_cnt = 0;
                                                foreach ($modslist as $mod) {
					                               	if ($mod_cnt) echo ', ';
					                               	$mod_cnt++;
													echo CKunenaLink::GetProfileLink($kunena_config, $mod->userid, ($kunena_config->username ? $mod->username : $mod->name));
                                                }
                                                ?>
                                            </div>

                                        <?php
                                        }

                                        if (CKunenaTools::isModerator($kunena_my->id, $singlerow->id))
                                        {
		                                    $kunena_db->setQuery("SELECT COUNT(*) FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='1'");
        		                            $numPending = $kunena_db->loadResult();
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
                                            <div class = "<?php echo KUNENA_BOARD_CLASS ?>latest-subject fbm">
<?php
                                                echo CKunenaLink::GetThreadPageLink($kunena_config, 'view', $singlerow->catid, $latestthread, $latestthreadpages, $kunena_config->messages_per_page, $latestsubject, $latestid);
?>
                                            </div>

                                            <div class = "<?php echo KUNENA_BOARD_CLASS ?>latest-subject-by fbs">
<?php
                                                echo _GEN_BY.' ';
                                                echo CKunenaLink::GetProfileLink($kunena_config, $latestuserid, $latestname);
                                                echo ' | '.$lastptime.' ';
                                                echo CKunenaLink::GetThreadPageLink($kunena_config, 'view', $singlerow->catid, $latestthread, $latestthreadpages, $kunena_config->messages_per_page,
                                                isset($kunena_emoticons['latestpost']) ? '<img src="'.KUNENA_URLICONSPATH.$kunena_emoticons['latestpost'].'" border="0" alt="'._SHOW_LAST.'" title="'. _SHOW_LAST.'"/>' :
                                                                         '<img src="'.KUNENA_URLEMOTIONSPATH.'icon_newest_reply.gif" border="0"  alt="'._SHOW_LAST.'"/>', $latestid);
?>
                                            </div>
                                        </td>

                                    <?php
                                    }
                                    else
                                    {
                                    ?>

                                        <td class = "td-5"  align="left">
<?php echo _NO_POSTS; ?>
                                        </td>

                    <?php
                                    }
                                    ?></tr><?php
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
	if ($kunena_config->showwhoisonline > 0)
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
	if ($kunena_config->showstats > 0)
	{
		if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php')) {
			include_once (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
		}
		else {
			include_once (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/stats/stats.class.php');
		}

		$kunena_stats = new CKunenaStats();
		$kunena_stats->showFrontStats();
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
