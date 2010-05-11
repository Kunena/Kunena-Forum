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
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaAvatarCommunityBuilder extends KunenaAvatar
{
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->priority = 50;
	}

	public function getEditURL()
	{
		return cbSef( 'index.php?option=com_comprofiler&task=userAvatar' . getCBprofileItemid() );
	}

	protected function _getURL($user, $sizex, $sizey)
	{
		$user = KunenaFactory::getUser($user);
		// Get CUser object
		$cbUser = null;
		if ($user->userid) {
			$cbUser = CBuser::getInstance( $user->userid );
		}
		if ( $cbUser === null ) {
			if ($sizex<=90) return selectTemplate() . 'images/avatar/tnnophoto_n.png';
			return selectTemplate() . 'images/avatar/nophoto_n.png';
		}
		if ($sizex<=90) return $cbUser->getField( 'avatar' , null, 'csv' );
		return $cbUser->getField( 'avatar' , null, 'csv', 'none', 'list' );
	}
}
