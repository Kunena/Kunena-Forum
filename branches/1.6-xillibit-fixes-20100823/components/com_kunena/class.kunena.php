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

require_once (KPATH_SITE . '/lib/kunena.defines.php');
kimport('error');

$kunena_app =& JFactory::getApplication();
$document =& JFactory::getDocument();
$kunena_config = KunenaFactory::getConfig ();
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
    if (KunenaError::checkDatabaseError()) return;
    define('KUNENA_JCSSURL', KUNENA_JLIVEURL . 'templates/' . $current_stylesheet . '/css/template_css.css');
}

// Kunena uploaded files directory
define('KUNENA_LIVEUPLOADEDPATH_LEGACY', KUNENA_JLIVEURL . 'images/fbfiles');
define('KUNENA_LIVEUPLOADEDPATH', KUNENA_JLIVEURL . 'media/kunena');


// now continue with other paths

$fb_user_template = JRequest::getString('fb_user_template', '', 'COOKIE');
$fb_user_img_template = JRequest::getString('fb_user_img_template', '', 'COOKIE');
// don't allow directory travelling
$fb_user_template = strtr($fb_user_template, '\\/', '');
$fb_user_img_template = strtr($fb_user_img_template, '\\/', '');

if (JString::strlen($fb_user_template) > 0 && file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_user_template .DS. 'template.xml'))
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

if (JString::strlen($fb_user_img_template) > 0 && file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_user_img_template .DS. 'images'))
{
    $fb_cur_img_template = $fb_user_img_template;
    }
else if (file_exists(KUNENA_PATH_TEMPLATE .DS. $kunena_config->template .DS. 'images'))
{
    $fb_cur_img_template = $kunena_config->template;
    }
else
{
    $fb_cur_img_template = 'default';
    }

// only for preview module - maybe used later by users to change template

define('KUNENA_RELTMPLTPATH', $fb_cur_template);
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
define('KUNENA_ABSCATIMAGESPATH', KUNENA_ROOT_PATH.DS.'media'.DS.'kunena'.DS.$kunena_config->catimagepath); // Kunena category images absolute path

define('KUNENA_TMPLTURL', KUNENA_DIRECTURL . "template/{$fb_cur_template}/");
define('KUNENA_TMPLTMAINIMGURL', KUNENA_DIRECTURL . "template/{$fb_cur_img_template}/");

// IMAGES URL PATH
define('KUNENA_TMPLTCSSURL', KUNENA_TMPLTURL . 'css/kunena.forum-min.css');

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
		kimport('error');
		return KunenaError::checkDatabaseError();
	}

	function showButton($name, $text) {
		return '<span class="'.$name.'"><span>'.$text.'</span></span>';
	}

	function showIcon($name, $title='') {
		return '<span class="kicon '.$name.'" title="'.$title.'"></span>';
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

	// TODO: deprecated
	function parseText($txt) {
		user_error(__CLASS__.'::'.__FUNCTION__.'(): Deprecated', E_USER_NOTICE);
		kimport('html.parser');
		return KunenaParser::parseText($txt);
	}

	// TODO: deprecated
	function parseBBCode($txt) {
		user_error(__CLASS__.'::'.__FUNCTION__.'(): Deprecated', E_USER_NOTICE);
		kimport('html.parser');
		return KunenaParser::parseBBCode($txt);
	}

	// TODO: deprecated
	function stripBBCode($txt, $len=0) {
		user_error(__CLASS__.'::'.__FUNCTION__.'(): Deprecated', E_USER_NOTICE);
		kimport('html.parser');
		return KunenaParser::stripBBCode($txt, $len);
	}

	function reCountUserPosts() {
    	$kunena_db = &JFactory::getDBO();

        // Reset category counts as next query ignores users which have written no messages
        $kunena_db->setQuery("UPDATE #__kunena_users SET posts=0");
        $kunena_db->query();
        if (KunenaError::checkDatabaseError()) return;

          	// Update user post count (ignore unpublished categories and hidden messages)
    	$kunena_db->setQuery("INSERT INTO #__kunena_users (userid, posts)"
    		." SELECT m.userid, COUNT(m.userid) "
    		." FROM #__kunena_messages AS m"
    		." INNER JOIN #__kunena_users AS u ON u.userid = m.userid"
    		." WHERE m.hold=0 and m.catid IN (SELECT id FROM #__kunena_categories WHERE published=1)"
    		." GROUP BY m.userid"
    		." ON DUPLICATE KEY UPDATE posts=VALUES(posts)");
    	$kunena_db->query();
        KunenaError::checkDatabaseError();
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
        $kunena_db->setQuery("UPDATE #__kunena_categories SET numTopics=0, numPosts=0");
        $kunena_db->query();
        if (KunenaError::checkDatabaseError()) return;

        // Update category post count
        $kunena_db->setQuery("INSERT INTO #__kunena_categories (id, numTopics, numPosts, id_last_msg, time_last_msg)"
        	." SELECT c.id, SUM( m.parent=0 ), SUM( m.parent>0 ), MAX( m.id ), MAX( m.time )"
        	." FROM #__kunena_messages as m"
        	." INNER JOIN #__kunena_categories AS c ON c.id=m.catid"
        	." WHERE m.catid>0 AND m.hold=0"
        	." GROUP BY catid "
        	." ON DUPLICATE KEY UPDATE numTopics=VALUES(numTopics), numPosts=VALUES(numPosts), id_last_msg=VALUES(id_last_msg), time_last_msg=VALUES(time_last_msg)");
    	$kunena_db->query();
    	if (KunenaError::checkDatabaseError()) return;

    	// Load categories to be counted
        $kunena_db->setQuery("SELECT id, parent, published, numTopics, numPosts, id_last_msg, time_last_msg FROM #__kunena_categories");
        $cats = $kunena_db->loadObjectList('id');
        if (KunenaError::checkDatabaseError()) return;

        foreach ($cats as $c)
        {
            if (isset($cats[$c->parent])) $cats[$c->parent]->children[] = $c->id;
            else $cats[0]->children[] = $c->id;
        }

        if (!empty($cats)) CKunenaTools::reCountBoardsRecursion($cats, 0);

        // now back to db
        foreach ($cats as $c)
        {
        	if (!isset($c->id)) continue;
            $kunena_db->setQuery("UPDATE #__kunena_categories SET"
            	."  numTopics=" . intval($c->numTopics)
            	.", numPosts=" . intval($c->numPosts)
            	.", id_last_msg=" . intval($c->id_last_msg)
            	.", time_last_msg=" . intval($c->time_last_msg)
            	." WHERE id=" . intval($c->id));
            $kunena_db->query();
            if (KunenaError::checkDatabaseError()) return;
        }
    }

    function updateNameInfo()
    {
        $kunena_db = &JFactory::getDBO();
        $kunena_config = KunenaFactory::getConfig ();

        $fb_queryName = $kunena_config->username ? "username" : "name";

	    $query = "UPDATE #__kunena_messages AS m, #__users AS u
	    			SET m.name = u.$fb_queryName
					WHERE m.userid = u.id";
        $kunena_db->setQuery($query);
        $kunena_db->query();
        KunenaError::checkDatabaseError();
        return $kunena_db->getAffectedRows();
    }

    function modifyCategoryStats($msg_id, $msg_parent, $msg_time, $msg_cat) {
        $kunena_db = &JFactory::getDBO();
        $kunena_db->setQuery("SELECT id, parent, numTopics, numPosts, id_last_msg, time_last_msg FROM #__kunena_categories ORDER BY id ASC");
        $cats = $kunena_db->loadObjectList();
        if (KunenaError::checkDatabaseError()) return;

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
                "UPDATE `#__kunena_categories`"
                ." SET `time_last_msg`='" . $ctg[$msg_cat]->time_last_msg . "'"
                .",`id_last_msg`='" . $ctg[$msg_cat]->id_last_msg . "'"
                .",`numTopics`='" . $ctg[$msg_cat]->numTopics . "'"
                .",`numPosts`='" . $ctg[$msg_cat]->numPosts . "'"
                ." WHERE (`id`='" . $ctg[$msg_cat]->id . "') ");
            $kunena_db->query();
            if (KunenaError::checkDatabaseError()) return;

            // parent
            $msg_cat = $ctg[$msg_cat]->parent;
        }
    }

    // FIXME: broken function, bad implementation
    function decreaseCategoryStats($msg_id, $msg_cat) {
        //topic : 1 , message = 0
        $kunena_db = &JFactory::getDBO();
        $kunena_db->setQuery("SELECT id, parent, numTopics, numPosts, id_last_msg, time_last_msg FROM #__kunena_categories ORDER BY id ASC");
        $cats = $kunena_db->loadObjectList();
        if (KunenaError::checkDatabaseError()) return;

        foreach ($cats as $c) {
            $ctg[$c->id] = $c;
            }

        $kunena_db->setQuery("SELECT id FROM #__kunena_messages WHERE id={$kunena_db->Quote($msg_id)} OR thread={$kunena_db->Quote($msg_id)}");

        $msg_ids = $kunena_db->loadResultArray();
        if (KunenaError::checkDatabaseError()) return;

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
            $kunena_db->setQuery("SELECT id, time FROM #__kunena_messages WHERE catid={$kunena_db->Quote($msg_cat)} AND (thread!={$kunena_db->Quote($msg_id)} AND id!={$kunena_db->Quote($msg_id)}) ORDER BY time DESC LIMIT 1;");
            $lastMsgInCat = $kunena_db->loadObject();
            if (KunenaError::checkDatabaseError()) return;

            $ctg[$msg_cat]->numTopics = (int) ($ctg[$msg_cat]->numTopics - $cntTopics);
            $ctg[$msg_cat]->numPosts = (int) ($ctg[$msg_cat]->numPosts - $cntPosts);

            $ctg[$msg_cat]->id_last_msg = $lastMsgInCat->id;
            $ctg[$msg_cat]->time_last_msg = $lastMsgInCat->time;

            $msg_cat = $ctg[$msg_cat]->parent;
		}

        // now back to db
        foreach ($ctg as $cc)
        {
            $kunena_db->setQuery("UPDATE `#__kunena_categories` SET `time_last_msg`='" . $cc->time_last_msg . "',`id_last_msg`='" . $cc->id_last_msg . "',`numTopics`='" . $cc->numTopics . "',`numPosts`='" . $cc->numPosts . "' WHERE `id`='" . $cc->id . "' ");
            $kunena_db->query();
            if (KunenaError::checkDatabaseError()) return;
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
			$kunena_db->setQuery ( "UPDATE #__kunena_sessions SET readtopics={$kunena_db->Quote($readTopics)} WHERE userid={$kunena_db->Quote($userid)}" );
			$kunena_db->query ();
			KunenaError::checkDatabaseError();
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

	function KSelectList($name, $options=array(), $attr='', $sections=false, $id='', $selected=0) {
		$kunena_db = &JFactory::getDBO ();
		$list = JJ_categoryArray ();

		foreach ( $list as $item ) {
			$options [] = JHTML::_ ( 'select.option', $item->id, $item->treename, 'value', 'text', !$sections && $item->section);
		}

		if (!$id) $id = $name;
		$catsList = JHTML::_ ( 'select.genericlist', $options, $name, $attr, 'value', 'text', $selected, $id );
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

	// TODO: deprecated
	function getTemplateImage($image) {
		$template = KunenaFactory::getTemplate ();
		return 'components/com_kunena/' . $template->getImagePath($image, false);
	}

	// TODO: deprecated
	function topicIcon($topic) {
		$template = KunenaFactory::getTemplate ();
		return $template->getTopicIcon($topic);
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

	function getAllowedForums($uid) {
		$acl = KunenaFactory::getAccessControl();
		return $acl->getAllowedCategories($uid);
	}

	function KDeletePosts() {
		$kunena_app = JFactory::getApplication ();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );
		if (!JRequest::checkToken()) {
			$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$kunena_app->redirect ( $backUrl );
		}

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

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

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );
		if (!JRequest::checkToken()) {
			$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$kunena_app->redirect ( $backUrl );
		}

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

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

	function KDeletePerm() {
		$kunena_app = JFactory::getApplication ();
		$kunena_db = & JFactory::getDBO ();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );
		if (!JRequest::checkToken()) {
			$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$kunena_app->redirect ( $backUrl );
		}

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$items = KGetArrayInts ( "cb" );

		// start iterating here
		foreach ( $items as $id => $value ) {
			$query = "SELECT `hold` FROM #__kunena_messages WHERE `thread`={$kunena_db->quote($id)};";
			$kunena_db->setQuery ( $query );
			$messagesHold = $kunena_db->loadObjectList ();
			KunenaError::checkDatabaseError();

			foreach ( $messagesHold as $messageHold ) {
				if ($messageHold->hold == 2) {
					$delete = $kunena_mod->deleteThreadPerminantly ( $id, true );
					if (! $delete) {
						$kunena_app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
					} else {
						$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_BULKMSG_DELETED' ) );
					}
				}
			}

		} //end foreach

		//$kunena_app->redirect ( $backUrl );
	}

	function KUndelete() {
		$kunena_app = JFactory::getApplication ();
		$kunena_db = & JFactory::getDBO ();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );
		if (!JRequest::checkToken()) {
			$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$kunena_app->redirect ( $backUrl );
		}

		// Need to check if the user has moderation rights

		$items = KGetArrayInts ( "cb" );

		// start iterating here
		foreach ( $items as $id => $value  ) {
			// Need to get hold value to check if the message is right deleted
			$query = "SELECT `hold` FROM #__kunena_messages WHERE `id`={$kunena_db->quote($id)};";
			$kunena_db->setQuery ( $query );
			$messageHold = $kunena_db->loadResult ();
			KunenaError::checkDatabaseError();

			if ( $messageHold == 2 ) {
				// Execute undelete
				$query = "UPDATE #__kunena_messages SET `hold`=0 WHERE `id`={$kunena_db->quote($id)};";
				$kunena_db->setQuery ( $query );
				$kunena_db->query ();
				$dberror = 0;
				if (KunenaError::checkDatabaseError()) $dberror = KunenaError::getDatabaseError();
				if ($dberror) {
					$kunena_app->enqueueMessage( JText::_ ( 'COM_KUNENA_POST_ERROR_UNDELETE' ) , 'notice' );
				} else {
					$kunena_app->enqueueMessage( JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE') );
				}

				// Last but not least update forum stats
				CKunenaTools::reCountBoards();

				// Activity integration
				$activity = KunenaFactory::getActivityIntegration();
				$activity->onAfterUndelete($this);
			} else {
				$kunena_app->enqueueMessage( JText::_ ( 'COM_KUNENA_POST_UNDELETE_NOT_DELETED' ) , 'notice' );
			}
		} //end foreach

		//$kunena_app->redirect ( $backUrl );
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

	function displayMenu() {
		jimport ( 'joomla.application.module.helper' );
		$position = "kunena_menu";
		$options = array ('style' => 'xhtml' );
		$modules = JModuleHelper::getModules ( $position );
		foreach ( $modules as $module ) {
			if ($module->module == 'mod_mainmenu') {
				$basemenu = KunenaRoute::getMenu ();
				if ($basemenu) {
					$module = clone $module;
					// FIXME: J1.5 only
					$search = array ('/menutype=(.*)(\s)/', '/startLevel=(.*)(\s)/', '/endLevel=(.*)(\s)/' );
					$replace = array ("menutype={$basemenu->menutype}\\2", 'startLevel=' . ($basemenu->sublevel + 1) . '\2', 'endLevel=' . ($basemenu->sublevel + 2) . '\2' );
					$module->params = preg_replace ( $search, $replace, $module->params );
				}
			}
			echo JModuleHelper::renderModule ( $module, $options );
		}
	}

	function displayLoginBox() {
		require_once (KUNENA_PATH_LIB . DS . 'kunena.login.php');
		$type = CKunenaLogin::getType ();
		if ($type == 'login') {
			CKunenaTools::loadTemplate('/loginbox/login.php');
		} else {
			CKunenaTools::loadTemplate('/loginbox/logout.php');
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
			$kunena_config = KunenaFactory::getConfig ();
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
		 * This function load datas for rules or help page
		 *
		 */
		function getRulesHelpDatas($id) {
			$kunena_db = &JFactory::getDBO ();

			$kunena_db->setQuery ( "SELECT introtext, id FROM #__content WHERE id={$kunena_db->Quote($id)}" );
			$introtext = $kunena_db->loadResult ();
			KunenaError::checkDatabaseError();

			return $introtext;
		}


		/**
		 * Wrapper to addStyleSheet
		 *
		 */
		function addStyleSheet($filename) {

			$document = & JFactory::getDocument ();
			$kunena_config = KunenaFactory::getConfig ();

			if ($kunena_config->debug || Kunena::isSvn()) {
				// If we are in debug more, make sure we load the unpacked css
				$filename = preg_replace ( '/\-min\./u', '.', $filename );
			}

			return $document->addStyleSheet ( $filename );
		}

		/**
		 * Wrapper to addScript
		 *
		 */
		function addScript($filename) {

			$document = & JFactory::getDocument ();
			$kunena_config = KunenaFactory::getConfig ();

			if ($kunena_config->debug || Kunena::isSvn()) {
				// If we are in debug more, make sure we load the unpacked css
				$filename = preg_replace ( '/\-min\./u', '.', $filename );
			}

			return $document->addScript ( $filename );
		}

    } // end of class

function JJ_categoryArray($admin=0) {
    $kunena_db = &JFactory::getDBO();
    $app = JFactory::getApplication();

    // get a list of the menu items
	$query = "SELECT * FROM #__kunena_categories";
	if($app->isSite()) {
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
    KunenaError::checkDatabaseError();
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

    $kunena_db->setQuery("SELECT code, location, emoticonbar FROM #__kunena_smileys ORDER BY id");
        $set = $kunena_db->loadAssocList();
        KunenaError::checkDatabaseError();

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

function kunena_htmlspecialchars($string, $quote_style=ENT_COMPAT, $charset='UTF-8') {
	return htmlspecialchars($string, $quote_style, $charset);
}

// TODO: deprecated
function html_entity_decode_utf8($string)
{
    static $trans_tbl = NULL;

    user_error(__FUNCTION__.'(): Deprecated', E_USER_NOTICE);

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
// TODO: deprecated
function code2utf($num)
{
	if ($num < 128) return chr($num);
    if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    return '';
}

?>
