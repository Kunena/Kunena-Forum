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

class KunenaProfileEasyblog extends KunenaProfile {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = JFactory::getUser();

		if ($config->userlist_allowed == 1 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
	}

	public function getProfileURL($userid, $task='', $xhtml = true) {
		//Make sure that user profile exist.
		if (!$userid) {
			return false;
		}
		return JRoute::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $userid, false);
	}

	public function showProfile($view, &$params) {}

	public function getEditProfileURL($userid, $xhtml = true) {
		return $this->getProfileURL($userid, 'edit', $xhtml);
	}
}

