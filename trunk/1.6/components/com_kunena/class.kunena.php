<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

jimport('joomla.utilities.string');

// Joomla absolute path
define('KUNENA_JLIVEURL', JURI::root());

require_once (KUNENA_PATH_LIB .DS. "kunena.config.class.php");

$kunena_app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$kunena_config =& CKunenaConfig::getInstance();
$kunena_db = &JFactory::getDBO();
$kunena_my = &JFactory::getUser();

// Joomla template dir
define('KUNENA_JTEMPLATEPATH', KUNENA_ROOT_PATH .DS. "templates".DS . $kunena_app->getTemplate());
define('KUNENA_JTEMPLATEURL', KUNENA_JLIVEURL. "templates/".$kunena_app->getTemplate());

//check if we have all the itemid sets. if so, then no need for DB call
if (!defined("KUNENA_COMPONENT_ITEMID"))
{
	$kunena_db->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_kunena' AND published='1'");
    $kid = $kunena_db->loadResult();
    check_dberror ( "Unable to load kunena itemid." );

    define("KUNENA_COMPONENT_ITEMID", (int)$kid);
    define("KUNENA_COMPONENT_ITEMID_SUFFIX", "&amp;Itemid=" . KUNENA_COMPONENT_ITEMID);

    //JomSocial
    if ($kunena_config->pm_component == 'jomsocial' || $kunena_config->fb_profile == 'jomsocial' || $kunena_config->avatar_src == 'jomsocial')
    {
    	// Only proceed if jomSocial is really installed
	    if ( file_exists( KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/core.php' ) )
	    {
	        $kunena_db->setQuery("SELECT id FROM #__menu WHERE link LIKE 'index.php?option=com_community%' AND published='1' ORDER BY id ASC LIMIT 1");
	        $JOMSOCIAL_Itemid = $kunena_db->loadResult();
	        	check_dberror('Unable to load jomSocial itemid');

	        define("KUNENA_JOMSOCIAL_ITEMID", (int)$JOMSOCIAL_Itemid);
	        define("KUNENA_JOMSOCIAL_ITEMID_SUFFIX", "&amp;Itemid=" . KUNENA_JOMSOCIAL_ITEMID);

			include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/core.php');
			include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/messaging.php');
			// A bug in the JomSocial 1.6 pre-release is throwing a hard php error when this include is enabled
			// for now I have moved it straight into post.php - the only place the userpoints classes
			// are being used. We might want to change that back for future releases.
			// include_once(KUNENA_ROOT_PATH .DS. 'components/com_community/libraries/userpoints.php');

			//PM popup requires JomSocial css to be loaded from selected template
			$config =& CFactory::getConfig();
			$document->addStyleSheet(KUNENA_JLIVEURL.'components/com_community/assets/window.css');
			$document->addStyleSheet(KUNENA_JLIVEURL.'components/com_community/templates/'.$config->get('template').'/css/style.css');
	    }
	    else
	    {
	    	// JomSocial not present reset config settings to avoid problems
	    	$kunena_config->pm_component = $kunena_config->pm_component == 'jomsocial' ? 'none' : $kunena_config->pm_component;
	    	$kunena_config->fb_profile = $kunena_config->fb_profile == 'jomsocial' ? 'kunena' : $kunena_config->fb_profile;
	    	$kunena_config->avatar_src = $kunena_config->avatar_src == 'jomsocial' ? 'kunena' : $kunena_config->avatar_src;
	    }
    }

    //Community Builder 1.2 integration
	if ($kunena_config->pm_component == 'cb' || $kunena_config->fb_profile == 'cb' || $kunena_config->avatar_src == 'cb')
    {
		// Get Community Builder compability
		require_once (KUNENA_PATH_LIB .DS. "kunena.communitybuilder.php");
		global $kunenaProfile;
		$kunenaProfile =& CkunenaCBProfile::getInstance();
    }

    // UddeIM
    if ($kunena_config->pm_component == 'uddeim') {
        $kunena_db->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_uddeim' AND published='1'");
        $UIM_itemid = $kunena_db->loadResult();
        check_dberror('Unable to load uddeim itemid');

        define("KUNENA_UIM_ITEMID", (int)$UIM_itemid);
        define("KUNENA_UIM_ITEMID_SUFFIX", "&amp;Itemid=" . KUNENA_UIM_ITEMID);
        }

    // PROFILE LINK
    if ($kunena_config->fb_profile == "jomsocial") {
        $profilelink = 'index.php?option=com_community&amp;view=profile&amp;userid=';
        define("KUNENA_PROFILE_LINK_SUFFIX", "index.php?option=com_community&amp;view=profile&amp;Itemid=" . KUNENA_JOMSOCIAL_ITEMID . "&amp;userid=");
        }
    else if ($kunena_config->fb_profile == "cb") {
        $profilelink = 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user=';
        define("KUNENA_PROFILE_LINK_SUFFIX", "index.php?option=com_comprofiler&amp;task=userProfile" . KUNENA_CB_ITEMID_SUFFIX . "&amp;user=");
        }
    else if ($kunena_config->fb_profile == "aup") { // integration AlphaUserPoints
		$db	   =& JFactory::getDBO();
		$query = "SELECT id FROM #__menu WHERE `link`='index.php?option=com_alphauserpoints&view=account' AND `type`='component' AND `published`='1'";
		$db->setQuery( $query );
		check_dberror ( "Unable to load aup itemid." );
		$AUP_itemid = $db->loadResult();
		define("KUNENA_AUP_ITEMID", (int)$AUP_itemid);
		define("KUNENA_AUP_ITEMID_SUFFIX", "&amp;Itemid=" . KUNENA_AUP_ITEMID);
		$profilelink = 'index.php?option=com_alphauserpoints&amp;view=account&amp;userid=';
        define("KUNENA_PROFILE_LINK_SUFFIX", "index.php?option=com_alphauserpoints&amp;view=account&amp;Itemid=" . KUNENA_AUP_ITEMID . "&amp;userid=");
        }
    else {
        $profilelink = 'index.php?option=com_kunena&amp;func=profile&amp;userid=';
        define("KUNENA_PROFILE_LINK_SUFFIX", "index.php?option=com_kunena&amp;func=profile&amp;Itemid=" . KUNENA_COMPONENT_ITEMID . "&amp;userid=");
        }
    }

/*       _\|/_
         (o o)
 +----oOO-{_}-OOo--------------------------------+
 | Now we have the components Itemids everywhere |
 | Please use these constants where ever needed  |
 +----------------------------------------------*/

// Kunena live url
define('KUNENA_LIVEURL', KUNENA_JLIVEURL . 'index.php?option=com_kunena' . KUNENA_COMPONENT_ITEMID_SUFFIX);
define('KUNENA_LIVEURLREL', 'index.php?option=com_kunena' . KUNENA_COMPONENT_ITEMID_SUFFIX);

// Kunena souces absolute path
define('KUNENA_DIRECTURL', KUNENA_JLIVEURL . 'components/com_kunena/');

if (!defined("KUNENA_JCSSURL")) {
    $kunena_db->setQuery("SELECT template FROM #__templates_menu WHERE client_id='0'");
    $current_stylesheet = $kunena_db->loadResult();
    check_dberror ( "Unable to load template." );
    define('KUNENA_JCSSURL', KUNENA_JLIVEURL . 'templates/' . $current_stylesheet . '/css/template_css.css');
}

// Kunena uploaded files directory
define('KUNENA_LIVEUPLOADEDPATH', KUNENA_JLIVEURL . 'images/fbfiles');


// now continue with other paths

$fb_user_template = JRequest::getString('fb_user_template', '', 'COOKIE');
$fb_user_img_template = JRequest::getString('fb_user_img_template', '', 'COOKIE');
// don't allow directory travelling
$fb_user_template = strtr($fb_user_template, '\\/', '');
$fb_user_img_template = strtr($fb_user_template, '\\/', '');

if (JString::strlen($fb_user_template) > 0 && file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_user_template .DS. 'css'))
{
    $fb_cur_template = $fb_user_template;
    }
else if (file_exists(KUNENA_PATH_TEMPLATE .DS. $kunena_config->template .DS. 'css'))
{
    $fb_cur_template = $kunena_config->template;
    }
else
{
    $fb_cur_template = 'default';
    }

if (JString::strlen($fb_user_img_template) > 0 && file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_user_template .DS. 'images'))
{
    $fb_cur_img_template = $fb_user_img_template;
    }
else if (file_exists(KUNENA_PATH_TEMPLATE .DS. $kunena_config->templateimagepath .DS. 'images'))
{
    $fb_cur_img_template = $kunena_config->templateimagepath;
    }
else
{
    $fb_cur_img_template = 'default';
    }

// only for preview module - maybe used later by users to change template

define('KUNENA_ABSTMPLTPATH', KUNENA_PATH_TEMPLATE .DS. $fb_cur_template);
define('KUNENA_ABSTMPLTMAINIMGPATH', KUNENA_PATH_TEMPLATE .DS. $fb_cur_img_template);

define('KUNENA_ABSIMAGESPATH', KUNENA_ABSTMPLTMAINIMGPATH .DS. 'images/');

// absolute icons path
define('KUNENA_ABSICONSPATH', KUNENA_ABSIMAGESPATH . 'icons/');

// absolute emoicons path
define('KUNENA_ABSEMOTIONSPATH', KUNENA_ABSIMAGESPATH . 'emoticons/');

// absolute ranks path
define('KUNENA_ABSRANKSPATH', KUNENA_ABSIMAGESPATH . 'ranks/');

// absolute catimages path
define('KUNENA_ABSCATIMAGESPATH', KUNENA_PATH_UPLOADED .DS. $kunena_config->catimagepath); // Kunena category images absolute path

define('KUNENA_TMPLTURL', KUNENA_DIRECTURL . "template/{$fb_cur_template}/");
define('KUNENA_TMPLTMAINIMGURL', KUNENA_DIRECTURL . "template/{$fb_cur_img_template}/");

// IMAGES URL PATH
define('KUNENA_TMPLTCSSURL', KUNENA_TMPLTURL . 'css/kunena.forum.css');

define('KUNENA_URLIMAGESPATH', KUNENA_TMPLTMAINIMGURL . 'images/');

// url icons path
define('KUNENA_URLICONSPATH', KUNENA_URLIMAGESPATH . 'icons/');

// url emoicons path
define('KUNENA_URLEMOTIONSPATH', KUNENA_URLIMAGESPATH . 'emoticons/');

// url ranks path
define('KUNENA_URLRANKSPATH', KUNENA_URLIMAGESPATH . 'ranks/');

// url catimages path
define('KUNENA_URLCATIMAGES', KUNENA_LIVEUPLOADEDPATH ."/{$kunena_config->catimagepath}/"); // Kunena category images direct url

function kunena_check_image_type($type) {
    switch (strtolower($type))
    {
        case 'jpeg':
        case 'pjpeg':
        case 'jpg':
            return '.jpg';

            break;

        case 'gif':
            return '.gif';

            break;

        case 'png':
            return '.png';

            break;
    }

    return false;
    }

class CKunenaTools {
    var $id = null;

    function showButton($name, $text) {
		return '<span class="'.$name.'"><span>'.$text.'</span></span>';
    }

	function showModulePosition($position) {
		$html = '';
		if (JDocumentHTML::countModules ( $position )) {
			$document = &JFactory::getDocument ();
			$renderer = $document->loadRenderer ( 'modules' );
			$options = array ('style' => 'xhtml' );
			$html .= '<div class="'.$position.'">';
			$html .= $renderer->render ( $position, $options, null );
			$html .= '</div>';
		}
		echo $html;
	}

    function getMessageId() {
    	$page = JRequest::getInt ( 'page', 0 );
    	$limitstart = JRequest::getInt ( 'limitstart', 0 );
        $kunena_config = & CKunenaConfig::getInstance ();
        $msg_html = new StdClass ( );
        $msg_html->id = $this->kunena_message->id;

        if ($kunena_config->ordering_system == 'old_ord') {
    		echo CKunenaLink::GetSamePageAnkerLink ( $msg_html->id, '#' . $msg_html->id );
    		} else {
    		if ($kunena_config->default_sort == 'desc') {
    			if ( $page == '1') {
    				$numb = $this->total_messages--;
    				echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$numb);
    			} else {
    				$nums = $this->total_messages - $limitstart;
    				$numb = $nums;
    				echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$numb);
    				$this->total_messages--;
    			}
    			} else {
    			if ( $page == '1') {
    				echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$this->mmm);
    				}else {
    				$nums = $this->mmm + $limitstart;
    				echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$nums);
    				}
    			}
    		}
    }

	function parseText($txt) {
		if (!$txt) return;
		$txt = stripslashes ( $txt );
		$txt = nl2br ( $txt );
		$txt = CKunenaTools::prepareContent ( $txt );
		return $txt;
	}

	function parseBBCode($txt) {
		static $emoticons = null;

		if (!$txt) return;
		if (!$emoticons) $emoticons = smile::getEmoticons ( 0 );
		$kunena_config = & CKunenaConfig::getInstance ();
		$txt = stripslashes ( $txt );
		$txt = smile::smileReplace ( $txt, 0, $kunena_config->disemoticons, $emoticons );
		$txt = nl2br ( $txt );
		$txt = str_replace ( "__FBTAB__", "&#009;", $txt ); // For [code]
		$txt = CKunenaTools::prepareContent ( $txt );
		return $txt;
	}

    function reCountUserPosts() {
    	$kunena_db = &JFactory::getDBO();

        // Reset category counts as next query ignores users which have written no messages
        $kunena_db->setQuery("UPDATE #__fb_users SET posts=0");
        $kunena_db->query();
          	check_dberror("Unable to reset category post counts.");

          	// Update user post count (ignore unpublished categories and hidden messages)
    	$kunena_db->setQuery("INSERT INTO #__fb_users (userid, posts)"
    		." SELECT m.userid, COUNT(m.userid) "
    		." FROM #__fb_messages AS m"
    		." INNER JOIN #__fb_users AS u ON u.userid = m.userid"
    		." WHERE m.hold=0 and m.catid IN (SELECT id FROM #__fb_categories WHERE published=1)"
    		." GROUP BY m.userid"
    		." ON DUPLICATE KEY UPDATE posts=VALUES(posts)");
    	$kunena_db->query();
        check_dberror("Unable to update user posts.");
    }

    function reCountBoardsRecursion(&$array, $current)
    {
    	foreach ($array[$current]->children as $child)
    	{
    		if (!$array[$child]->published) continue;
    		if (!empty($array[$child]->children)) CKunenaTools::reCountBoardsRecursion($array, $child);
    		$array[$current]->numTopics += $array[$child]->numTopics;
    		$array[$current]->numPosts += $array[$child]->numPosts;
    		if (isset($array[$current]->id) && $array[$child]->id_last_msg > $array[$current]->id_last_msg)
    		{
    			$array[$current]->id_last_msg = $array[$child]->id_last_msg;
    			$array[$current]->time_last_msg = $array[$child]->time_last_msg;
    		}
    	}
    }

    function reCountBoards()
    {
        $kunena_db = &JFactory::getDBO();

        // Reset category counts as next query ignores empty categories
        $kunena_db->setQuery("UPDATE #__fb_categories SET numTopics=0, numPosts=0");
        $kunena_db->query();
          	check_dberror("Unable to reset category post counts.");

        // Update category post count
        $kunena_db->setQuery("INSERT INTO #__fb_categories (id, numTopics, numPosts, id_last_msg, time_last_msg)"
        	." SELECT c.id, SUM( m.parent=0 ), SUM( m.parent>0 ), MAX( m.id ), MAX( m.time )"
        	." FROM #__fb_messages as m"
        	." INNER JOIN #__fb_categories AS c ON c.id=m.catid"
        	." WHERE m.catid>0 AND m.hold=0"
        	." GROUP BY catid "
        	." ON DUPLICATE KEY UPDATE numTopics=VALUES(numTopics), numPosts=VALUES(numPosts), id_last_msg=VALUES(id_last_msg), time_last_msg=VALUES(time_last_msg)");
    	$kunena_db->query();
    		check_dberror("Unable to update categories post count.");

    	// Load categories to be counted
        $kunena_db->setQuery("SELECT id, parent, published, numTopics, numPosts, id_last_msg, time_last_msg FROM #__fb_categories");
        $cats = $kunena_db->loadObjectList('id');
        	check_dberror("Unable to load categories.");

        foreach ($cats as $c)
        {
            if (isset($cats[$c->parent])) $cats[$c->parent]->children[] = $c->id;
            else $cats[0]->children[] = $c->id;
        }

        CKunenaTools::reCountBoardsRecursion($cats, 0);

        // now back to db
        foreach ($cats as $c)
        {
        	if (!isset($c->id)) continue;
            $kunena_db->setQuery("UPDATE #__fb_categories SET"
            	."  numTopics=" . intval($c->numTopics)
            	.", numPosts=" . intval($c->numPosts)
            	.", id_last_msg=" . intval($c->id_last_msg)
            	.", time_last_msg=" . intval($c->time_last_msg)
            	." WHERE id=" . intval($c->id));
            $kunena_db->query();
            	check_dberror("Unable to update categories.");
        }
    }

    function updateNameInfo()
    {
        $kunena_db = &JFactory::getDBO();
        $kunena_config =& CKunenaConfig::getInstance();

        $fb_queryName = $kunena_config->username ? "username" : "name";

	    $query = "UPDATE #__fb_messages AS m, #__users AS u
	    			SET m.name = u.$fb_queryName
					WHERE m.userid = u.id";
        $kunena_db->setQuery($query);
        $kunena_db->query();
        	check_dberror ("Unable to update user name information");
        return $kunena_db->getAffectedRows();
    }

    function modifyCategoryStats($msg_id, $msg_parent, $msg_time, $msg_cat) {
        $kunena_db = &JFactory::getDBO();
        $kunena_db->setQuery("SELECT id, parent, numTopics, numPosts, id_last_msg, time_last_msg FROM #__fb_categories ORDER BY id ASC");
        $cats = $kunena_db->loadObjectList();
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
            $kunena_db->setQuery(
                "UPDATE `#__fb_categories`"
                ." SET `time_last_msg`='" . $ctg[$msg_cat]->time_last_msg . "'"
                .",`id_last_msg`='" . $ctg[$msg_cat]->id_last_msg . "'"
                .",`numTopics`='" . $ctg[$msg_cat]->numTopics . "'"
                .",`numPosts`='" . $ctg[$msg_cat]->numPosts . "'"
                ." WHERE (`id`='" . $ctg[$msg_cat]->id . "') ");
            $kunena_db->query();
            check_dberror("Unable to update category stats.");

            // parent
            $msg_cat = $ctg[$msg_cat]->parent;
            }

        return;
        }

    function decreaseCategoryStats($msg_id, $msg_cat) {
        //topic : 1 , message = 0
        $kunena_db = &JFactory::getDBO();
        $kunena_db->setQuery("SELECT id, parent, numTopics, numPosts, id_last_msg, time_last_msg FROM #__fb_categories ORDER BY id ASC");
        $cats = $kunena_db->loadObjectList();
        	check_dberror("Unable to load categories.");

        foreach ($cats as $c) {
            $ctg[$c->id] = $c;
            }

        $kunena_db->setQuery("SELECT id FROM #__fb_messages WHERE id='{$msg_id}' OR thread='{$msg_id}'");

        $msg_ids = $kunena_db->loadResultArray();
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

        while ($msg_cat)
        {
            $kunena_db->setQuery("SELECT id, time FROM #__fb_messages WHERE catid='{$msg_cat}' AND (thread!='{$msg_id}' AND id!='{$msg_id}') ORDER BY time DESC LIMIT 1;");
            $lastMsgInCat = $kunena_db->loadObject();
            	check_dberror("Unable to load messages.");

            $ctg[$msg_cat]->numTopics = (int) ($ctg[$msg_cat]->numTopics - $cntTopics);
            $ctg[$msg_cat]->numPosts = (int) ($ctg[$msg_cat]->numPosts - $cntPosts);

            $ctg[$msg_cat]->id_last_msg = $lastMsgInCat->id;
            $ctg[$msg_cat]->time_last_msg = $lastMsgInCat->time;

            $msg_cat = $ctg[$msg_cat]->parent;
		}

        // now back to db
        foreach ($ctg as $cc)
        {
            $kunena_db->setQuery("UPDATE `#__fb_categories` SET `time_last_msg`='" . $cc->time_last_msg . "',`id_last_msg`='" . $cc->id_last_msg . "',`numTopics`='" . $cc->numTopics . "',`numPosts`='" . $cc->numPosts . "' WHERE `id`='" . $cc->id . "' ");
            $kunena_db->query();
            	check_dberror("Unable to update categories.");
            }

        return;
        }

	function markTopicRead($thread, $userid) {
		$thread = intval ( $thread );
		$userid = intval ( $userid );
		if (! $userid || ! $thread)
			return;

		$kunena_db = &JFactory::getDBO ();
		$kunena_session = CKunenaSession::getInstance ();

		$readTopics = explode ( ',', $kunena_session->readtopics );
		if (! in_array ( $thread, $readTopics )) {
			$readTopics[] = $thread;
			$readTopics = implode ( ',', $readTopics );
		} else {
			$readTopics = 0;
		}

		if ($readTopics) {
			$kunena_db->setQuery ( "UPDATE #__fb_sessions SET readtopics='{$readTopics}' WHERE userid='{$userid}'" );
			$kunena_db->query ();
			check_dberror ( "Unable to update session." );
		}
	}

	function forumSelectList($name, $catid=0, $options=array(), $attr='', $sections=false) {
		$kunena_db = &JFactory::getDBO ();
		$list = JJ_categoryArray ();

		foreach ( $list as $item ) {
			$options [] = JHTML::_ ( 'select.option', $item->id, $item->treename, 'value', 'text', !$sections && $item->section);
		}

		if (is_array($catid)) $catids = 'catids[]';
		else $catids = 'catid';
		$parent = JHTML::_ ( 'select.genericlist', $options, $catids, $attr, 'value', 'text', $catid, $name );
		return $parent;
	}

	function showBulkActionCats($disabled = 1) {
        $kunena_db = &JFactory::getDBO();

        $options = array ();
        $options[] = JHTML::_('select.option', '0', "&nbsp;");
        $attr = 'class="inputbox fbs" size="1"' . ($disabled ? ' disabled="disabled" ' : "");
        $lists['parent'] = CKunenaTools::forumSelectList('bulkactions', 0, $options, $attr);

        echo $lists['parent'];
        }

    function fbDeletePosts($isMod, $return) {
    	$kunena_app =& JFactory::getApplication();
        $kunena_my = &JFactory::getUser();
		$kunena_db = &JFactory::getDBO();

        if (!CKunenaTools::isAdmin() && !$isMod) {
            $kunena_app->redirect($return, JText::_('COM_KUNENA_POST_NOT_MODERATOR'));
            }

        $items = fbGetArrayInts("kDelete");
        $dellattach = 1;

        // start iterating here
        foreach ($items as $id => $value) {
            $kunena_db->setQuery("SELECT a.id, b.id AS poll_exist, catid, parent, thread, subject, userid FROM #__fb_messages AS a
            					JOIN #__fb_polls AS b ON a.id=b.threadid WHERE a.id='{$id}'");
            $mes = $kunena_db->loadObject();
            check_dberror ( "Unable to load online message info." );
            if (!$mes) return -2;
            $thread = $mes->thread;

            if($mes->poll_exist) {
            	//remove of poll
            	CKunenaPolls::delete_poll($id);
            }

            if ($mes->parent == 0) {
                // this is the forum topic; if removed, all children must be removed as well.
                $children = array ();
                $userids = array ();
                $kunena_db->setQuery("SELECT userid, id, catid FROM #__fb_messages WHERE thread='{$id}' OR id='{$id}'");
				$msguserlist = $kunena_db->loadObjectList();
				check_dberror ( "Unable to load user list." );
                foreach ($msguserlist as $line) {
                    $children[] = $line->id;

                    if ($line->userid > 0) {
                        $userids[] = $line->userid;
                        }
                    }

                $children = implode(',', $children);
                }
            else {
                //this is not the forum topic, so delete it and promote the direct children one level up in the hierarchy
                $kunena_db->setQuery('UPDATE #__fb_messages SET parent=\'' . $mes->parent . '\' WHERE parent=\'' . $id . '\'');
                $kunena_db->query();
                check_dberror ( "Unable to update messages." );

                $children = $id;
                $userids = $mes->userid > 0 ? $mes->userid : '';
                }

            //Delete the post (and it's children when it's the first post)
            $kunena_db->setQuery('UPDATE #__fb_messages SET hold=2 WHERE id=' . $id . ' OR thread=' . $id);
			$kunena_db->query();
			check_dberror ( "Unable to delete messages." );

            // now update stats
            CKunenaTools::decreaseCategoryStats($id, $mes->catid);

            //Delete (possible) ghost post
            $kunena_db->setQuery("SELECT mesid FROM #__fb_messages_text WHERE message='catid={$mes->catid}&amp;id={$id}'");
            $int_ghost_id = $kunena_db->loadResult();
            check_dberror ( "Unable to load ghost post." );

            if ($int_ghost_id > 0) {
                $kunena_db->setQuery('UPDATE #__fb_messages SET hold=2 WHERE id=' . $int_ghost_id);
                $kunena_db->query();
                check_dberror ( "Unable to delete ghost message." );
                }

            } //end foreach
            CKunenaTools::reCountBoards();

            $kunena_app->redirect($return, JText::_('COM_KUNENA_BULKMSG_DELETED'));
        }

    function isAdmin($uid = null) {
		static $instances = null;

		if ($uid === 0) return false;
		if ($uid === null){
			$user =& JFactory::getUser();
			$uid = $user->id;
		}
		if (is_object($uid)){
			$uid = $uid->id;
		}

    	if (!$instances) {
			$kunena_db = &JFactory::getDBO();
			$kunena_db->setQuery ("SELECT u.id AS uid FROM #__users AS u"
				." WHERE u.block='0' "
				." AND u.usertype IN ('Administrator', 'Super Administrator')");
			$instances = $kunena_db->loadResultArray();
			check_dberror("Unable to load administrators.");
		}

		if (in_array($uid, $instances)) return true;
		return false;
    }

	function isModerator($uid, $catid=0) {
		static $instances = null;

		$uid = (int)$uid;
		$catid = (int)$catid;
		if ($uid == 0) return false; // Anonymous never has moderator permission
		if (self::isAdmin($uid)) return true;
		if (!$instances) {
			$kunena_db = &JFactory::getDBO();
			$kunena_db->setQuery ("SELECT u.id AS uid, m.catid FROM #__users AS u"
				." LEFT JOIN #__fb_users AS p ON u.id=p.userid"
				." LEFT JOIN #__fb_moderation AS m ON u.id=m.userid"
				." LEFT JOIN #__fb_categories AS c ON m.catid=c.id"
				." WHERE u.block='0' AND p.moderator='1' AND (m.catid IS NULL OR c.moderated='1')");
			$list = $kunena_db->loadObjectList();
			check_dberror("Unable to load moderators.");
			foreach ($list as $item) $instances[$item->uid][] = $item->catid;
		}

		if (isset($instances[$uid])) {
			// Is user a global moderator?
			if (in_array(null, $instances[$uid], true)) return true;
			// Is user moderator in any category?
			if (!$catid && count($instances[$uid])) return true;
			// Is user moderator in the category?
			if ($catid && in_array($catid, $instances[$uid])) return true;
		}
		return false;
	}

	function getRank($profile) {
		static $ranks = null;

		if ($ranks === null) {
			$kunena_db = &JFactory::getDBO();
			$kunena_db->setQuery ( "SELECT * FROM #__fb_ranks" );
			$ranks = $kunena_db->loadObjectList ('rank_id');
			check_dberror ( "Unable to load ranks." );
		}

		$rank = new stdClass();
		$rank->rank_id = false;
		$rank->rank_title = JText::_('COM_KUNENA_VIEW_USER');
		$rank->rank_min = 0;
		$rank->rank_special = 0;
		$rank->rank_image = 'rank6.gif';

		if ($profile->rank != '0' && isset($ranks[$profile->rank])) {
			$rank = $ranks[$profile->rank];
		}
		else if ($profile->rank == '0' && self::isAdmin($profile->userid)) {
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_ADMINISTRATOR');
			$rank->rank_special = 1;
			$rank->rank_image = 'rankadmin.gif';
			jimport ('joomla.filesystem.file');
			foreach ($ranks as $cur) {
				if ($cur->rank_special == 1 && JFile::stripExt($cur->rank_image) == 'rankadmin') {
					$rank = $cur;
					break;
				}
			}
		}
		else if ($profile->rank == '0' && self::isModerator($profile->userid)) {
			$rank->rank_id = 0;
			$rank->rank_title = JText::_('COM_KUNENA_RANK_MODERATOR');
			$rank->rank_special = 1;
			$rank->rank_image = 'rankmod.gif';
			jimport ('joomla.filesystem.file');
			foreach ($ranks as $cur) {
				if ($cur->rank_special == 1 && JFile::stripExt($cur->rank_image) == 'rankadmin') {
					$rank = $cur;
					break;
				}
			}
		}
		if ($rank->rank_id === false) {
			//post count rank
			$rank->rank_id = 0;
			foreach ($ranks as $cur) {
				if ($cur->rank_special == 0 && $cur->rank_min <= $profile->posts && $cur->rank_min >= $rank->rank_min) {
					$rank = $cur;
				}
			}
		}
		$rank->rank_image = KUNENA_URLRANKSPATH . $rank->rank_image;
		return $rank;
	}

	function getEMailToList($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$catid = intval ( $catid );
		$thread = intval ( $thread );
		if (! $catid || ! $thread)
			return array();

		// Make sure that category exists and fetch access info
		$kunena_db = &JFactory::getDBO ();
		$query = "SELECT pub_access, pub_recurse, admin_access, admin_recurse FROM #__fb_categories WHERE id={$catid}";
		$kunena_db->setQuery ($query);
		$access = $kunena_db->loadObject ();
		check_dberror ( "Unable to load category access rights." );
		if (!$access) return array();

		$arogroups = '';
		if ($subscriptions) {
			// Get all allowed Joomla groups to make sure that subscription is valid
			$kunena_acl = &JFactory::getACL ();
			$public = array ();
			$admin = array ();
			if ($access->pub_access > 0) {
				if ($access->pub_recurse) {
					$public = $kunena_acl->get_group_children ( $access->pub_access, 'ARO', 'RECURSE' );
				}
				$public [] = $access->pub_access;
			}
			if ($access->admin_access > 0) {
				if ($access->admin_recurse) {
					$admin = $kunena_acl->get_group_children ( $access->admin_access, 'ARO', 'RECURSE' );
				}
				$admin [] = $access->admin_access;
			}
			$arogroups = implode ( ',', array_unique ( array_merge ( $public, $admin ) ) );
			if ($arogroups)
				$arogroups = "u.gid IN ({$arogroups})";
		}

		$querysel = "SELECT u.id, u.name, u.username, u.email,
					MAX((s.thread IS NOT NULL) OR (sc.catid IS NOT NULL)) as subscription,
					MAX(p.moderator='1' AND (m.catid IS NULL OR (c.moderated='1' AND m.catid=$catid))) as moderator,
					MAX(u.gid IN (24, 25)) AS admin FROM #__users AS u
					LEFT JOIN #__fb_users AS p ON u.id=p.userid
					LEFT JOIN #__fb_moderation AS m ON u.id=m.userid
					LEFT JOIN #__fb_categories AS c ON m.catid=c.id
					LEFT JOIN #__fb_subscriptions AS s ON u.id=s.userid AND s.thread=$thread
					LEFT JOIN #__fb_subscriptions_categories AS sc ON u.id=sc.userid AND sc.catid=$catid";

		$where = array ();
		$having = '';
		if ($subscriptions){
			if ($arogroups)
				$where [] = "$arogroups";
			$having = "HAVING subscription > 0";
		}
		if ($moderators)
			$where [] = " ( p.moderator=1 AND ( m.catid IS NULL OR ( c.moderated=1 AND m.catid=$catid ) ) ) ";
		if ($admins)
			$where [] = " ( u.gid IN (24, 25) ) ";

		$subsList = array ();
		if (count ( $where )) {
			$query = $querysel . " WHERE u.block=0 AND u.id NOT IN ($excludeList)
									AND (" . implode ( ' OR ', $where ) . ")
									GROUP BY u.id
									$having";
			$kunena_db->setQuery ( $query );
			$subsList = $kunena_db->loadObjectList ();
			check_dberror ( "Unable to load email list." );
		}
		return $subsList;
	}

    function fbMovePosts($catid, $isMod, $return) {
    	$kunena_app =& JFactory::getApplication();
        $kunena_db = &JFactory::getDBO();
		$kunena_my = &JFactory::getUser();

	// $isMod if user is moderator in the current category
	if (!$isMod) {
		// Test also if user is a moderator in some other category
		$kunena_db->setQuery("SELECT userid FROM #__fb_moderation WHERE userid='{$kunena_my->id}'");
		$isMod = $kunena_db->loadResult();
		check_dberror("Unable to load moderation info.");
	}
	$isAdmin = CKunenaTools::isAdmin();

        //isMod will stay until better group management comes in
        if (!$isAdmin && !$isMod) {
            $kunena_app->redirect($return, JText::_('COM_KUNENA_POST_NOT_MODERATOR'));
            }

		$catid = (int)$catid;
		if ($catid > 0) {
	        $items = fbGetArrayInts("kDelete");

	        // start iterating here

	        foreach ($items as $id => $value) {
	            $id = (int)$id;

	            $kunena_db->setQuery("SELECT subject, catid, time AS timestamp FROM #__fb_messages WHERE id='{$id}'");
	            $oldRecord = $kunena_db->loadObjectList();
	            	check_dberror("Unable to load message detail.");

		    if (CKunenaTools::isModerator($kunena_my->id, $oldRecord[0]->catid)) {

		        $newSubject = JText::_('COM_KUNENA_MOVED_TOPIC') . " " . $oldRecord[0]->subject;
		        $kunena_db->setQuery("SELECT MAX(time) AS timestamp FROM #__fb_messages WHERE thread='{$id}'");
		        $lastTimestamp = $kunena_db->loadResult();
			check_dberror("Unable to load messages max(time).");

			if ($lastTimestamp == "") {
				$lastTimestamp = $oldRecord[0]->timestamp;
                	}

			//perform the actual move
			$kunena_db->setQuery("UPDATE #__fb_messages SET `catid`='$catid' WHERE `id`='$id' OR `thread`='$id'");
			$kunena_db->query();
			check_dberror("Unable to move thread.");

			$err = JText::_('COM_KUNENA_POST_SUCCESS_MOVE');
		    } else {
                        $err = JText::_('COM_KUNENA_POST_NOT_MODERATOR');
                    }
		} //end foreach
		} else {
			$err = JText::_('COM_KUNENA_POST_NO_DEST_CATEGORY');
		}
        CKunenaTools::reCountBoards();

        $kunena_app->redirect($return, $err);
        }

	function &prepareContent(&$content)
	{
		$kunena_config =& CKunenaConfig::getInstance();

		// Joomla Mambot Support, Thanks hacksider
		if ($kunena_config->jmambot)
		{
			$row = new stdClass();
			$row->text =& $content;
			$params = new JParameter( '' );
			$dispatcher	=& JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onPrepareContent', array (&$row, &$params, 0));
			$content =& $row->text;
		}
		return $content;
	}

	function getAllowedForums($uid, $unused = 0, $unused2 = 0) {
		$kunena_acl = &JFactory::getACL();
		$kunena_db = &JFactory::getDBO();

        if ($uid != 0)
        {
        	$aro_group = $kunena_acl->getAroGroup($uid);
			$gid = $aro_group->id;
		} else {
			$gid = 0;
		}

			function _has_rights(&$kunena_acl, $gid, $access, $recurse) {
				if ($gid == $access) return 1;
				if ($recurse) {
					$childs = $kunena_acl->get_group_children($access, 'ARO', 'RECURSE');
					return (is_array($childs) and in_array($gid, $childs));
				}
				return 0;
			}

			$catlist = '';
			$query = "SELECT c.id, c.pub_access, c.pub_recurse, c.admin_access, c.admin_recurse, c.moderated"
				. ",(m.userid IS NOT NULL) AS ismod FROM #__fb_categories c"
				. " LEFT JOIN #__fb_moderation m ON c.id=m.catid AND m.userid='{$uid}' WHERE published='1'";
			$kunena_db->setQuery($query);
			$rows = $kunena_db->loadObjectList();
					check_dberror("Unable to load category list.");
			if ($rows) {
				foreach($rows as $row) {
					if (($gid == 24 || $gid == 25) or
						($row->moderated and $row->ismod) or
						($row->pub_access == 0) or
						($row->pub_access == -1 and $uid > 0) or
						($row->pub_access > 0 and _has_rights($kunena_acl, $gid, $row->pub_access, $row->pub_recurse)) or
						($row->admin_access > 0 and _has_rights($kunena_acl, $gid, $row->admin_access, $row->admin_recurse))
					) $catlist .= (($catlist == '')?'':',').$row->id;
				}
			}
			return $catlist;
		}

		/**
		 * This function format a number to n significant digits when above
		 * 10,000. Starting at 10,0000 the out put changes to 10k, starting
		 * at 1,000,000 the output switches to 1m. Both k and m are defined
		 * in the language file. The significant digits are used to limit the
		 * number of digits displayed when in 10k or 1m mode.
		 *
		 * @param int $number 		Number to be formated
		 * @param int $precision	Significant digits for output
		 */
		function formatLargeNumber($number, $precision = 4) {
			$output = '';
			// Do we need to reduce the number of significant digits?
			if ($number >= 10000){
				// Round the number to n significant digits
				$number = round ($number, -1*(log10($number)+1) + $precision);
			}

			if ($number < 10000) {
				$output = $number;
			} elseif ($number >= 1000000) {
				$output = $number / 1000000 . JText::_('COM_KUNENA_MILLION');
			} else {
				$output = $number / 1000 . JText::_('COM_KUNENA_THOUSAND');
			}

			return $output;
		}

		/**
		 *  createMenu() does just that. It creates a Joomla menu for the main
		 *  navigation tab and publishes it in the Kunena module position kunena_menu.
		 *  In addition it check if there is a link to Kunena in any of the menus
		 *  and if not, adds a forum link in the mainmenu.
		 */
		function createMenu() {
			$kunena_db =& JFactory::getDBO();

			// First we need to get the componentid of the install Kunena component
			$query = "SELECT id FROM `#__components` WHERE `option`='com_kunena';";
			$kunena_db->setQuery ($query);
			$componentid = $kunena_db->loadResult ();
			check_dberror ( "Unable to get component id." );

			// Create new Joomla menu for Kunena
			$query = "SELECT id FROM `#__menu_types` WHERE `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			$moduleid = $kunena_db->loadResult ();
			check_dberror ( "Unable to load kunena menu." );

			// Check if it exsits, if not create it
			if (!$moduleid) {
				// Create a menu type for the Kunena menu
				$query = "REPLACE INTO `#__menu_types` (`menutype`, `title`, `description`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_TITLE') . "', 'This is the default Kunena menu. It is used as the top navigation for Kunena. It can be publish in any module position. Simply unpublish items that are not required.');";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create menu." );

				// Now get the menu id again, we need it, in order to publish the menu module
				$query = "SELECT id FROM `#__menu_types` WHERE `menutype`='kunenamenu';";
				$kunena_db->setQuery ($query);
				$moduleid = $kunena_db->loadResult ();
				check_dberror ( "Unable to load kunena menu." );
			}

			// Category Index
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=listcat' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Category Index." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_LISTCAT') . "', 'forum/listcat', 'index.php?option=com_kunena&func=listcat', 'component', 1, 0, $componentid, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Category Index." );
			}

			// Recent Topics
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=latest' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Recent Topics." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_LATEST') . "', 'forum/latest', 'index.php?option=com_kunena&func=latest', 'component', 1, 0, $componentid, 0, 5, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Recent Topics." );
			}

			// My latest
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=mylatest' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load My latest." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_MYLATEST') . "', 'forum/mylatest', 'index.php?option=com_kunena&func=mylatest', 'component', 1, 0, $componentid, 0, 9, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create My latest." );
			}

			// No Replies
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=noreplies' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load No Replies." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_NOREPLIES') . "', 'forum/noreplies', 'index.php?option=com_kunena&func=noreplies', 'component', 1, 0, $componentid, 0, 8, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create No Replies." );
			}

			// My Profile
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=profile' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load My Profile." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_PROFILE') . "', 'forum/profile', 'index.php?option=com_kunena&func=profile', 'component', 1, 0, $componentid, 0, 10, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create My Profile." );
			}

			// Rules
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=rules' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Rules." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_RULES') . "', 'forum/rules', 'index.php?option=com_kunena&func=rules', 'component', 1, 0, $componentid, 0, 11, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Rules." );
			}

			// Help
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=help' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Help." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_HELP') . "', 'forum/help', 'index.php?option=com_kunena&func=help', 'component', 1, 0, $componentid, 0, 12, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Help." );
			}

			// Search
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=search' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Search." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_SEARCH') . "', 'forum/search', 'index.php?option=com_kunena&func=search', 'component', 1, 0, $componentid, 0, 13, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Search." );
			}

			// Welcome
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=showcat&catid=2' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Welcome." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_WELCOME') . "', 'forum/welcome', 'index.php?option=com_kunena&func=showcat&catid=2', 'component', 1, 0, $componentid, 0, 6, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Welcome." );
			}

			// New Topic
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=post&do=reply' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load New Topic." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_NEWTOPIC') . "', 'forum/newtopic', 'index.php?option=com_kunena&func=post&do=reply', 'component', 1, 0, $componentid, 0, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create New Topic." );
			}

			$query = "SELECT id FROM `#__modules` WHERE `position`='kunena_menu';";
			$kunena_db->setQuery ($query);
			$moduleid = $kunena_db->loadResult ();
			check_dberror ( "Unable to load module id." );

			// Check if it exsits, if not create it
			if (!$moduleid) {
				// Create a module for the Kunena menu
				$query = "REPLACE INTO `#__modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
				('" . JText::_('COM_KUNENA_MENU_TITLE') . "', '', 0, 'kunena_menu', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=kunenamenu\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, '');";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create menu." );

				// Now get the module id again, we need it, in order to publish the menu module
				$query = "SELECT id FROM `#__modules` WHERE `position`='kunena_menu';";
				$kunena_db->setQuery ($query);
				$moduleid = $kunena_db->loadResult ();
				check_dberror ( "Unable to load menu." );

				// Now publish the module
				$query = "INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ($moduleid, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to publish menu." );
			}

			// Finally add Kunena to mainmenu if it does not exist somewhere
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena' AND `menutype`='mainmenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load main menu." );
			if (!$kunena_db->loadResult ()) {
				$query = "INSERT INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('mainmenu', '" . JText::_('COM_KUNENA_MENU_FORUM') . "', 'forum', 'index.php?option=com_kunena', 'component', 1, 0, $componentid, 0, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create item into main menu." );
			}
		}
    } // end of class

class fbForum
    extends JTable {
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
    var $allow_polls = null;
    /**
    * @param database A database connector object
    */
    function __construct( &$kunena_db )
	{
		parent::__construct( '#__fb_categories', 'id', $kunena_db );
    }

	// check for potential problems
	function check() {
		if ($this->parent) {
			if ($this->id == $this->parent):
				$this->setError(JText::_('COM_KUNENA_FORUM_SAME_ERR'));
				elseif ($this->isChild($this->parent)):
				$this->setError(JText::_('COM_KUNENA_FORUM_OWNCHILD_ERR'));
			endif;
		}
		return ($this->getError() == '');
	}

	// check if given forum is one of its own childs
	function isChild($id) {
		if ($id > 0) {
			$query = "SELECT id, parent FROM #__fb_categories";
			$this->_db->setQuery($query);
			$list = $this->_db->loadObjectList('id');
			check_dberror("Unable to access categories.");
			$recurse = array();
			while ($id) {
				if (in_array($id, $recurse)) {
					$this->setError(get_class( $this ).JText::_('COM_KUNENA_RECURSION'));
					return 0;
				}
				$recurse[] = $id;
				if (!isset($list[$id])) {
					$this->setError(get_class( $this ).JText::_('COM_KUNENA_FORUM_UNKNOWN_ERR'));
					return 0;
				}
				$id = $list[$id]->parent;
				if ($id <> 0 and $id == $this->id)
					return 1;
			};
		}
		return 0;
	}

	function store($updateNulls=false)
	{
		$ret = parent::store($updateNulls);

		if ($ret) {
			// we must reset fbSession (allowed), when forum record was changed

			$this->_db->setQuery("UPDATE #__fb_sessions SET allowed='na'");
			$this->_db->query() or check_dberror("Unable to update sessions.");
		}
		return $ret;
	}

}

function JJ_categoryArray($admin=0) {
    $kunena_db = &JFactory::getDBO();

    // get a list of the menu items
	$query = "SELECT * FROM #__fb_categories";
	if(!$admin) {
		$kunena_session =& CKunenaSession::getInstance();
		if ($kunena_session && $kunena_session->allowed != 'na') {
			$query .= " WHERE id IN ($kunena_session->allowed)";
		} else {
			$query .= " WHERE pub_access='0' AND published='1'";
		}
	}
    $query .= " ORDER BY ordering, name";
    $kunena_db->setQuery($query);
    $items = $kunena_db->loadObjectList();
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
                $txt     = kunena_htmlspecialchars(stripslashes($v->name));
            } else {
                $txt     = $pre . kunena_htmlspecialchars(stripslashes($v->name));
            }
            $pt = $v->parent;
            $list[$id] = $v;
            $list[$id]->treename = $indent . $txt;
            $list[$id]->children = count( @$children[$id] );
            $list[$id]->section = ($v->parent==0);

            $list = fbTreeRecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
        }
    }
    return $list;
}

//
//Begin Smilies mod
//
function generate_smilies() {
    $kunena_db = &JFactory::getDBO();
    $kunena_emoticons_rowset = array ();

    $inline_columns = 4;
    $inline_rows = 5;

    $kunena_db->setQuery("SELECT code, location, emoticonbar FROM #__fb_smileys ORDER BY id");
        $set = $kunena_db->loadAssocList();
        check_dberror("Unable to fetch smilies.");

        $num_smilies = 0;
        $num_iconbar = 0;

        foreach ($set as $smilies) {
            $key_exists = false;

            foreach ($kunena_emoticons_rowset as $check) //checks if the smiley (location) already exists with another code
            {
                if ($check['location'] == $smilies['location']) {
                    $key_exists = true;
                    }
                }

            if ($key_exists == false) {
                $kunena_emoticons_rowset[] = array
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

        $num_smilies = count($kunena_emoticons_rowset);

        if ($num_smilies) {
            $smilies_count = min(20, $num_smilies);
            $smilies_split_row = $inline_columns - 1;

            $s_colspan = 0;
            $row = 0;
            $col = 0;
            reset ($kunena_emoticons_rowset);

            $cur = 0;

            foreach ($kunena_emoticons_rowset as $data) {
                if ($data['emoticonbar'] == 1) {
                    $cur++;

                    if (!($cur > $inline_rows * $inline_columns)) {
                        if (!$col) {
                            echo '<tr align="center" valign="middle">' . "\n";
                            }

                        echo '<td onclick="bbfontstyle(\' '
                                 . $data['code'] . ' \',\'\')" style="cursor:pointer"><img class="btnImage" src="' . KUNENA_URLEMOTIONSPATH . $data['location'] . '" border="0" alt="' . $data['code'] . ' " title="' . $data['code'] . ' " /></td>' . "\n";

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
                echo "<tr><td class=\"moresmilies\" colspan=\"" . $inline_columns . "\" onclick=\"javascript:dE('smilie');\" style=\"cursor:pointer\"><b>" . JText::_('COM_KUNENA_EMOTICONS_MORE_SMILIES') . "</b></td></tr>";
                }
            }
        return $kunena_emoticons_rowset;
    }

function fbGetArrayInts($name) {
    $array = JRequest::getVar($name, array ( 0 ), 'post', 'array');
    foreach ($array as $item=>$value) {
        if ((int)$item && (int)$item>0) $items[(int)$item] = 1;
    }
    $array = $items;

    if (!is_array($array)) {
        $array = array ( 0 );
    }

    return $array;
}

function fbReturnDashed (&$string, $key) {
            $string = "_".$string."_";
}

function kunena_htmlspecialchars($string, $quote_style=ENT_COMPAT, $charset='UTF-8') {
	return htmlspecialchars($string, $quote_style, $charset);
}

function utf8_urldecode($str) {
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x1;",urldecode($str));
	return html_entity_decode_utf8($str);
}

function html_entity_decode_utf8($string)
{
    static $trans_tbl = NULL;

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

?>
