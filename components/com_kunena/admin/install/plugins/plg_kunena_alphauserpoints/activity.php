<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage AlphaUserPoints
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.utilities.string');

class KunenaActivityAlphaUserPoints extends KunenaActivity {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	protected function _getAUPversion() {
		return AlphaUserPointsHelper::getAupVersion();
	}

	protected function _buildKeyreference( $plugin_function, $spc='' ) {
		return AlphaUserPointsHelper::buildKeyreference($plugin_function, $spc);
	}

	public function onAfterPost($message) {
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid = AlphaUserPointsHelper::getReferreid( $message->userid );
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				if ( $this->_checkRuleEnabled( 'plgaup_kunena_topic_create' ) ) {
					$keyreference = $this->_buildKeyreference( 'plgaup_kunena_topic_create', $message->id ) ;
					AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_create', $referreid, $keyreference, $datareference );
				}
			}
		}
		return true;
	}

	public function onAfterReply($message) {
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid = AlphaUserPointsHelper::getReferreid( $message->userid );
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				if ( $this->_checkRuleEnabled( 'plgaup_kunena_topic_reply' ) ) {
					$keyreference = $this->_buildKeyreference( 'plgaup_kunena_topic_reply', $message->id ) ;
					AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', $referreid, $keyreference, $datareference );
				}
			}
		}
	}

	public function onAfterDelete($message) {
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $message->userid );
			if ( $aupid ) {
				if ( $this->_checkRuleEnabled( 'plgaup_kunena_message_delete' ) ) {
					AlphaUserPointsHelper::newpoints( 'plgaup_kunena_message_delete', $aupid);
				}
			}
		}
	}

	public function onAfterThankyou($actor, $target, $message) {
		$infoTargetUser = JText::_('COM_KUNENA_THANKYOU_GOT_FROM').': ' . KunenaFactory::getUser($actor)->username;
		$infoRootUser = JText::_('COM_KUNENA_THANKYOU_SAID_TO').': ' . KunenaFactory::getUser($target)->username;
		if ($this->_checkPermissions($message)) {
			$aupactor = AlphaUserPointsHelper::getAnyUserReferreID($actor);
			$auptarget = AlphaUserPointsHelper::getAnyUserReferreID($target);

			$ruleName = 'plgaup_kunena_message_thankyou';

			$usertargetpoints = intval($this->_getPointsOnThankyou($ruleName));

			if ( $usertargetpoints && $this->_checkRuleEnabled($ruleName) ) {
				// for target user
				if ($auptarget) AlphaUserPointsHelper::newpoints($ruleName , $auptarget, '', $infoTargetUser, $usertargetpoints);
				// for who has gived the thank you
				if ($aupactor) AlphaUserPointsHelper::newpoints($ruleName , $aupactor, '', $infoRootUser );
			}
		}
	}

	function escape($var) {
		return htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}

	public function getUserMedals($userid) {
		if ($userid == 0)
			return false;

		if (! defined ( "_AUP_MEDALS_LIVE_PATH" )) {
			define ( '_AUP_MEDALS_LIVE_PATH', JUri::root(true) . '/components/com_alphauserpoints/assets/images/awards/icons/' );
		}

		$aupmedals = AlphaUserPointsHelper::getUserMedals ( '', $userid );
		$medals = array ();
		foreach ( $aupmedals as $medal ) {
			$medals [] = '<img src="' . _AUP_MEDALS_LIVE_PATH . $this->escape ( $medal->icon ) . '" alt="' . $this->escape ( $medal->rank ) . '" title="' . $this->escape ( $medal->rank ) . '" />';
		}

		return $medals;
	}

	public function getUserPoints($userid) {
		if ($userid == 0)
			return false;
		$_db = JFactory::getDBO ();

		$_db->setQuery ( "SELECT points FROM #__alpha_userpoints WHERE `userid`='" . ( int ) $userid . "'" );
		$userpoints = $_db->loadResult ();
		KunenaError::checkDatabaseError ();
		return $userpoints;
	}

	private function _checkPermissions($message) {
		$category = $message->getCategory();
		$accesstype = $category->accesstype;
		if ($accesstype != 'joomla.group' && $accesstype != 'joomla.level') {
			return false;
		}

		// FIXME: Joomla 2.5 can mix up groups and access levels
		if ($accesstype == 'joomla.level' && $category->access <= 2) {
			return true;
		} elseif ($category->pub_access == 1 || $category->pub_access == 2) {
			return true;
		} elseif ($category->admin_access == 1 || $category->admin_access == 2) {
			return true;
		}
		return false;
	}

	private function _checkRuleEnabled($ruleName) {
		$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled($ruleName);
		return !empty($ruleEnabled[0]->published);
	}

	private function _getPointsOnThankyou($ruleName) {
		$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled($ruleName);
		if (!empty($ruleEnabled[0]->published)) {
			return $ruleEnabled[0]->points2;
		}
		return null;
	}
}
