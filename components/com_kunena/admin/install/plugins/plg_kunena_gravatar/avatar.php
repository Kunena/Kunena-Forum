<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Gravatar
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined ( '_JEXEC' ) or die ();

class KunenaAvatarGravatar extends KunenaAvatar {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
		require_once dirname(__FILE__) . '/gravatar.php';
	}

	public function getEditURL() {
		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
	}

	protected function _getURL($user, $sizex, $sizey) {
		$user = KunenaFactory::getUser($user);
		$gravatar = new KunenaGravatar($user->email);
		$gravatar->setAvatarSize(min($sizex, $sizey));
		$gravatar->setDefaultImage(false);
		$gravatar->setMaxRating('g');
		return $gravatar->buildGravatarURL( true );
	}
}
