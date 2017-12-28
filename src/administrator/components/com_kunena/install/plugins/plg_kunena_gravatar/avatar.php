<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Gravatar
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class KunenaAvatarGravatar extends KunenaAvatar
{
	protected $params = null;

	/**
	 * KunenaAvatarGravatar constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
		require_once dirname(__FILE__) . '/gravatar.php';
	}

	/**
	 * @return boolean
	 */
	public function getEditURL()
	{
		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit');
	}

	/**
	 * @param $user
	 * @param $sizex
	 * @param $sizey
	 *
	 * @return string
	 */
	protected function _getURL($user, $sizex, $sizey)
	{
		$user     = KunenaFactory::getUser($user);
		$gravatar = new KunenaGravatar($user->email);
		$gravatar->setAvatarSize(min($sizex, $sizey));
		$gravatar->setDefaultImage($this->params->get("default_image", false));
		$gravatar->setMaxRating('g');

		return $gravatar->buildGravatarURL(true);
	}
}
