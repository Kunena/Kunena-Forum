<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Cpanel Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerCpanel extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena';
	}

	public function display($cachable = false, $urlparams = false) {
		$db = JFactory::getDbo();

		// Enable Kunena updates if they were disabled (but only every 6 hours or logout/login).
		$now = time();
		$timestamp = $this->app->getUserState('pkg_kunena.updateCheck', 0);
		if ($timestamp < $now) {
			$query = $db->getQuery(true)
				->update($db->quoteName('#__update_sites'))
				->set($db->quoteName('enabled').'=1')
				->where($db->quoteName('location') . ' LIKE '. $db->quote('http://update.kunena.org/%'));
			$db->setQuery($query);
			$db->execute();

			$this->app->setUserState('pkg_kunena.updateCheck', $now + 60*60*6);
		}

		parent::display($cachable, $urlparams);
	}
}
