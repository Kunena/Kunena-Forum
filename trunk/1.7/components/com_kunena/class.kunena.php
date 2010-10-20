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
		JHTML::addIncludePath(KPATH_ADMIN . '/libraries/html/html');

		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = 0;
		$cat_params['sections'] = $sections;
		$cat_params['direction'] = 1;
		$cat_params['unpublished'] = 0;
		$cat_params['catid'] = $catid;
		$cat_params['action'] = 'read';
		return JHTML::_('kunena.categorylist', $name, $catid, $options, $cat_params, $attr, 'value', 'text', $catid);
	}

	function KSelectList($name, $options=array(), $attr='', $sections=false, $id='', $selected=0) {
		JHTML::addIncludePath(KPATH_ADMIN . '/libraries/html/html');

		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = 1;
		$cat_params['sections'] = $sections;
		$cat_params['direction'] = 1;
		$cat_params['unpublished'] = 0;
		$cat_params['catid'] = 0;
		$cat_params['action'] = 'read';
		if (!$id) $id = $name;
		return JHTML::_('kunena.categorylist', $name, 0, $options, $cat_params, $attr, 'value', 'text', $selected, $id);
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
		$kunena_db = JFactory::getDBO ();

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

		$kunena_app->redirect ( $backUrl );
	}

	function KUndelete() {
		$kunena_app = JFactory::getApplication ();
		$kunena_db = JFactory::getDBO ();

		$backUrl = $kunena_app->getUserState ( "com_kunena.ActionBulk" );
		if (!JRequest::checkToken()) {
			$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$kunena_app->redirect ( $backUrl );
		}

		require_once (KUNENA_PATH_LIB . '/kunena.moderation.class.php');
		$kunena_mod = CKunenaModeration::getInstance ();

		$items = KGetArrayInts ( "cb" );

		// start iterating here
		foreach ( $items as $id => $value  ) {
			// Need to get hold value to check if the message is right deleted
			$query = "SELECT MAX(IF(`hold`=2 OR `hold`=3, 1, 0)) FROM #__kunena_messages WHERE `thread`={$kunena_db->quote($id)} GROUP BY `thread`;";
			$kunena_db->setQuery ( $query );
			$messageHold = $kunena_db->loadResult ();
			KunenaError::checkDatabaseError();

			if ($messageHold) {
				$delete = $kunena_mod->UndeleteThread ( $id, true );
				if (! $delete) {
					$kunena_app->enqueueMessage ( $kunena_mod->getErrorMessage (), 'notice' );
				} else {
					$kunena_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_SUCCESS_UNDELETE' ) );
				}

				// Last but not least update forum stats
				kimport('categories');
				KunenaCategory::recount ();

				// Activity integration
				$activity = KunenaFactory::getActivityIntegration();
				$activity->onAfterUndelete($this);
			}
		} //end foreach

		$kunena_app->redirect ( $backUrl );
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
				$app = JFactory::getApplication();

				$menu = JSite::getMenu ();
				$active = $menu->getActive();
				$basemenu = KunenaRoute::getCurrentMenu ();
				if ($basemenu) {
					$module = clone $module;
					// FIXME: J1.5 only
					$search = array ('/menutype=(.*)(\s)/', '/startLevel=(.*)(\s)/', '/endLevel=(.*)(\s)/' );
					$replace = array ("menutype={$basemenu->menutype}\\2", 'startLevel=' . ($basemenu->sublevel + 1) . '\2', 'endLevel=' . ($basemenu->sublevel + 2) . '\2' );
					$module->params = preg_replace ( $search, $replace, $module->params );
				} else {
					if ($active)
						KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_NOMENU_ITEMID', $active->route, $active->id), 'nomenu');
					else
						KunenaError::warning(JText::sprintf('COM_KUNENA_WARNING_NOMENU_COMPONENT'), 'nomenu');
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

function KGetArrayInts($name) {
    $array = JRequest::getVar($name, array ( 0 ), 'post', 'array');

    $items = array();
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

    $items = array();
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
