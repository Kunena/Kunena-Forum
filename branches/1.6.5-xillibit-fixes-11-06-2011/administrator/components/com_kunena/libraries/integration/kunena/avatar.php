<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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

	protected function _getURL($user, $sizex, $sizey)
	{
		$user = KunenaFactory::getUser($user);
		$avatar = $user->avatar;
		$config = KunenaFactory::getConfig();

		$path = KPATH_MEDIA ."/avatars";
		if ( !is_file("{$path}/{$avatar}")) {
			// If avatar does not exist use default image
			if ($sizex <= 90) $avatar = 's_nophoto.jpg';
			else $avatar = 'nophoto.jpg';
		}
		$dir = dirname($avatar);
		$file = basename($avatar);
		if ($sizex == $sizey) {
			$resized = "resized/size{$sizex}/{$dir}";
		} else {
			$resized = "resized/size{$sizex}x{$sizey}/{$dir}";
		}
		if ( !is_file( "{$path}/{$resized}/{$file}" ) ) {
			require_once(KUNENA_PATH_LIB.'/kunena.image.class.php');
			CKunenaImageHelper::version("{$path}/{$avatar}", "{$path}/{$resized}", $file, $sizex, $sizey, intval($config->avatarquality));
		}
		return KURL_MEDIA . "avatars/{$resized}/{$file}";
	}
}
