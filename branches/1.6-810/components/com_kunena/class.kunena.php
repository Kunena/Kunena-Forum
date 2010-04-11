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

/*       _\|/_
         (o o)
 +----oOO-{_}-OOo--------------------------------+
 | Now we have the components Itemids everywhere |
 | Please use these constants where ever needed  |
 +----------------------------------------------*/

// Kunena live url
define('KUNENA_LIVEURL', KUNENA_JLIVEURL . 'index.php?option=com_kunena');
define('KUNENA_LIVEURLREL', 'index.php?option=com_kunena');

// Kunena souces absolute path
define('KUNENA_DIRECTURL', KUNENA_JLIVEURL . 'components/com_kunena/');

if (!defined("KUNENA_JCSSURL")) {
    $current_stylesheet = $kunena_app->getTemplate();
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

kimport('html.parser');

class CKunenaTools {
    var $id = null;

	function checkDatabaseError() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			if (CKunenaTools::isAdmin ()) {
				$app->enqueueMessage ( 'Kunena '.JText::sprintf ( 'COM_KUNENA_INTERNAL_ERROR_ADMIN', '<a href="http:://www.kunena.com/">ww.kunena.com</a>' ), 'error' );
			} else {
				$app->enqueueMessage ( 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' ), 'error' );
			}
			return true;
		}
		return false;
	}

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

	function parseText($txt) {
		user_error(__CLASS__.'::'.__FUNCTION__.'(): Deprecated', E_USER_NOTICE);
		if (!$txt) return;
		$txt = nl2br ( $txt );
		$txt = kunena_htmlspecialchars ( $txt );
		$txt = CKunenaTools::prepareContent ( $txt );
		return $txt;
	}

	function parseBBCode($txt) {
		user_error(__CLASS__.'::'.__FUNCTION__.'(): Deprecated', E_USER_NOTICE);
		static $emoticons = null;

		if (!$txt) return;
		if (!$emoticons) $emoticons = smile::getEmoticons ( 0 );
		$kunena_config = & CKunenaConfig::getInstance ();
		$txt = smile::smileReplace ( $txt, 0, $kunena_config->disemoticons, $emoticons );
		$txt = nl2br ( $txt );
		$txt = str_replace ( "__FBTAB__", "&#009;", $txt ); // For [code]
		$txt = CKunenaTools::prepareContent ( $txt );
		return $txt;
	}

	function stripBBCode($txt, $len=0) {
		user_error(__CLASS__.'::'.__FUNCTION__.'(): Deprecated', E_USER_NOTICE);
		static $emoticons = null;

		if (!$txt) return;
		if (!$emoticons) $emoticons = smile::getEmoticons ( 0 );
		$kunena_config = & CKunenaConfig::getInstance ();
		$txt = smile::purify ( $txt );
		if ($len) $txt = JString::substr ( $txt, 0, $len );
		$txt = kunena_htmlspecialchars ( $txt );
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

    // FIXME: broken function, bad implementation
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
		$kunena_session = KunenaFactory::getSession ();

		$readTopics = explode ( ',', $kunena_session->readtopics );
		if (! in_array ( $thread, $readTopics )) {
			$readTopics[] = $thread;
			$readTopics = implode ( ',', $readTopics );
		} else {
			$readTopics = false; // do not update session
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

	function KSelectList($name, $options=array(), $attr='', $sections=false) {
		$kunena_db = &JFactory::getDBO ();
		$list = JJ_categoryArray ();

		foreach ( $list as $item ) {
			$options [] = JHTML::_ ( 'select.option', $item->id, $item->treename, 'value', 'text', !$sections && $item->section);
		}

		$catsList = JHTML::_ ( 'select.genericlist', $options, $name, $attr, 'value', 'text', '', $name );
		return $catsList;
	}

	function showBulkActionCats($disabled = 1) {
        $kunena_db = &JFactory::getDBO();

        $options = array ();
        $options[] = JHTML::_('select.option', '0', "&nbsp;");
        $attr = 'class="inputbox fbs" size="1"' . ($disabled ? ' disabled="disabled" ' : "");
        $lists['parent'] = CKunenaTools::forumSelectList('bulkactions', 0, $options, $attr);

        echo $lists['parent'];
        }

	function isAdmin($uid = null) {
		$acl = KunenaFactory::getAccessControl();
		return $acl->isAdmin($uid);
	}

	function isModerator($uid=null, $catid=0) {
		$acl = KunenaFactory::getAccessControl();
		return $acl->isModerator($uid, $catid);
	}

	function getEMailToList($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$acl = KunenaFactory::getAccessControl();
		return $acl->getSubscribers($catid, $thread, $subscriptions, $moderators, $admins, $excludeList);
	}

	function KUnfavorite() {
		$kunena_app = JFactory::getApplication ();
		$kunena_db = &JFactory::getDBO ();
		$user =& JFactory::getUser();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );

		$items = KGetArrayInts ( "cb" );
		$items = implode(',',array_keys($items));

		//Need to get thread and userid related to message id
		$query="SELECT thread FROM #__fb_messages WHERE id IN ('$items')";
		$kunena_db->setQuery ( $query );
		$messList = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to message details list." );

		foreach ( $messList as $mes ) {
			CKunenaTools::removeFavorite ($mes->thread, $user->id);
		}

		$kunena_app->redirect ( $backUrl, 'Unfavorite done' );
	}

	function KUnsubscribe () {
		$kunena_app = JFactory::getApplication ();
		$kunena_db = &JFactory::getDBO ();
		$user =& JFactory::getUser();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );

		$items = KGetArrayInts ( "cb" );
		$items = implode(',',array_keys($items));

		//Need to get thread and userid related to message id
		$query="SELECT thread FROM #__fb_messages WHERE id IN ('$items')";
		$kunena_db->setQuery ( $query );
		$messList = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to message details list." );

		foreach ( $messList as $mes ) {
			CKunenaTools::removeSubscritpion ($mes->thread, $user->id);
		}

		$kunena_app->redirect ( $backUrl, 'Unsubscribe done' );
	}

	function getAllowedForums($uid) {
		$acl = KunenaFactory::getAccessControl();
		return $acl->getAllowedCategories($uid);
	}

	function KDeletePosts() {
		$kunena_app = JFactory::getApplication ();

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );

		$items = KGetArrayInts ( "cb" );

		// start iterating here
		$message = '';
		foreach ( $items as $id => $value ) {
			$delete = $kunena_mod->deleteThread ( $id, $DeleteAttachments = false );
			if (! $delete) {
				$kunena_app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
			} else {
				$message = JText::_ ( 'COM_KUNENA_BULKMSG_DELETED' );
			}

		} //end foreach

		$kunena_app->redirect ( $backUrl, $message );
	}

	function KMovePosts($catid) {
		$catid = ( int ) $catid;

		$kunena_app = JFactory::getApplication ();

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );

		$items = KGetArrayInts ( "cb" );

		$message = '';
		// start iterating here
		foreach ( $items as $id => $value ) {
			$move = $kunena_mod->moveThread ( $id, $catid, $DeleteAttachments = false );
			if (! $move) {
				$kunena_app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
			} else {
				$message = JText::_ ( 'COM_KUNENA_POST_SUCCESS_MOVE' );
			}
		} //end foreach

		$kunena_app->redirect ( $backUrl, $message );
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

		/**
		 * This function formats a number to n significant digits when above
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
		 * This function shortens long filenames for display purposes.
		 * The first 8 characters of the filename, followed by three dots
		 * and the last 5 character of the filename.
		 *
		 * @param char $filename 	Filename to be shortened if too long
		 */
		function shortenFileName($filename, $front=10, $back=8, $filler='...') {
			$len = strlen($filename);
			if ($len>($front+strlen($filler)+$back)){
				$output=substr($filename,0,$front).$filler.substr($filename,$len-$back,$back);
			}else{
				$output=$filename;
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

			// Forum
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			$parentid = $kunena_db->loadResult ();
			check_dberror ( "Unable to load Category Index." );
			if (!$parentid) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_LISTCAT') . "', 'forum', 'index.php?option=com_kunena', 'component', 1, 0, $componentid, 0, 4, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Category Index." );
				$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena' AND `menutype`='kunenamenu';";
				$kunena_db->setQuery ($query);
				$parentid = $kunena_db->loadResult ();
				check_dberror ( "Unable to load Category Index." );
			}

			// Category Index
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=listcat' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Category Index." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_LISTCAT') . "', 'listcat', 'index.php?option=com_kunena&func=listcat', 'component', 1, $parentid, $componentid, 1, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
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
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_LATEST') . "', 'latest', 'index.php?option=com_kunena&func=latest', 'component', 1, $parentid, $componentid, 1, 1, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Recent Topics." );
			}

			// Welcome
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=showcat&catid=2' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load Welcome." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_WELCOME') . "', 'welcome', 'index.php?option=com_kunena&func=showcat&catid=2', 'component', 1, $parentid, $componentid, 1, 2, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Welcome." );
			}

			// New Topic
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=post&do=new' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load New Topic." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_NEWTOPIC') . "', 'newtopic', 'index.php?option=com_kunena&func=post&do=new', 'component', 1, $parentid, $componentid, 1, 3, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create New Topic." );
			}

			// No Replies
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=noreplies' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load No Replies." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_NOREPLIES') . "', 'noreplies', 'index.php?option=com_kunena&func=noreplies', 'component', 1, $parentid, $componentid, 1, 4, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create No Replies." );
			}

			// My latest
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=mylatest' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load My latest." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_MYLATEST') . "', 'mylatest', 'index.php?option=com_kunena&func=mylatest', 'component', 1, $parentid, $componentid, 1, 5, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create My latest." );
			}

			// My Profile
			$query = "SELECT id FROM `#__menu` WHERE `link`='index.php?option=com_kunena&func=profile' AND `menutype`='kunenamenu';";
			$kunena_db->setQuery ($query);
			check_dberror ( "Unable to load My Profile." );
			if (!$kunena_db->loadResult ()) {
				$query = "REPLACE INTO `#__menu` (`menutype`, `name`, `alias`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`, `lft`, `rgt`, `home`) VALUES
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_PROFILE') . "', 'profile', 'index.php?option=com_kunena&func=profile', 'component', 1, $parentid, $componentid, 1, 6, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
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
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_RULES') . "', 'rules', 'index.php?option=com_kunena&func=rules', 'component', 1, $parentid, $componentid, 1, 7, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
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
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_HELP') . "', 'help', 'index.php?option=com_kunena&func=help', 'component', 1, $parentid, $componentid, 1, 8, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
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
							('kunenamenu', '" . JText::_('COM_KUNENA_MENU_SEARCH') . "', 'search', 'index.php?option=com_kunena&func=search', 'component', 1, $parentid, $componentid, 1, 9, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 'menu_image=-1\r\n\r\n', 0, 0, 0);";
				$kunena_db->setQuery ($query);
				$kunena_db->query ();
				check_dberror ( "Unable to create Search." );
			}

			$query = "SELECT id FROM `#__modules` WHERE `position`='kunena_menu';";
			$kunena_db->setQuery ($query);
			$moduleid = $kunena_db->loadResult ();
			check_dberror ( "Unable to load module id." );

			// Check if it exsits, if not create it
			if (!$moduleid) {
				// Create a module for the Kunena menu
				$query = "REPLACE INTO `#__modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `numnews`, `access`, `showtitle`, `params`, `iscore`, `client_id`, `control`) VALUES
				('" . JText::_('COM_KUNENA_MENU_TITLE') . "', '', 0, 'kunena_menu', 0, '0000-00-00 00:00:00', 1, 'mod_mainmenu', 0, 0, 0, 'menutype=kunenamenu\nmenu_style=list\nstartLevel=1\nendLevel=2\nshowAllChildren=1\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0, 0, '');";
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

		/**
		 * This function loads the appropriate template file
		 * It checks if the selected template contains an override
		 * and if so loads it. Fall back is the default template
		 * implementation
		 *
		 * @param string 	$relpath	Relative path to template file
		 * @param bool 		$once		limit to single include default false
		 * @param string 	$template	Custom path to template (relative to Joomla)
		 */
		function loadTemplate($relpath, $once=false, $template=null) {
			if ($once){
				if ($template && file_exists ( JPATH_ROOT.$template.$relpath )) {
					require_once (JPATH_ROOT.$template.$relpath);
				} else if (file_exists ( KUNENA_ABSTMPLTPATH.$relpath )) {
					require_once (KUNENA_ABSTMPLTPATH.$relpath);
				} else {
					require_once (KUNENA_PATH_TEMPLATE_DEFAULT.$relpath);
				}
			} else {
				if ($template && file_exists ( JPATH_ROOT.$template.$relpath )) {
					require (JPATH_ROOT.$template.$relpath);
				} else if (file_exists ( KUNENA_ABSTMPLTPATH.$relpath )) {
					require (KUNENA_ABSTMPLTPATH.$relpath);
				} else {
					require (KUNENA_PATH_TEMPLATE_DEFAULT.$relpath);
				}
			}
		}

		/**
		 * This function check the edit time for the author of the author
		 * of the post and return if the user is allwoed or not to edit
		 * her post
		 *
		 * @param timestamp $messagemodifiedtime	Time when the message has been edited
		 * @param timestamp $messagetime			Actual message time
		 */
		function editTimeCheck ($messagemodifiedtime, $messagetime) {
			$kunena_config = & CKunenaConfig::getInstance ();
			if (intval($kunena_config->useredit) != 1) return false;
			if (intval($kunena_config->useredittime) == 0) {
					return true;
			} else {
				//Check whether edit is in time
				$modtime = $messagemodifiedtime;
				if (! $modtime) {
					$modtime = $messagetime;
				}
				if ($modtime + intval($kunena_config->useredittime) >= CKunenaTimeformat::internalTime ()) {
					return true;
				}
			}
		}

			/**
		 * This function allows normal user to delete their own posts, if there aren't replies
		 * after that
		 *
		 * @param int $MessageID	The id of the message to deleted
		 */
		function userOwnDelete ($MessageID) {
			$kunena_db =& JFactory::getDBO();
			$user =& JFactory::getUser();

			// Sanitize parameters!
			$MessageID = intval ( $MessageID );
			// no need to check $DeleteAttachments as we only test for true

			$kunena_db->setQuery ( "SELECT `id`,  `parent`, `userid`, `thread`, `subject`, `time` AS timestamp FROM #__fb_messages WHERE `id`='$MessageID'" );
			$currentMessage = $kunena_db->loadObject ();
			check_dberror ( "Unable to load message." );

			// Check that message to be deleted actually exists
			if ( !is_object($currentMessage) ) {
				return false;
			}

			//need to check that the user is right the author of the post
			if ($currentMessage->userid != $user->id ) {
				return false;
			}

			//need to check that the message is the last of the thread
			$kunena_db->setQuery ( "SELECT MAX(id) AS lastthreadid FROM #__fb_messages WHERE `hold`='0' AND `thread`='$currentMessage->thread'" );
			$lastMessage = $kunena_db->loadObject ();
			check_dberror ( "Unable to load message." );

			if ($currentMessage->id != $lastMessage->lastthreadid ) {
				return false;
			}

			// Execute delete
			$sql = "UPDATE #__fb_messages SET `hold`=2 WHERE `id`='$MessageID';";
			$kunena_db->setQuery ( $sql );
			$kunena_db->query ();
			check_dberror ( 'Unable to perform delete.' );

			// Last but not least update forum stats
			CKunenaTools::reCountBoards();

			return true;
		}

		/**
		 * This function manage user moderation from frontend profile
		 *
		 */
		function KModerateUser () {
			$kunena_db =& JFactory::getDBO();
			$kunena_app = JFactory::getApplication ();

			$thisuserid = JRequest::getInt ( 'thisuserid', '' );
			$banIP = JRequest::getVar ( 'prof_ip_select', '' );
			$banEmail = JRequest::getVar ( 'banemail', '' );
			$banUsername = JRequest::getVar ( 'banusername', '' );
			$banDelPosts = JRequest::getVar ( 'bandelposts', '' );
			$DelAvatar = JRequest::getVar ( 'delavatar', '' );
			$DelSignature = JRequest::getVar ( 'delsignature', '' );
			$DelProfileInfo = JRequest::getVar ( 'delprofileinfo', '' );

			if ( isset($banIP) ) {
				//future feature
			}

			if ( isset($banEmail) ) {
				//future feature
			}

			if ( isset($banUsername) ) {
				//future feature
			}

			if ( isset ($DelAvatar) ) {
				jimport('joomla.filesystem.file');
				$userprofile = KunenaFactory::getUser($thisuserid);

				$kunena_db->setQuery ( "UPDATE #__fb_users SET avatar=null WHERE userid=$thisuserid" );
				$kunena_db->Query ();
				check_dberror ( "Unable to remove user avatar." );

				// Delete avatar from file system
				if ( JFile::exists(KUNENA_PATH_AVATAR_UPLOADED.DS.$userprofile->avatar) ) {
					JFile::delete(KUNENA_PATH_AVATAR_UPLOADED.DS.$userprofile->avatar);
				}

				$kunena_app->redirect ( CKunenaLink::GetProfileURL($thisuserid) );
			}

			if ( isset ($DelSignature) ) {
				$kunena_db->setQuery ( "UPDATE #__fb_users SET signature=null WHERE userid=$thisuserid" );
				$kunena_db->Query ();
				check_dberror ( "Unable to remove user singature." );

				$kunena_app->redirect ( CKunenaLink::GetProfileURL($thisuserid) );
			}

			if ( isset ($DelProfileInfo) ) {
				$kunena_db->setQuery ( "UPDATE #__fb_users SET signature=null,avatar=null,karma=null,personalText=null,gender=0,birthdate=0000-00-00,location=null,ICQ=null,AIM=null,YIM=null,MSN=null,SKYPE=null,GTALK=null,websitename=null,websiteurl=null,rank=0,TWITTER=null,FACEBOOK=null,MYSPACE=null,LINKEDIN=null,DELICIOUS=null,FRIENDFEED=null,DIGG=null,BLOGSPOT=null,FLICKR=null,BEBO=null WHERE userid=$thisuserid" );
				$kunena_db->Query ();
				check_dberror ( "Unable to remove user profile information." );

				$kunena_app->redirect ( CKunenaLink::GetProfileURL($thisuserid) );
			}

			if ( isset($banDelPosts) ) {
				$path = KUNENA_PATH_LIB.'/kunena.moderation.class.php';
				require_once ($path);
				$kunena_mod = CKunenaModeration::getInstance();

				if ($thisuserid) {
					//select only the messages which aren't already in the trash
					$kunena_db->setQuery ( "SELECT id FROM #__fb_messages WHERE hold!=2 AND userid=$thisuserid" );
					$idusermessages = $kunena_db->loadObjectList ();
					check_dberror ( "Unable to load message id from fb_messages." );

					$kunena_mod->deleteMessage($thisuserid, $DeleteAttachments = false);
				}

				$kunena_app->redirect ( CKunenaLink::GetProfileURL($thisuserid) );
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
    var $allow_anonymous = null;
    var $post_anonymous = null;
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
		$kunena_session =& KunenaFactory::getSession();
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
                $txt     = kunena_htmlspecialchars($v->name);
            } else {
                $txt     = $pre . kunena_htmlspecialchars($v->name);
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

function KGetArrayInts($name) {
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

function KGetArrayReverseInts($name) {
    $array = JRequest::getVar($name, array ( 0 ), 'post', 'array');

    foreach ($array as $item=>$value) {
        if ((int)$item && (int)$item>0) $items[(int)$item] = (int)$item;
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
