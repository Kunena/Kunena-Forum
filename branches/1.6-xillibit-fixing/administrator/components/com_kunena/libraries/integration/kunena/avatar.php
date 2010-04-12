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

class KunenaAvatarKunena extends KunenaAvatar
{
	public function __construct() {
		$this->priority = 25;
	}

	public function getEditURL()
	{
		return KunenaRoute::_('index.php?option=com_kunena&func=profile&do=edit');
	}

	public function getURL($user, $size='thumb')
	{
		$user = KunenaFactory::getUser($user);
		$avatar = $user->avatar;

		if ( $avatar && file_exists(KPATH_MEDIA ."/avatars/{$avatar}" ) ) {
			if ($size=='thumb') $avatar = 'thumb/'.$avatar;
			$avatar = KURL_MEDIA . "avatars/{$avatar}";
		} else if ($avatar && $size=='thumb' && file_exists( KPATH_MEDIA_LEGACY ."/avatars/s_{$avatar}" )){
			$avatar = KURL_MEDIA_LEGACY . "avatars/s_{$avatar}";
		} else if ($avatar && file_exists( KPATH_MEDIA_LEGACY ."/avatars/{$avatar}" )){
			$avatar = KURL_MEDIA_LEGACY . "avatars/{$avatar}";
		} else {
			// If avatar does not exist use default image
			if ($size=='thumb') $avatar = 's_nophoto.jpg';
			else $avatar = 'nophoto.jpg';

			$avatar = KURL_MEDIA_LEGACY . "avatars/{$avatar}";
		}
		return $avatar;
	}
}
