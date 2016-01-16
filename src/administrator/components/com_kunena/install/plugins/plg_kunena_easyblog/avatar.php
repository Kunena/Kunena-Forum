<?php
/**
 * Kunena plugin - Easyblog integration

 * @version	0.0.1 (2016-01-10)
 * @author	Ruud van Lent | Onlinecommunityhub
 * @copyright	Copyright Ruud van Lent (2016)
 * @link	https://onlinecommunityhub.nl
 * @license	GNU/GPL version 3 or later
 */

defined ( '_JEXEC' ) or die ();

class KunenaAvatarEasyblog extends KunenaAvatar {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function getEditURL()
	{	//vervangen met easyblog url
		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
	}

        public function _getURL($user, $sizex, $sizey){

		if (!$user->userid == 0)
		{
	 		$user = KunenaFactory::getUser($user->userid);
        	        $user = EB::user($user->userid);
			$avatar = $user->getAvatar();
		}
		else
		{
			$avatar = JUri::root( true ).'/components/com_easyblog/assets/images/default_blogger.png';
		}
                return $avatar;
        }
}
