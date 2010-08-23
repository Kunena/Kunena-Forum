<?php
/**
 * @version		$Id$
 * KunenaMenu Plugin for JomSocial
 * @package plg_jomsocial_kunenamenu
 * @copyright	Copyright (C) 2009 - 2010 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined ( '_JEXEC' ) or die ();

$path = JPATH_ROOT . '/components/com_community/libraries/core.php';
if (! is_file ( $path ))
	return;
require_once $path;

class plgCommunityKunenaMenu extends CApplications {
	var $name = "My Kunena Menu";
	var $_name = 'kunenamenu';

	function plgCommunityKunenaMenu(& $subject, $config) {
		//Load Language file.
		JPlugin::loadLanguage ( 'plg_community_kunenamenu', JPATH_ADMINISTRATOR );

		// Kunena detection and version check
		$minKunenaVersion = '1.6.0-RC2';
		if (! class_exists ( 'Kunena' ) || Kunena::versionBuild () < 3251) {
			return;
		}
		parent::__construct ( $subject, $config );
	}

	function onSystemStart() {
		//initialize the toolbar object
		$toolbar = CFactory::getToolbar ();

		//adding new 'tab' 'Forum Settings' to JomSocial toolbar
		$toolbar->addGroup ( 'KUNENAMENU', JText::_ ( 'PLG_COMMUNITY_KUNENANENU_FORUM' ), KunenaRoute::_ ( 'index.php?option=com_kunena&func=myprofile' ) );
		// Kunena online check
		if (! Kunena::enabled ()) {
			$toolbar->addItem ( 'KUNENAMENU', 'KUNENAMENU_OFFLINE', JText::_ ( 'PLG_COMMUNITY_KUNENAMENU_KUNENA_OFFLINE' ), KunenaRoute::_ ( 'index.php?option=com_kunena' ) );
			return;
		}
		$toolbar->addItem ( 'KUNENAMENU', 'KUNENAMENU_EDITPROFILE', JText::_ ( 'PLG_COMMUNITY_KUNENAMENU_EDITPROFILE' ), KunenaRoute::_ ( 'index.php?option=com_kunena&func=myprofile&task=edit' ) );
		$toolbar->addItem ( 'KUNENAMENU', 'KUNENAMENU_PROFILE', JText::_ ( 'PLG_COMMUNITY_KUNENAMENU_PROFILE' ), KunenaRoute::_ ( 'index.php?option=com_kunena&func=myprofile' ) );
		$toolbar->addItem ( 'KUNENAMENU', 'KUNENAMENU_POSTS', JText::_ ( 'PLG_COMMUNITY_KUNENAMENU_POSTS' ), KunenaRoute::_ ( 'index.php?option=com_kunena&func=latest&do=userposts' ) );
		$toolbar->addItem ( 'KUNENAMENU', 'KUNENAMENU_SUBSCRIBES', JText::_ ( 'PLG_COMMUNITY_KUNENAMENU_SUBSCRIBTIONS' ), KunenaRoute::_ ( 'index.php?option=com_kunena&func=latest&do=subscriptions' ) );
		$toolbar->addItem ( 'KUNENAMENU', 'KUNENAMENU_FAVORITES', JText::_ ( 'PLG_COMMUNITY_KUNENAMENU_FAVORITES' ), KunenaRoute::_ ( 'index.php?option=com_kunena&func=latest&do=favorites' ) );
	}
}