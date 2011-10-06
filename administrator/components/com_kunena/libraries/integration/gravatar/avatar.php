<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration.Gravatar
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined ( '_JEXEC' ) or die ();

class KunenaAvatarGravatar extends KunenaAvatar
{
	protected $integration = null;

	public function __construct() {
		$this->priority = 50;
	}

	public function getEditURL()
	{
		trigger_error(__CLASS__.'::'.__FUNCTION__.'() not implemented');
	}

	protected function _getURL($user, $sizex, $sizey)
	{
		jimport( 'joomla.environment.browser' );
		jimport( 'joomla.filesystem.file' );
		$browser = JBrowser::getInstance();
		$protocol = $browser->isSSLConnection() ? "https" : "http";

		$user = KunenaFactory::getUser($user);
		$config = KunenaFactory::getConfig();

		if ($sizex == $sizey) {
			$resized = "resized/size{$sizex}";
		} else {
			$resized = "resized/size{$sizex}x{$sizey}";
		}

		// store the gravatar locally on filesystem
		if  (in_array  ('curl', get_loaded_extensions())) {
			$avatar_path = JPATH_ROOT.'/media/kunena/avatars/users';
			if( !JFile::exists($avatar_path.'gravatar'.$user->userid.'.jpg')){
				$size_pixels = min($sizex,$sizey);
				$avatar = $protocol.'://www.gravatar.com/avatar/'.md5($user->email).'?s=200';


				$ci = curl_init ($avatar);

				curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ci, CURLOPT_HEADER, 0);
				curl_setopt($ci, CURLOPT_BINARYTRANSFER,1);

				$data=curl_exec($ci);

				curl_close ($ci);

				if(JFile::exists($avatar_path.'/gravatar'.$user->userid.'.jpg')){
					unlink($avatar_path.'/gravatar'.$user->userid.'.jpg');
				}

					$fp = fopen($avatar_path.'/gravatar'.$user->userid.'.jpg','x');
				fwrite($fp, $data);
				fclose($fp);

				JFile::copy($avatar_path.'/gravatar'.$user->userid.'.jpg', JPATH_ROOT.'/media/kunena/avatars/resized/size200/users/gravatar'.$user->userid.'.jpg');

				$image_sizes = array('36','48','50','72','90','100','144');
				require_once(KPATH_SITE.'/lib/kunena.image.class.php');
				foreach ( $image_sizes as $size) {
					if ( !is_file( JPATH_ROOT.'/media/kunena/avatars/resized/size'. $size.'/users/' ) ) {
						CKunenaImageHelper::version($avatar_path.'/gravatar'.$user->userid.'.jpg', JPATH_ROOT.'/media/kunena/avatars/resized/size'. $size.'/users/', '/gravatar'.$user->userid.'.jpg' , $size, $size, intval($config->avatarquality));
					}

				}
			}
		}

		return KURL_MEDIA . "avatars/{$resized}/users/gravatar{$user->userid}.jpg";;
	}
}
