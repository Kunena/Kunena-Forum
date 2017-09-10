<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      AlphaUserPoints
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

jimport('joomla.utilities.string');

/**
 * KunenaActivityAlphaUserPoints class to handle activity integration with AlphaUserPoints
 *
 * @deprecated  6.0
 * @since       Kunena
 */
class KunenaActivityAlphaUserPoints extends KunenaActivity
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaActivityAlphaUserPoints constructor.
	 *
	 * @param $params
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $message
	 *
	 * @return boolean
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function onAfterPost($message)
	{
		// Check for permisions of the current category - activity only if public or registered
		if ($this->_checkPermissions($message))
		{
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid     = AlphaUserPointsHelper::getReferreid($message->userid);

			if (Joomla\String\StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0))
			{
				if ($this->_checkRuleEnabled('plgaup_kunena_topic_create'))
				{
					$keyreference = $this->_buildKeyreference('plgaup_kunena_topic_create', $message->id);
					AlphaUserPointsHelper::newpoints('plgaup_kunena_topic_create', $referreid, $keyreference, $datareference);
				}
			}
		}

		return true;
	}

	/**
	 * @param $message
	 *
	 * @return boolean
	 *
	 * @deprecated  6.0
	 * @since       Kunena
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
	 * @return boolean
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	private function _checkRuleEnabled($ruleName)
	{
		$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled($ruleName);

		return !empty($ruleEnabled[0]->published);
	}

	/**
	 * @param          $plugin_function
	 * @param   string $spc
	 *
	 * @return mixed
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	protected function _buildKeyreference($plugin_function, $spc = '')
	{
		return AlphaUserPointsHelper::buildKeyreference($plugin_function, $spc);
	}

	/**
	 * @param $message
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function onAfterReply($message)
	{
		// Check for permisions of the current category - activity only if public or registered
		if ($this->_checkPermissions($message))
		{
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid     = AlphaUserPointsHelper::getReferreid($message->userid);

			if (Joomla\String\StringHelper::strlen($message->message) > $this->params->get('activity_points_limit', 0))
			{
				if ($this->_checkRuleEnabled('plgaup_kunena_topic_reply'))
				{
					$keyreference = $this->_buildKeyreference('plgaup_kunena_topic_reply', $message->id);
					AlphaUserPointsHelper::newpoints('plgaup_kunena_topic_reply', $referreid, $keyreference, $datareference);
				}
			}
		}
	}

	/**
	 * @param $message
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function onAfterDelete($message)
	{
		// Check for permisions of the current category - activity only if public or registered
		if ($this->_checkPermissions($message))
		{
			$aupid = AlphaUserPointsHelper::getAnyUserReferreID($message->userid);

			if ($aupid)
			{
				if ($this->_checkRuleEnabled('plgaup_kunena_message_delete'))
				{
					AlphaUserPointsHelper::newpoints('plgaup_kunena_message_delete', $aupid);
				}
			}
		}
	}

	/**
	 * @param   int $actor
	 * @param   int $target
	 * @param   int $message
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function onAfterThankyou($actor, $target, $message)
	{
		$infoTargetUser = JText::_('COM_KUNENA_THANKYOU_GOT_FROM') . ': ' . KunenaFactory::getUser($actor)->username;
		$infoRootUser   = JText::_('COM_KUNENA_THANKYOU_SAID_TO') . ': ' . KunenaFactory::getUser($target)->username;

		if ($this->_checkPermissions($message))
		{
			$aupactor  = AlphaUserPointsHelper::getAnyUserReferreID($actor);
			$auptarget = AlphaUserPointsHelper::getAnyUserReferreID($target);

			$ruleName = 'plgaup_kunena_message_thankyou';

			$usertargetpoints = intval($this->_getPointsOnThankyou($ruleName));

			if ($usertargetpoints && $this->_checkRuleEnabled($ruleName))
			{
				// For target user
				if ($auptarget)
				{
					AlphaUserPointsHelper::newpoints($ruleName, $auptarget, '', $infoTargetUser, $usertargetpoints);
				}

				// For who has gived the thank you
				if ($aupactor)
				{
					AlphaUserPointsHelper::newpoints($ruleName, $aupactor, '', $infoRootUser);
				}
			}
		}
	}

	/**
	 * @param $ruleName
	 *
	 * @return null
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	private function _getPointsOnThankyou($ruleName)
	{
		$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled($ruleName);

		if (!empty($ruleEnabled[0]->published))
		{
			return $ruleEnabled[0]->points2;
		}

		return null;
	}

	/**
	 * @param $userid
	 *
	 * @return array|boolean
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function getUserMedals($userid)
	{
		if ($userid == 0)
		{
			return false;
		}

		if (!defined("_AUP_MEDALS_LIVE_PATH"))
		{
			define('_AUP_MEDALS_LIVE_PATH', \Joomla\CMS\Uri\Uri::root(true) . '/components/com_alphauserpoints/assets/images/awards/icons/');
		}

		$aupmedals = AlphaUserPointsHelper::getUserMedals('', $userid);
		$medals    = array();

		foreach ($aupmedals as $medal)
		{
			$medals [] = '<img src="' . _AUP_MEDALS_LIVE_PATH . $this->escape($medal->icon) . '" alt="' . $this->escape($medal->rank) . '" title="' . $this->escape($medal->rank) . '" />';
		}

		return $medals;
	}

	/**
	 * @param $var
	 *
	 * @return string
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * @param   int $userid
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	public function getUserPoints($userid)
	{
		if ($userid == 0)
		{
			return false;
		}

		$_db = \Joomla\CMS\Factory::getDBO();

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
	 * @return mixed
	 *
	 * @deprecated  6.0
	 * @since       Kunena
	 */
	protected function _getAUPversion()
	{
		return AlphaUserPointsHelper::getAupVersion();
	}
}
