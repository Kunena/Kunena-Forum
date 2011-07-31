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
		$browser = JBrowser::getInstance();
		$protocol = $browser->isSSLConnection() ? "https" : "http";

		$size_pixels = min($sizex,$sizey);

		$avatar = $protocol.'://www.gravatar.com/avatar/'.md5($user->email).'?s='.$size_pixels;

		return $avatar;
	}
}
