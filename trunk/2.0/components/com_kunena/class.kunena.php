<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

jimport('joomla.utilities.string');

require_once (KPATH_SITE . '/lib/kunena.defines.php');
kimport('kunena.error');

$kunena_app = JFactory::getApplication();
$kunena_config = KunenaFactory::getConfig ();

// Kunena live url
define('KUNENA_LIVEURL', JURI::root() . 'index.php?option=com_kunena');
define('KUNENA_LIVEURLREL', 'index.php?option=com_kunena');

// Kunena souces absolute path
define('KUNENA_DIRECTURL', JURI::root() . 'components/com_kunena/');

// Kunena uploaded files directory
define('KUNENA_LIVEUPLOADEDPATH', JURI::root() . 'media/kunena');

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

kimport('kunena.html.parser');

class CKunenaTools {
    var $id = null;

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

    function updateNameInfo()
    {
        $db = &JFactory::getDBO();
        $config = KunenaFactory::getConfig ();

        $fb_queryName = $config->username ? "username" : "name";

	    $query = "UPDATE #__kunena_messages AS m, #__users AS u
	    			SET m.name = u.$fb_queryName
					WHERE m.userid = u.id";
        $db->setQuery($query);
        $db->query();
        KunenaError::checkDatabaseError();
        return $db->getAffectedRows();
    }

	function markTopicRead($thread, $userid) {
		$thread = intval ( $thread );
		$userid = intval ( $userid );
		if (! $userid || ! $thread)
			return;

		$db = JFactory::getDBO ();
		$kunena_session = KunenaFactory::getSession ();

		$readTopics = explode ( ',', $kunena_session->readtopics );
		if (! in_array ( $thread, $readTopics )) {
			$readTopics[] = $thread;
			$readTopics = implode ( ',', $readTopics );
		} else {
			$readTopics = false; // do not update session
		}

		if ($readTopics) {
			$db->setQuery ( "UPDATE #__kunena_sessions SET readtopics={$db->Quote($readTopics)} WHERE userid={$db->Quote($userid)}" );
			$db->query ();
			KunenaError::checkDatabaseError();
		}
	}

	function KSelectList($name, $options=array(), $attr='', $sections=false, $id='', $selected=0) {
		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = 1;
		$cat_params['sections'] = $sections;
		$cat_params['direction'] = 1;
		$cat_params['unpublished'] = 0;
		$cat_params['catid'] = 0;
		$cat_params['action'] = 'read';
		if (!$id) $id = $name;
		return JHTML::_('kunenaforum.categorylist', $name, 0, $options, $cat_params, $attr, 'value', 'text', $selected, $id);
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
			$template = KunenaFactory::getTemplate($template);
			if ($once){
				require_once (JPATH_ROOT.'/'.$template->getFile($relpath));
			} else {
				require (JPATH_ROOT.'/'.$template->getFile($relpath));
			}
		}

		/**
		 * This function load datas for rules or help page
		 *
		 */
		function getRulesHelpDatas($id) {
			$db = JFactory::getDBO ();

			$db->setQuery ( "SELECT introtext, id FROM #__content WHERE id={$db->Quote($id)}" );
			$introtext = $db->loadResult ();
			KunenaError::checkDatabaseError();

			return $introtext;
		}


		/**
		 * Wrapper to addStyleSheet
		 *
		 */
		function addStyleSheet($filename) {

			$document = JFactory::getDocument ();
			$config = KunenaFactory::getConfig ();

			if ($config->debug || KunenaForum::isSvn()) {
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

			$document = JFactory::getDocument ();
			$config = KunenaFactory::getConfig ();

			if ($config->debug || KunenaForum::isSvn()) {
				// If we are in debug more, make sure we load the unpacked css
				$filename = preg_replace ( '/\-min\./u', '.', $filename );
			}

			return $document->addScript ( $filename );
		}

    } // end of class