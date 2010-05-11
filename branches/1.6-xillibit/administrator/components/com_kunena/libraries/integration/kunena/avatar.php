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

	protected function _getURL($user, $sizex, $sizey)
	{
		$user = KunenaFactory::getUser($user);
		$avatar = $user->avatar;
		$config = KunenaFactory::getConfig();

		$path = KPATH_MEDIA ."/avatars/users";
		$pathgallery = KPATH_MEDIA ."/avatars/";
		if ( $avatar && file_exists("{$path}/{$avatar}" ) ) {
			if ($sizex == $sizey) {
				$resized = "size{$sizex}";
			} else {
				$resized = "size{$sizex}x{$sizey}";
			}
			$rzavatar = "{$resized}_{$avatar}";
			if ( !file_exists( "{$path}/{$rzavatar}" ) ) {
				require_once(KUNENA_PATH_LIB.DS.'kunena.image.class.php');
				CKunenaImageHelper::version($path .DS. $avatar, $path, $rzavatar, $sizex, $sizey, intval($config->avatarquality));
			}
			$avatar = KURL_MEDIA."/avatars/users/{$rzavatar}";
		} else if ($avatar &&  preg_match('`gallery`',$avatar)){
			$avatar = KURL_MEDIA . "avatars/{$avatar}";
		} else {
			// If avatar does not exist use default image
			if ($sizex <= 90) $avatar = 's_nophoto.jpg';
			else $avatar = 'nophoto.jpg';

			$avatar = KURL_MEDIA . "avatars/{$avatar}";
		}
		return $avatar;
	}
}
