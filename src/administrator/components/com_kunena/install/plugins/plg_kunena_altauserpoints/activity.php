<?php
/**
 * Kunena Plugin
 *
 * @package    Kunena.Plugins
 * @subpackag   AltaUserPoints
 *
 * @copyright  (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.kunena.org
 **/
defined('_JEXEC') or die ();
jimport('joomla.utilities.string');

/**
 * KunenaActivityAltaUserPoints class to handle activity integration with AltaUserPoints
 *
 * @since  5.0
 */
class KunenaActivityAltaUserPoints extends KunenaActivity
{
	protected $params = null;

	/**
	 * KunenaActivityAltaUserPoints constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @return mixed
	 */
	protected function _getAUPversion()
	{
		return AltaUserPointsHelper::getAupVersion();
	}

	/**
	 * @param        $plugin_function
	 * @param string $spc
	 *
	 * @return mixed
	 */
	protected function _buildKeyreference($plugin_function, $spc = '')
	{
		return AltaUserPointsHelper::buildKeyreference($plugin_function, $spc);
	}

	/**
	 * @param $message
	 *
	 * @return bool
	 */
	public function onAfterPost($message)
	{
		// Check for permisions of the current category - activity only if public or registered
		if ($this->_checkPermissions($message))
		{
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid     = AltaUserPointsHelper::getReferreid($message->userid);

			if (Joomla\String\StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0))
			{
				if ($this->_checkRuleEnabled('plgaup_kunena_topic_create'))
				{
					$keyreference = $this->_buildKeyreference('plgaup_kunena_topic_create', $message->id);
					AltaUserPointsHelper::newpoints('plgaup_kunena_topic_create', $referreid, $keyreference, $datareference);
				}
			}
		}

		return true;
	}

	/**
	 * @param $message
	 */
	public function onAfterReply($message)
	{
		// Check for permisions of the current category - activity only if public or registered
		if ($this->_checkPermissions($message))
		{
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid     = AltaUserPointsHelper::getReferreid($message->userid);

			if (Joomla\String\StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0))
			{
				if ($this->_checkRuleEnabled('plgaup_kunena_topic_reply'))
				{
					$keyreference = $this->_buildKeyreference('plgaup_kunena_topic_reply', $message->id);
					AltaUserPointsHelper::newpoints('plgaup_kunena_topic_reply', $referreid, $keyreference, $datareference);
				}
			}
		}
	}

	public function onAfterDelete($message)
	{
		// Check for permissions of the current category - activity only if public or registered
		if ($this->_checkPermissions($message))
		{
			$aupid = AltaUserPointsHelper::getAnyUserReferreID($message->userid);

			if ($aupid)
			{
				if ($this->_checkRuleEnabled('plgaup_kunena_message_delete'))
				{
					AltaUserPointsHelper::newpoints('plgaup_kunena_message_delete', $aupid);
				}
			}
		}
	}

	/**
	 * @param int $actor
	 * @param int $target
	 * @param int $message
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
		$infoTargetUser = JText::_('COM_KUNENA_THANKYOU_GOT_FROM') . ': ' . KunenaFactory::getUser($actor)->username;
		$infoRootUser   = JText::_('COM_KUNENA_THANKYOU_SAID_TO') . ': ' . KunenaFactory::getUser($target)->username;

		if ($this->_checkPermissions($message))
		{
			$aupactor         = AltaUserPointsHelper::getAnyUserReferreID($actor);
			$auptarget        = AltaUserPointsHelper::getAnyUserReferreID($target);
			$ruleName         = 'plgaup_kunena_message_thankyou';
			$usertargetpoints = intval($this->_getPointsOnThankyou($ruleName));

			if ($usertargetpoints && $this->_checkRuleEnabled($ruleName))
			{
				// For target user
				if ($auptarget)
				{
					AltaUserPointsHelper::newpoints($ruleName, $auptarget, '', $infoTargetUser, $usertargetpoints);
				}
				// For who has gived the thank you
				if ($aupactor)
				{
					AltaUserPointsHelper::newpoints($ruleName, $aupactor, '', $infoRootUser);
				}
			}
		}
	}

	/**
	 * @param $var
	 *
	 * @return string
	 */
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @param $userid
	 *
	 * @return array|bool
	 */
	public function getUserMedals($userid)
	{
		if ($userid == 0)
		{
			return false;
		}

		if (!defined("_AUP_MEDALS_LIVE_PATH"))
		{
			define('_AUP_MEDALS_LIVE_PATH', JUri::root(true) . '/components/com_altauserpoints/assets/images/awards/icons/');
		}

		$aupmedals = AltaUserPointsHelper::getUserMedals('', $userid);
		$medals    = array();

		foreach ($aupmedals as $medal)
		{
			$medals [] = '<img src="' . _AUP_MEDALS_LIVE_PATH . $this->escape($medal->icon) . '" alt="' . $this->escape($medal->rank) . '" title="' . $this->escape($medal->rank) . '" />';
		}

		return $medals;
	}

	/**
	 * @param int $userid
	 *
	 * @return bool
	 */
	public function getUserPoints($userid)
	{
		if ($userid == 0)
		{
			return false;
		}

		$_db = JFactory::getDBO();
		$_db->setQuery("SELECT points FROM #__alpha_userpoints WHERE `userid`='" . (int) $userid . "'");

		try
		{
			$userpoints = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $userpoints;
	}

	/**
	 * @param $message
	 *
	 * @return bool
	 */
	private function _checkPermissions($message)
	{
		$category   = $message->getCategory();
		$accesstype = $category->accesstype;

		if ($accesstype != 'joomla.group' && $accesstype != 'joomla.level')
		{
			return false;
		}

		// FIXME: Joomla 2.5 can mix up groups and access levels
		if ($accesstype == 'joomla.level' && $category->access <= 2)
		{
			return true;
		}
		elseif ($category->pub_access == 1 || $category->pub_access == 2)
		{
			return true;
		}
		elseif ($category->admin_access == 1 || $category->admin_access == 2)
		{
			return true;
		}

		return false;
	}

	/**
	 * @param $ruleName
	 *
	 * @return bool
	 */
	private function _checkRuleEnabled($ruleName)
	{
		$ruleEnabled = AltaUserPointsHelper::checkRuleEnabled($ruleName);

		return !empty($ruleEnabled[0]->published);
	}

	/**
	 * @param $ruleName
	 *
	 * @return null
	 */
	private function _getPointsOnThankyou($ruleName)
	{
		$ruleEnabled = AltaUserPointsHelper::checkRuleEnabled($ruleName);

		if (!empty($ruleEnabled[0]->published))
		{
			return $ruleEnabled[0]->points2;
		}

		return null;
	}
}
