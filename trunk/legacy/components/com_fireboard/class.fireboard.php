<?php
/**
* @version $Id: class.fireboard.php 1079 2008-10-27 05:50:14Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*/

// Dont allow direct linking
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
*@desc Getting the correct Itemids, for components required
*/

//Fireboard
$Itemid = intval(mosGetParam($_REQUEST, 'Itemid'));

//check if we have all the itemid sets. if so, then no need for DB call

global $fbConfig;
if (!defined("FB_FB_ITEMID")) {
        $database->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=com_fireboard' AND published = 1");
        $Itemid = $database->loadResult();

        if ($Itemid < 1) {
            $Itemid = 0;
            }

    define("FB_FB_ITEMID", (int)$Itemid);
    define("FB_FB_ITEMID_SUFFIX", "&amp;Itemid=" . FB_FB_ITEMID);

    //Community Builder
    if ($fbConfig->cb_profile || $fbConfig->fb_profile == "cb") {
        $database->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=com_comprofiler' AND published=1");
        $CB_Itemid = $database->loadResult();

        define("FB_CB_ITEMID", (int)$CB_Itemid);
        define("FB_CB_ITEMID_SUFFIX", "&amp;Itemid=" . FB_CB_ITEMID);
        }

    //Clexus PM
    if ($fbConfig->pm_component == 'clexuspm' || $fbConfig->fb_profile == "clexuspm") {
        $database->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=com_mypms' AND published=1");
        $CPM_Itemid = $database->loadResult();

        define("FB_CPM_ITEMID", (int)$CPM_Itemid);
        define("FB_CPM_ITEMID_SUFFIX", "&amp;Itemid=" . FB_CPM_ITEMID);
        }

    // UddeIM
    if ($fbConfig->pm_component == 'uddeim') {
        $database->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_uddeim'");
        $UIM_itemid = $database->loadResult();
        define("FB_UIM_ITEMID", (int)$UIM_itemid);
        define("FB_UIM_ITEMID_SUFFIX", "&amp;Itemid=" . FB_UIM_ITEMID);
        }

    // MISSUS
    if ($fbConfig->pm_component == 'missus') {
        $database->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_missus'");
        $MISSUS_itemid = $database->loadResult();
        define("FB_MISSUS_ITEMID", (int)$MISSUS_itemid);
        define("FB_MISSUS_ITEMID_SUFFIX", "&amp;Itemid=" . FB_MISSUS_ITEMID);
        }

    // PROFILE LINK
    if ($fbConfig->fb_profile == "cb") {
        $profilelink = 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user=';
        define("FB_PROFILE_LINK_SUFFIX", "index.php?option=com_comprofiler&amp;task=userProfile&amp;Itemid=" . FB_CB_ITEMID . "&amp;user=");
        }
    else if ($fbConfig->fb_profile == "clexuspm") {
        $profilelink = 'index.php?option=com_mypms&amp;task=showprofile&amp;user=';
        define("FB_PROFILE_LINK_SUFFIX", "index.php?option=com_mypms&amp;task=showprofile&amp;Itemid=" . FB_CPM_ITEMID . "&amp;user=");
        }
    else {
        $profilelink = 'index.php?option=com_fireboard&amp;func=fbprofile&amp;task=showprf&amp;userid=';
        define("FB_PROFILE_LINK_SUFFIX", "index.php?option=com_fireboard&amp;func=fbprofile&amp;task=showprf&amp;Itemid=" . FB_FB_ITEMID . "&amp;userid=");
        }
    }

/*       _\|/_
         (o o)
 +----oOO-{_}-OOo--------------------------------+
 | Now we have the components Itemids everywhere |
 | Please use these constants where ever needed  |
 +----------------------------------------------*/

// Shortcuts to all the path we have:

define('JB_JABSPATH', $mainframe->getCfg('absolute_path'));

// Joomla absolute path
define('JB_JLIVEURL', $mainframe->getCfg('live_site'));

// fireboard live url
define('JB_LIVEURL', JB_JLIVEURL . '/index.php?option=com_fireboard' . FB_FB_ITEMID_SUFFIX);
define('JB_CLEANLIVEURL', JB_JLIVEURL . '/index2.php?option=com_fireboard&amp;no_html=1' . FB_FB_ITEMID_SUFFIX);
define('JB_LIVEURLREL', 'index.php?option=com_fireboard' . FB_FB_ITEMID_SUFFIX);
define('JB_ABSPATH', JB_JABSPATH . '/components/com_fireboard');

// fireboard absolute path
define('JB_ABSSOURCESPATH', JB_ABSPATH . '/sources/');

// fireboard souces absolute path
define('JB_DIRECTURL', JB_JLIVEURL . '/components/com_fireboard');

// fireboard direct url
define('JB_URLSOURCESPATH', JB_DIRECTURL . '/sources/');

// fireboard sources url
define('JB_LANG', $mainframe->getCfg('lang'));
define('JB_ABSADMPATH', JB_JABSPATH . '/administrator/components/com_fireboard');

if (!defined("JB_JCSSURL")) {
    $database->setQuery("SELECT template FROM #__templates_menu where client_id ='0'");
    $current_stylesheet = $database->loadResult();
    define('JB_JCSSURL', JB_JLIVEURL . '/templates/' . $current_stylesheet . '/css/template_css.css');
    }

// fireboard uploaded files directory
define('FB_ABSUPLOADEDPATH', JB_JABSPATH . '/images/fbfiles');
define('FB_LIVEUPLOADEDPATH', JB_JLIVEURL . '/images/fbfiles');


// now continue with other paths

$fb_user_template = strval(mosGetParam($_COOKIE, 'fb_user_template', ''));
$fb_user_img_template = strval(mosGetParam($_COOKIE, 'fb_user_img_template', ''));

if (strlen($fb_user_template) > 0) {
    $fb_cur_template = $fb_user_template;
    }
else {
    $fb_cur_template = $fbConfig->template;
    }

if (strlen($fb_user_img_template) > 0) {
    $fb_cur_img_template = $fb_user_img_template;
    }
else {
    $fb_cur_img_template = $fbConfig->templateimagepath;
    }

// only for preview module - maybe used later by users to change template

define('JB_ABSTMPLTPATH', JB_ABSPATH . '/template/' . $fb_cur_template);
define('JB_ABSTMPLTMAINIMGPATH', JB_ABSPATH . '/template/' . $fbConfig->templateimagepath);

// IMAGES ABSOLUTE PATH
define('JB_ABSIMAGESPATH', JB_ABSTMPLTMAINIMGPATH . '/images/' . JB_LANG . '/');

// absolute images path
define('JB_ABSICONSPATH', JB_ABSIMAGESPATH . 'icons/');

// absolute icons path
define('JB_ABSEMOTIONSPATH', JB_ABSIMAGESPATH . 'emoticons/');

// absolute emoticons path
define('JB_ABSGRAPHPATH', JB_ABSIMAGESPATH . 'graph/');

// absolute graph path
define('JB_ABSRANKSPATH', JB_ABSIMAGESPATH . 'ranks/');

// absolute ranks path
define('JB_ABSCATIMAGESPATH', FB_ABSUPLOADEDPATH . '/' . $fbConfig->catimagepath); // fireboard category images absolute path

define('JB_TMPLTURL', JB_DIRECTURL . '/template/' . $fb_cur_template);
define('JB_TMPLTMAINIMGURL', JB_DIRECTURL . '/template/' . $fb_cur_img_template);

// IMAGES URL PATH
define('JB_TMPLTCSSURL', JB_TMPLTURL . '/forum.css');

if (is_dir(JB_ABSTMPLTMAINIMGPATH . '/images/' . JB_LANG . '')) {
    define('JB_URLIMAGESPATH', JB_TMPLTMAINIMGURL . '/images/' . JB_LANG . '/');
    }
else {
    define('JB_URLIMAGESPATH', JB_TMPLTMAINIMGURL . '/images/english/');
    }

// url images path
define('JB_URLICONSPATH', JB_URLIMAGESPATH . 'icons/');

// url icons path
define('JB_URLEMOTIONSPATH', JB_URLIMAGESPATH . 'emoticons/');

// url emoticons path
define('JB_URLGRAPHPATH', JB_URLIMAGESPATH . 'graph/');

// url graph path
define('JB_URLRANKSPATH', JB_URLIMAGESPATH . 'ranks/');

// url ranks path
define('JB_URLCATIMAGES', FB_LIVEUPLOADEDPATH . '/' . $fbConfig->catimagepath); // fireboard category images direct url

if (file_exists(JB_ABSTMPLTPATH . '/js/jquery-latest.pack.js')) {
    define('JB_JQURL', JB_DIRECTURL . '/template/' . $fb_cur_template . '/js/jquery-latest.pack.js');
    }
else {
    define('JB_JQURL', JB_DIRECTURL . '/template/default/js/jquery-latest.pack.js');
    }

if (file_exists(JB_ABSTMPLTPATH . '/js/bojForumCore.js')) {
    define('JB_COREJSURL', JB_DIRECTURL . '/template/' . $fb_cur_template . '/js/bojForumCore.js');
    }
else {
    define('JB_COREJSURL', JB_DIRECTURL . '/template/default/js/bojForumCore.js');
    }

/**
 * gets Itemid of CB profile, or by default of homepage
 */
// ERROR: REMOVE
function JBgetCBprofileItemid($htmlspecialchars = false) {
    global $JB_CB__Cache_ProfileItemid, $database;

    if (!$JB_CB__Cache_ProfileItemid) {
        $database->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=com_comprofiler' AND published=1");
        $Itemid = (int)$database->loadResult();

        if (!$Itemid) {
            /** Nope, just use the homepage then. */
            $query = "SELECT id" . "\n FROM #__menu" . "\n WHERE menutype = 'mainmenu'" . "\n AND published = 1" . "\n ORDER BY parent, ordering" . "\n LIMIT 1";
            $database->setQuery($query);
            $Itemid = (int)$database->loadResult();
            }

        $JB_CB__Cache_ProfileItemid = $Itemid;
        }

    if ($JB_CB__Cache_ProfileItemid) {
        return $JB_CB__Cache_ProfileItemid;
        }
    else {
        return null;
        }
    }

function FB_fmodReplace($x, $y) { //function provided for older PHP versions which do not have an fmod function yet
    $i = floor($x / $y);
    // r = x - i * y
    return $x - $i * $y;
    }

function FB_check_image_type(&$type) {
    switch ($type)
    {
        case 'jpeg':
        case 'pjpeg':
        case 'jpg':
        case 'JPEG':
        case 'PJPEG':
        case 'JPG':
            return '.jpg';

            break;

        case 'gif':
        case 'GIF':
            return '.gif';

            break;

        case 'png':
        case 'PNG':
            return '.png';

            break;
    }

    return false;
    }

function getFBGroupName($id) {
    global $database;
    $gr = '';
    $database->setQuery("select id, title from #__fb_groups as g, #__fb_users as u where u.group_id = g.id and u.userid= $id");
    $database->loadObject($gr);

    if ($gr->id > 1) {
        return $gr;
        }
    }

function escape_quotes($receive) {
    if (!is_array($receive)) {
        $thearray = array ( $receive );
        }
    else {
        $thearray = $receive;
        }

    foreach (array_keys($thearray)as $string) {
        $thearray[$string] = addslashes($thearray[$string]);
        // Why changing escape to a forward slash?!
        $thearray[$string] = preg_replace("/[\\/]+/", "/", $thearray[$string]);
        }

    if (!is_array($receive)) {
        return $thearray[0];
        }
    else {
        return $thearray;
        }
    }

class FBTools {
    var $id = null;

/*
    function fbGetCurrentTime () {
    	// tells current FB internal representing time
        global $fbConfig;
        return time() + ($fbConfig->board_ofset * 3600);
    }
*/
    function fbGetInternalTime ($time=null) {
    	// tells internal FB representing time from UTC $time
        global $fbConfig;
        // Prevent zeroes
        if($time===0) {
          return 0;
        }
        if($time===null) {
          $time = time();
        }
        return $time + ($fbConfig->board_ofset * 3600);
    }

    function fbGetShowTime ($time=null, $space='FB') {
    	// converts internal (FB)|UTC representing time to display time
    	// could consider user properties (zones) for future
        global $fbConfig;
        // Prevent zeroes
        if($time===0) {
          return 0;
        }
        if($time===null) {
          $time = FBTools::fbGetInternalTime();
          $space = 'FB';
        }
        if($space=='UTC') {
          return $time + ($fbConfig->board_ofset * 3600);
        }
        return $time;
    }

    function whoisID($id) {
        global $database, $mosConfig_live_site;

        $id = intval($database->getEscaped($id));
        $database->setQuery("select username from #__users where id=$id");
        return $database->loadResult();
        }

    function reCountBoards() {
        global $database;
        include_once (JB_ABSSOURCESPATH . 'fb_db_iterator.class.php');

        //reset all stats to 0
        $database->setQuery("UPDATE `#__fb_categories` SET `id_last_msg`='0',`time_last_msg`='0',`numTopics`='0',`numPosts`='0'");
        $database->query();
        	check_dberror("Unable to update categories.");

        $database->setQuery("select id, time, parent, catid from #__fb_messages where hold=0 AND moved=0 order by id asc");
        $messages_iter = new fb_DB_Iterator($database);
        	check_dberror("Unable to load messages.");

        $database->setQuery("select id, parent, numTopics, numPosts,id_last_msg, time_last_msg from #__fb_categories order by id asc");
        $cats = $database->loadObjectList();
        	check_dberror("Unable to load messages.");

        foreach ($cats as $c) {
            $ctg[$c->id] = $c;
            }

        $i=0;
        while ($messages_iter->loadNextObject($l)) {
        	$i++;
            //if($i==100) { die(); }
            $cat_l = $l->catid;

            while ($cat_l) {
                if ($l->parent == 0) {
                    $ctg[$cat_l]->numTopics++;
                    }
                else {
                    $ctg[$cat_l]->numPosts++;
                    }

                $ctg[$cat_l]->id_last_msg = $l->id;
                $ctg[$cat_l]->time_last_msg = $l->time;
                $cat_l = $ctg[$cat_l]->parent;
                }
            }

        // now back to db
        foreach ($ctg as $cc) {
            $database->setQuery(
                "UPDATE `#__fb_categories` SET `time_last_msg`='" . $cc->time_last_msg . "',`id_last_msg`='" . $cc->id_last_msg . "',`numTopics`='" . $cc->numTopics . "',`numPosts`='" . $cc->numPosts . "' WHERE (`id`='" . $cc->id . "') ");
            $database->query();
            echo $database->getErrorMsg();
            }
        $messages_iter->Free();

        }

    function updateNameInfo()
    {
        global $database;
        global $fbConfig;

        $fb_queryName = $fbConfig->username ? "username" : "name";

	    $query = "UPDATE #__fb_messages AS m, #__users AS u
	    			SET m.name = u.$fb_queryName
					WHERE m.userid = u.id";
        $database->setQuery($query);
        $database->query();
        	check_dberror ("Unable to update user name information");
    }

    function modifyCategoryStats($msg_id, $msg_parent, $msg_time, $msg_cat) {
        global $database;
        $database->setQuery("select id, parent, numTopics, numPosts,id_last_msg, time_last_msg from #__fb_categories order by id asc");
        $cats = $database->loadObjectList();
        	check_dberror("Unable to load categories.");

        foreach ($cats as $c) {
            $ctg[$c->id] = $c;
            }

        while ($msg_cat) {
            // traverse parental from orig msg_cat
            if ($msg_parent == 0) {
                $ctg[$msg_cat]->numTopics++;
                }
            else {
                $ctg[$msg_cat]->numPosts++;
                }

            $ctg[$msg_cat]->id_last_msg = $msg_id;
            $ctg[$msg_cat]->time_last_msg = $msg_time;

            // store to db (only changed)
            $database->setQuery(
                "UPDATE `#__fb_categories`"
                ." SET `time_last_msg`='" . $ctg[$msg_cat]->time_last_msg . "'"
                .",`id_last_msg`='" . $ctg[$msg_cat]->id_last_msg . "'"
                .",`numTopics`='" . $ctg[$msg_cat]->numTopics . "'"
                .",`numPosts`='" . $ctg[$msg_cat]->numPosts . "'"
                ." WHERE (`id`='" . $ctg[$msg_cat]->id . "') ");
            $database->query();
            echo $database->getErrorMsg();

            // parent
            $msg_cat = $ctg[$msg_cat]->parent;
            }

        return;
        }

    function decreaseCategoryStats($msg_id, $msg_cat) {
        //topic : 1 , message = 0
        global $database;
        $database->setQuery("select id, parent, numTopics, numPosts,id_last_msg, time_last_msg from #__fb_categories order by id asc");
        $cats = $database->loadObjectList();
        	check_dberror("Unable to load categories.");

        foreach ($cats as $c) {
            $ctg[$c->id] = $c;
            }

        $database->setQuery('select id FROM #__fb_messages WHERE id=' . $msg_id . ' OR thread=' . $msg_id);

        $msg_ids = $database->loadResultArray();
        	check_dberror("Unable to load messages.");

        $cntTopics = 0;
        $cntPosts = 0;

        if (count($msg_ids) > 0) {
            foreach ($msg_ids as $msg) {
                if ($msg == $msg_id) {
                    $cntTopics = 1;
                    }
                else {
                    $cntPosts++;
                    }
                }
            }

        while ($msg_cat) {

            unset($lastMsgInCat);
            $database->setQuery("select id, time from #__fb_messages where catid={$msg_cat} and (thread <> {$msg_id} AND id<>{$msg_id}) order by time desc limit 1;");
            $database->loadObject($lastMsgInCat);
            	check_dberror("Unable to load messages.");

            $ctg[$msg_cat]->numTopics = (int) ($ctg[$msg_cat]->numTopics - $cntTopics);
            $ctg[$msg_cat]->numPosts = (int) ($ctg[$msg_cat]->numPosts - $cntPosts);

            $ctg[$msg_cat]->id_last_msg = $lastMsgInCat->id;
            $ctg[$msg_cat]->time_last_msg = $lastMsgInCat->time;

            $msg_cat = $ctg[$msg_cat]->parent;
            }

        // now back to db
        foreach ($ctg as $cc) {
            $database->setQuery("UPDATE `#__fb_categories` SET `time_last_msg`='" . $cc->time_last_msg . "',`id_last_msg`='" . $cc->id_last_msg . "',`numTopics`='" . $cc->numTopics . "',`numPosts`='" . $cc->numPosts . "' WHERE `id`='" . $cc->id . "' ");
            $database->query();
            	check_dberror("Unable to update categories.");
            }

        return;
        }

    function showBulkActionCats($disabled = 1) {
        global $database;

        $options = array ();
        $options[] = mosHTML::makeOption('0', "&nbsp;");
        $lists['parent'] = FB_GetAvailableForums(0, "", $options, $disabled);

        echo $lists['parent'];
        }

    function fbDeletePosts($isMod, $return) {
        global $my, $database;

        if (!FBTools::isModOrAdmin() && !$isMod) {
            return;
            }

        $items = fbGetArrayInts("fbDelete");
        $dellattach = 1;

        // start iterating here
        foreach ($items as $id => $value) {
            $database->setQuery('SELECT id,catid,parent,thread,subject,userid FROM #__fb_messages WHERE id=' . $id);

            if (!$database->query()) {
                return -2;
                }

            $database->loadObject($mes);
            $thread = $mes->thread;

            if ($mes->parent == 0) {
                // this is the forum topic; if removed, all children must be removed as well.
                $children = array ();
                $userids = array ();
                $database->setQuery('SELECT userid,id, catid FROM #__fb_messages WHERE thread=' . $id . ' OR id=' . $id);

                foreach ($database->loadObjectList()as $line) {
                    $children[] = $line->id;

                    if ($line->userid > 0) {
                        $userids[] = $line->userid;
                        }
                    }

                $children = implode(',', $children);
                $userids = implode(',', $userids);
                }
            else {
                //this is not the forum topic, so delete it and promote the direct children one level up in the hierarchy
                $database->setQuery('UPDATE #__fb_messages SET parent=\'' . $mes->parent . '\' WHERE parent=\'' . $id . '\'');

                if (!$database->query()) {
                    return -1;
                    }

                $children = $id;
                $userids = $mes->userid > 0 ? $mes->userid : '';
                }

            //Delete the post (and it's children when it's the first post)
            $database->setQuery('DELETE FROM #__fb_messages WHERE id=' . $id . ' OR thread=' . $id);

            if (!$database->query()) {
                return -2;
                }

            // now update stats
            FBTools::decreaseCategoryStats($id, $mes->catid);

            //Delete message text(s)
            $database->setQuery('DELETE FROM #__fb_messages_text WHERE mesid IN (' . $children . ')');

            if (!$database->query()) {
                return -3;
                }

            //Update user post stats
            if (count($userids) > 0) {
                $database->setQuery('UPDATE #__fb_users SET posts=posts-1 WHERE userid IN (' . $userids . ')');

                if (!$database->query()) {
                    return -4;
                    }
                }

            //Delete (possible) ghost post
            $database->setQuery('SELECT mesid FROM #__fb_messages_text WHERE message=\'catid=' . $mes->catid . '&amp;id=' . $id . '\'');
            $int_ghost_id = $database->loadResult();

            if ($int_ghost_id > 0) {
                $database->setQuery('DELETE FROM #__fb_messages WHERE id=' . $int_ghost_id);
                $database->query();
                $database->setQuery('DELETE FROM #__fb_messages_text WHERE mesid=' . $int_ghost_id);
                $database->query();
                }

            //Delete attachments
            if ($dellattach) {
                $database->setQuery('SELECT filelocation FROM #__fb_attachments WHERE mesid IN (' . $children . ')');
                $fileList = $database->loadObjectList();
                	check_dberror("Unable to load attachments.");

                if (count($fileList) > 0) {
                    foreach ($fileList as $fl) {
                        unlink ($fl->filelocation);
                        }

                    $database->setQuery('DELETE FROM #__fb_attachments WHERE mesid IN (' . $children . ')');
                    $database->query();
                    }
                }
            } //end foreach
            FBTools::reCountBoards();

            mosRedirect($return, _FB_BULKMSG_DELETED);
        }

    function isModOrAdmin($id = 0) {
        global $database, $my;
// echo '<div>CALL isModOrAdmin</div>';
        $userid = intval($id);

        if ($userid) {
            $user = new mosUser($database);
            $user->load($userid);
            }
        else {
            $user = $my;
            }

        if (strtolower($user->usertype) == 'super administrator' || strtolower($user->usertype) == 'administrator') {
            return true;
            }

            return false;
        }

    function fbMovePosts($catid, $isMod, $return) {
        global $my, $database;

        //isMod will stay until better group management comes in
        if (!FBTools::isModOrAdmin() && !$isMod) {
            return;
            }

		$catid = (int)$catid;
		if ($catid > 0) {
	        $items = fbGetArrayInts("fbDelete");

	        // start iterating here
	        foreach ($items as $id => $value) {
	            $id = (int)$id;

	            $database->setQuery("SELECT `subject`, `catid`, `time` AS timestamp FROM #__fb_messages WHERE `id`=" . $id);
	            $oldRecord = $database->loadObjectList();
	            	check_dberror("Unable to load message detail.");

	            $newSubject = _MOVED_TOPIC . " " . $oldRecord[0]->subject;
	            $database->setQuery("SELECT MAX(time) AS timestamp FROM #__fb_messages WHERE `thread`=" . $id);
	            $lastTimestamp = $database->loadResult();
	            	check_dberror("Unable to load messages max(time).");

	            if ($lastTimestamp == "") {
	                $lastTimestamp = $oldRecord[0]->timestamp;
                }

	            //perform the actual move
				$database->setQuery("UPDATE #__fb_messages SET `catid`='$catid' WHERE `id`='$id' OR `thread`='$id'");
				$database->query();
					check_dberror("Unable to move thread.");
	        } //end foreach
		} else {
			$err = _POST_NO_DEST_CATEGORY;
		}
        FBTools::reCountBoards();

        mosRedirect($return, $err ? $err : _POST_SUCCESS_MOVE);
        }

        function isJoomla15()
        {
            return (defined('_JEXEC') && class_exists('JApplication'));
        }


        function fbRemoveXSS($val, $reverse = 0) {

           // now the only remaining whitespace attacks are \t, \n, and \r
           $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
           $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
           $ra = array_merge($ra1, $ra2);

           $ra2 = $ra;
           array_walk($ra2, "fbReturnDashed");

           if ($reverse) {
                $val = str_ireplace($ra2, $ra, $val);
           }
           else {
           //replace them all with a dummy variable, and later replace them in CODE
                $val = str_ireplace($ra, $ra2, $val);
           }
           return $val;
        }

		function getAllowedForums($uid = 0, $gid = 0, &$acl) {
        	global $database;

			function _has_rights(&$acl, $gid, $access, $recurse) {
				if ($gid == $access) return 1;
				if ($recurse) {
					$childs = $acl->get_group_children($access, 'ARO', 'RECURSE');
					return (is_array($childs) and in_array($gid, $childs));
				}
				return 0;
			}

			$catlist = '';
			$query = "SELECT c.id,c.pub_access,c.pub_recurse,c.admin_access,c.admin_recurse,c.moderated"
				. ",(m.userid IS NOT NULL) AS ismod FROM #__fb_categories c"
				. " LEFT JOIN #__fb_moderation m ON c.id=m.catid AND m.userid="
				. (int)$uid . " WHERE published=1";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
					check_dberror("Unable to load category list.");
			if ($rows) {
				foreach($rows as $row) {
					if (($row->moderated and $row->ismod) or
						($row->pub_access == 0) or
						($row->pub_access == -1 and $uid > 0) or
						($row->pub_access > 0 and _has_rights($acl, $gid, $row->pub_access, $row->pub_recurse)) or
						($row->admin_access > 0 and _has_rights($acl, $gid, $row->admin_access, $row->admin_recurse))
					) $catlist .= (($catlist == '')?'':',').$row->id;
				}
			}
			return $catlist;
		}

    } // end of class

/**
* Users Table Class
* Provides access to the #__fb_users table
*/
class fbUserprofile
    extends mosDBTable {
    var $userid = null;
    var $view = null;
    var $signature = null;
    var $moderator = null;
    var $ordering = null;
    var $posts = null;
    var $avatar = null;
    var $karma = null;
    var $karma_time = null;
    var $group_id = null;
    var $uhits = null;
    var $personalText = null;
    var $gender = null;
    var $birthdate = null;
    var $location = null;
    var $ICQ = null;
    var $AIM = null;
    var $YIM = null;
    var $MSN = null;
    var $SKYPE = null;
	var $GTALK = null;
	var $websitename = null;
	var $websiteurl = null;
    var $hideEmail = null;
    var $showOnline = null;
    /**
    * @param database A database connector object
    */
    function fbUserprofile(&$database) {
        $this->mosDBTable('#__fb_users', 'userid', $database);
        }
    }
/**
* Moderator Table Class
*
* Provides access to the #__fb_moderator table
*/
class fbModeration
    extends mosDBTable {
    /** @var int Unique id*/
    var $catid = null;
    /** @var int */
    var $userid = null;
    /** @var int */
    var $future1 = null;
    /** @var int */
    var $future2 = null;
    /**
    * @param database A database connector object
    */
    function fbModeration(&$database) {
        $this->mosDBTable('#__fb_moderation', 'catid', $database);
        }
    }

class fbForum
    extends mosDBTable {
    /** @var int Unique id*/
    var $id = null;
    /** @var string */
    var $parent = null;
    /** @var string */
    var $name = null;
    var $cat_emoticon = null;
    var $locked = null;
    var $alert_admin = null;
    var $moderated = null;
    var $pub_access = null;
    var $pub_recurse = null;
    var $admin_access = null;
    var $admin_recurse = null;
    var $public = null;
    var $ordering = null;
    var $future2 = null;
    var $published = null;
    var $checked_out = null;
    var $checked_out_time = null;
    var $review = null;
    var $hits = null;
    var $description = null;
    var $class_sfx = null;
    var $headerdesc = null;
    /**
    * @param database A database connector object
    */
    function fbForum(&$database) {
        $this->mosDBTable('#__fb_categories', 'id', $database);
    }

	// check for potential problems
	function check() {
		$this->_error = '';
		if ($this->parent) {
			if ($this->id == $this->parent):
				$this->setError(_FB_FORUM_SAME_ERR);
			elseif ($this->isChild($this->parent)):
				$this->setError(_FB_FORUM_OWNCHILD_ERR);
			endif;
		}
		return ($this->getError() == '');
	}

	// check if given forum is one of its own childs
	function isChild($id) {
		if ($id > 0) {
			$query = "SELECT id, parent FROM #__fb_categories";
			$this->_db->setQuery($query);
			$this->_db->query() or trigger_dberror("Unable to access categories.");
			$list = $this->_db->loadObjectList('id');
			$recurse = array();
			while ($id) {
				if (in_array($id, $recurse)) {
					$this->setError(_FB_RECURSION);
					return 0;
				}
				$recurse[] = $id;
				if (!isset($list[$id])) {
					$this->setError(_FB_FORUM_UNKNOWN_ERR);
					return 0;
				}
				$id = $list[$id]->parent;
				if ($id <> 0 and $id == $this->id)
					return 1;
			};
		}
		return 0;
	}

	function setError($msg) {
		$this->_error = ($msg <> '')?$msg:'error';
	}

	function store($updateNulls=false) {
		if ($ret = parent::store($updateNulls)) {
			// we must reset fbSession (allowed), when forum record was changed

			$this->_db->setQuery("UPDATE #__fb_sessions SET allowed='na'");
			$this->_db->query() or trigger_dberror("Unable to update sessions.");
		}
		return $ret;
	}

}

function JJ_categoryArray($admin=0) {
    global $database;
    // ERROR: mixed global $fbSession
    global $fbSession;
    global $aro_group;

    // get a list of the menu items
	$query = "SELECT c.* FROM #__fb_categories c";
	if(!$admin) {
		if ($fbSession) {
			$query .= " WHERE c.id IN ($fbSession->allowed)";
		} else {
			$query .= " WHERE pub_access=0 AND published=1";
		}
	}
    $query .= " ORDER BY ordering, name";
    $database->setQuery($query);
    $items = $database->loadObjectList();
    	check_dberror("Unable to load categories.");
    // establish the hierarchy of the menu
    $children = array ();

    // first pass - collect children
    foreach ($items as $v) {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array ();
        array_push($list, $v);
        $children[$pt] = $list;
        }

    // second pass - get an indent list of the items
    $array = fbTreeRecurse(0, '', array (), $children, 10, 0, 1);
    return $array;
    }

function fbTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 ) {

    if (@$children[$id] && $level <= $maxlevel) {
        foreach ($children[$id] as $v) {
            $id = $v->id;
            if ( $type ) {
                $pre     = '&nbsp;';
                $spacer = '...';
            } else {
                $pre     = '- ';
                $spacer = '&nbsp;&nbsp;';
            }

            if ( $v->parent == 0 ) {
                $txt     = $v->name;
            } else {
                $txt     = $pre . $v->name;
            }
            $pt = $v->parent;
            $list[$id] = $v;
            $list[$id]->treename = "$indent$txt";
            $list[$id]->children = count( @$children[$id] );

            $list = fbTreeRecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
        }
    }
    return $list;
}

function JJ_categoryParentList($catid, $action, $options = array ()) {
    global $database;

    $list = JJ_categoryArray();
    $this_treename = '';

    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $catid && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
                }
            }
        else {
            if ($item->id != $catid) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
                }
            else {
                $this_treename = "$item->treename/";
                }
            }
        }

    $parent = mosHTML::selectList($options, 'catid', 'class="inputbox fbs" size="1"  onchange = "if(this.options[this.selectedIndex].value > 0){ forms[\'jumpto\'].submit() }"', 'value', 'text', $catid);
    return $parent;
    }

function FB_GetAvailableForums($catid, $action, $options = array (), $disabled, $multiple = 0) {
    global $database;
    $list = JJ_categoryArray();
    $this_treename = '';

    foreach ($list as $item) {
        if ($this_treename) {
            if ($item->id != $catid && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
                }
            }
        else {
            if ($item->id != $catid) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
                }
            else {
                $this_treename = "$item->treename/";
                }
            }
        }

	$tag_attribs = 'class="inputbox fbs" '.($multiple?' size="5" MULTIPLE ':' size="1" ') . ($disabled ? " disabled " : "");
	if (FBTools::isJoomla15()) {
    	$parent = JHTML::_('select.genericlist', $options, 'catid', $tag_attribs , 'value', 'text', $catid, 'FB_AvailableForums');
		}
    else {
		$parent = mosHTML::selectList($options, 'catid', $tag_attribs . ' ID="FB_AvailableForums"' , 'value', 'text', $catid);
		}
    return $parent;
    }

//
//Begin Smilies mod
//
function generate_smilies() {
    global $database;

    $inline_columns = 4;
    $inline_rows = 5;

    $database->setQuery("SELECT code, location, emoticonbar FROM #__fb_smileys ORDER BY id");

    if ($database->query()) {
        $num_smilies = 0;
        $rowset = array ();
        $set = $database->loadAssocList();
        $num_iconbar = 0;

        foreach ($set as $smilies) {
            $key_exists = false;

            foreach ($rowset as $check) //checks if the smiley (location) already exists with another code
            {
                if ($check['location'] == $smilies['location']) {
                    $key_exists = true;
                    }
                }

            if ($key_exists == false) {
                $rowset[] = array
                (
                    'code' => $smilies['code'],
                    'location' => $smilies['location'],
                    'emoticonbar' => $smilies['emoticonbar']
                );
                }

            if ($smilies['emoticonbar'] == 1) {
                $num_iconbar++;
                }
            }

        $num_smilies = count($rowset);

        if ($num_smilies) {
            $smilies_count = min(20, $num_smilies);
            $smilies_split_row = $inline_columns - 1;

            $s_colspan = 0;
            $row = 0;
            $col = 0;
            reset ($rowset);

            if (file_exists(JB_ABSPATH . '/template/default/plugin/emoticons/emoticons.js.php')) {
                include (JB_ABSPATH . '/template/default/plugin/emoticons/emoticons.js.php');
                reset ($rowset);
                }
            else {
                die ("file is missing: " . JB_ABSPATH . '/template/default/plugin/emoticons/emoticons.js.php');
                }

            $cur = 0;

            foreach ($rowset as $data) {
                if ($data['emoticonbar'] == 1) {
                    $cur++;

                    if (!($cur > $inline_rows * $inline_columns)) {
                        if (!$col) {
                            echo '<tr align="center" valign="middle">' . "\n";
                            }

                        echo '<td onclick="javascript:emo(\''
                                 . $data['code'] . ' \')" style="cursor:pointer"><img class="btnImage" src="' . JB_URLEMOTIONSPATH . $data['location'] . '" border="0" alt="' . $data['code'] . ' " title="' . $data['code'] . ' " /></td>' . "\n";

                        $s_colspan = max($s_colspan, $col + 1);

                        if ($col == $smilies_split_row) {
                            $col = 0;
                            $row++;
                            echo "</tr>\n";
                            }
                        elseif ($cur == $num_iconbar && $s_colspan !== 0) {
                            echo "<td colspan=\"" . $s_colspan . "\"></td></tr>";
                            }
                        else {
                            $col++;
                            }
                        }
                    }
                }

            if ($num_smilies > $inline_rows * $inline_columns) {
                echo "<tr><td class=\"moresmilies\" colspan=\"" . $inline_columns . "\" onclick=\"javascript:moreForumSmileys();\" style=\"cursor:pointer\"><b>" . _FB_EMOTICONS_MORE_SMILIES . "</b></td></tr>";
                }
            }
        }
    }

function fbGetArrayInts($name, $type = NULL) {
    if ($type == NULL) {
        $type = $_POST;
        }

    $array = mosGetParam($type, $name, array ( 0 ));

    mosArrayToInts ($array);

    if (!is_array($array)) {
        $array = array ( 0 );
        }

    return $array;
    }

    function time_since($older_date, $newer_date)
    {
    // ToDo: return code plus string to decide concatenation.
    // array of time period chunks
    $chunks = array(
    array(60 * 60 * 24 * 365 , _FB_DATE_YEAR, _FB_DATE_YEARS),
    array(60 * 60 * 24 * 30 , _FB_DATE_MONTH, _FB_DATE_MONTHS),
    array(60 * 60 * 24 * 7, _FB_DATE_WEEK, _FB_DATE_WEEKS),
    array(60 * 60 * 24 , _FB_DATE_DAY, _FB_DATE_DAYS),
    array(60 * 60 , _FB_DATE_HOUR, _FB_DATE_HOURS),
    array(60 , _FB_DATE_MINUTE, _FB_DATE_MINUTES),
    );

    // $newer_date will equal false if we want to know the time elapsed between a date and the current time
    // $newer_date will have a value if we want to work out time elapsed between two known dates
    //$newer_date = ($newer_date == false) ? FBTools::fbGetInternalTime() : $newer_date;

    // difference in seconds
    $since = $newer_date - $older_date;

    // no negatives!
    if($since<=0) {
      return '?';
    }

    // we only want to output two chunks of time here, eg:
    // x years, xx months
    // x days, xx hours
    // so there's only two bits of calculation below:

    // step one: the first chunk
    for ($i = 0, $j = count($chunks); $i < $j; $i++)
        {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        $names = $chunks[$i][2];

        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0)
            {
            break;
            }
        }

    // set output var
    $output = ($count == 1) ? '1 '.$name : $count.' '.$names ;

    // step two: the second chunk
    if ($i + 1 < $j)
        {
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];
        $names2 = $chunks[$i + 1][2];

        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
            {
            // add to output var
            $output .= ($count2 == 1) ? ', 1 '.$name2 : ', '.$count2.' '.$names2;
            }
        }

    return $output;
    }

function make_pattern(&$pat, $key) {
  $pat = '/'.preg_quote($pat, '/').'/i';
}
if(!function_exists('str_ireplace')){
function str_ireplace($search, $replace, $subject){
if(is_array($search)){
array_walk($search, 'make_pattern');
}
else{
$search = '/'.preg_quote($search, '/').'/i';
}
return preg_replace($search, $replace, $subject);
}
}

function fbReturnDashed (&$string, $key) {
            $string = "_".$string."_";
}

if (!function_exists('mb_detect_encoding')) {
  // We're on an aged PHP version
  function mb_detect_encoding($text) {
    return 'UTF-8';
  }
  function mb_convert_encoding($text,$target_encoding,$source_encoding) {
    return $text;
  }
}

function utf8_urldecode($str) {
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x1;",urldecode($str));
	return html_entity_decode_utf8($str);
}

function html_entity_decode_utf8($string)
{
    static $trans_tbl;

    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'code2utf(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'code2utf(\\1)', $string);

    // replace literal entities
    if (!isset($trans_tbl))
    {
        $trans_tbl = array();

        foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
            $trans_tbl[$key] = utf8_encode($val);
    }

    return strtr($string, $trans_tbl);
}

// Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
function code2utf($num)
{
    if ($num < 128) return chr($num);
    if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    return '';
}



