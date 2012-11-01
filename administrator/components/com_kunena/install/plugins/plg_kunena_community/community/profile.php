<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Community
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaProfileCommunity extends KunenaProfile {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function getUserListURL($action='', $xhtml = true)
	{
		$config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		if ( $config->userlist_allowed == 1 && $my->id == 0  ) return false;
		return CRoute::_('index.php?option=com_community&view=search&task=browse', $xhtml);
	}

	public function getProfileURL($userid, $task='', $xhtml = true)
	{
		if ($userid == 0) return false;
		// Get CUser object
		$user = CFactory::getUser($userid);
		if($user === null) return false;
		return CRoute::_('index.php?option=com_community&view=profile&userid='.$userid, $xhtml);
	}

	public function _getTopHits($limit=0) {
		$db = JFactory::getDBO ();
		$query = "SELECT userid AS id, view AS count FROM #__community_users WHERE view>0 ORDER BY view DESC";
		$db->setQuery ( $query, 0, $limit );
		$top = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError();
		return $top;
	}

	public function showProfile($view, &$params) {}
}
