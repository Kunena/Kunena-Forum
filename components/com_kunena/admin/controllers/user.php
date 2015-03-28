<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Controllers
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

require_once __DIR__ . '/users.php';

/**
 * Kunena User Controller
 *
 * @since 3.0
 */
class KunenaAdminControllerUser extends KunenaAdminControllerUsers
{
	public function removecatsubscriptions()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("UPDATE #__kunena_user_categories SET subscribed=0 WHERE user_id='$userid'");

		if (KunenaError::checkDatabaseError())
		{
			return array();
		}
	}

	public function removetopicsubscriptions()
	{
		$db     = JFactory::getDBO();
		$userid = $this->getState($this->getName() . '.id');

		$db->setQuery("UPDATE #__kunena_user_topics SET subscribed=0 WHERE user_id='$userid'");

		if (KunenaError::checkDatabaseError())
		{
			return array();
		}
	}
}
