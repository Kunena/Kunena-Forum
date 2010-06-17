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
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(KUNENA_PATH_LIB .DS. 'kunena.pathway.class.php');
$pathway = new CKunenaPathway();

global $kunena_icons;

$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
$catid = JRequest::getInt ( 'catid', 0 );
$id = JRequest::getInt ( 'id', 0 );

?>
<!-- Pathway -->
<?php
$sfunc = JRequest::getVar ( "func", null );

if ($func != "") {
	$catids = intval ( $catid );
	$jr_path_menu = array ();

	$fr_title_name = JText::_('COM_KUNENA_CATEGORIES');
	while ( $catids > 0 ) {
		$results = $pathway->getCatsDetails($catids);

		if (! $results)
			break;
		$parent_ids = $results->parent;
		$fr_name = kunena_htmlspecialchars ( JString::trim ( $results->name ) );
		$sname = CKunenaLink::GetCategoryLink ( 'showcat', $catids, $fr_name );

		if ($catid == $catids && $sfunc != "view") {
			$fr_title_name = $fr_name;
			$jr_path_menu [] = $fr_name;
		} else {
			$jr_path_menu [] = $sname;
		}

		// next looping
		$catids = $parent_ids;
	}

	//reverse the array
	$jr_path_menu = array_reverse ( $jr_path_menu );

	//attach topic name
	$this->kunena_topic_title = '';
	if ($sfunc == "view" and $id) {
		$this->kunena_topic_title = $pathway->getMessagesTitles($id);
		$jr_path_menu [] = $this->kunena_topic_title;
	}

	// print the list
	if (count ( $jr_path_menu ) == 0)
		$jr_path_menu [] = '';
	$jr_forum_count = count ( $jr_path_menu );

	$fireinfo = '';
	if (! empty ( $this->kunena_forum_locked )) {
		$fireinfo = isset ( $kunena_icons ['forumlocked'] ) ? ' <img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forumlocked'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '" title="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '"/>' : ' <img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0"  alt="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '" title="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '">';
		$lockedForum = 1;
	}

	if (! empty ( $this->kunena_forum_reviewed )) {
		$fireinfo = isset ( $kunena_icons ['forummoderated'] ) ? ' <img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forummoderated'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_MODERATED') . '" title="' . JText::_('COM_KUNENA_GEN_MODERATED') . '"/>' : ' <img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . JText::_('COM_KUNENA_GEN_MODERATED') . '" title="' . JText::_('COM_KUNENA_GEN_MODERATED') . '">';
		$moderatedForum = 1;
	}

	$firepath = '<div class="path-element-first">' . CKunenaLink::GetKunenaLink ( kunena_htmlspecialchars ( $this->config->board_title ) ) . '</div>';

	$firelast = '';
	for($i = 0; $i < $jr_forum_count; $i ++) {
		if ($i == $jr_forum_count - 1) {
			$firelast .= '<br /><div class="path-element-last">' . $jr_path_menu [$i] . $fireinfo . '</div>';
		} else {
			$firepath .= '<div class="path-element">' . $jr_path_menu [$i] . '</div>';
		}
	}

	//get viewing
	$fireonline = '';
	if ( $this->config->onlineusers ) {
		if ($sfunc == "userprofile") {
			$fireonline .= JText::_('COM_KUNENA_USER_PROFILE');
			$fireonline .= $this->kunena_username;
		} else {
			$fireonline .= "<div class=\"path-element-users\">(".$pathway->getTotalViewing($sfunc). ' ' . JText::_('COM_KUNENA_PATHWAY_VIEWING') . ")&nbsp;";
			$fireonline .= $pathway->getUsersOnlineList($sfunc);
		}
		$fireonline .= '</div>';
	}

	$pathway->document->setTitle ( htmlspecialchars_decode ( $this->kunena_topic_title ? $this->kunena_topic_title : $fr_title_name ) . ' - ' . $this->config->board_title );

	$this->kunena_pathway1 = $firepath . $fireinfo;
	$this->kunena_pathway2 = $firelast . $fireonline;

	echo '<div class = "kforum-pathway">';
	echo $this->kunena_pathway1 . $this->kunena_pathway2;
	echo '</div>';
}