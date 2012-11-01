<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Kunena
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaProfileKunena extends KunenaProfile {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function getUserListURL($action='', $xhtml = true)
	{
		$config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		if ( $config->userlist_allowed == 1 && $my->id == 0  ) return false;
		return KunenaRoute::_('index.php?option=com_kunena&func=userlist'.$action, $xhtml);
	}

	public function getProfileURL($user, $task='', $xhtml = true)
	{
		if ($user == 0) return false;
		$user = KunenaFactory::getUser($user);
		$my = JFactory::getUser();
		if ($user === false) return false;
		$userid = "&userid={$user->userid}";
		$do = $task ? '&do='.$task : '';
		return KunenaRoute::_("index.php?option=com_kunena&func=profile{$do}{$userid}", $xhtml);
	}

	public function _getTopHits($limit=0) {
		$db = JFactory::getDBO ();
		$query = "SELECT userid AS id, uhits AS count FROM #__kunena_users WHERE uhits>0 ORDER BY uhits DESC";
		$db->setQuery ( $query, 0, $limit );
		$top = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError();
		return $top;
	}

	public function showProfile($view, &$params) {}
}
