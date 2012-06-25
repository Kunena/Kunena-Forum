<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage AlphaUserPoints
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
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
		if (class_exists('AlphaUserPointsHelper') && method_exists('AlphaUserPointsHelper', 'getAupVersion')) {
			return AlphaUserPointsHelper::getAupVersion();
		}
		return '1.5';
	}

	protected function _buildKeyreference( $plugin_function, $spc='' ) {
		if (class_exists('AlphaUserPointsHelper') && method_exists('AlphaUserPointsHelper', 'buildKeyreference')) {
			return AlphaUserPointsHelper::buildKeyreference($plugin_function, $spc);
		}
	}

	public function onAfterPost($message) {
		// Check for permisions of the current category - activity only if public or registered
		if ( $this->_checkPermissions($message) ) {
			$datareference = '<a rel="nofollow" href="' . KunenaRoute::_($message->getPermaUrl()) . '">' . $message->subject . '</a>';
			$referreid = AlphaUserPointsHelper::getReferreid( $message->userid );
			if (JString::strlen($message->message) > $this->params->get('activity_points_limit', 0)) {
				if ( $this->_checkRuleEnabled( 'plgaup_kunena_topic_create' ) ) {
					if( $this->_getAUPversion() >= 1.6 ){
						// AUP >= 1.6
						$keyreference = $this->_buildKeyreference( 'plgaup_kunena_topic_create', $message->id ) ;
						AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_create', $referreid, $keyreference, $datareference );
					} else {
						// AUP >= 1.5.12
						AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_create', $referreid, $message->id, $datareference );
					}
				} elseif ( $this->_checkRuleEnabled( 'plgaup_newtopic_kunena' ) ) {
					// AUP <= 1.5.11
					AlphaUserPointsHelper::newpoints ( 'plgaup_newtopic_kunena', $referreid, $message->id, $datareference );
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
					if( $this->_getAUPversion() >= 1.6 ){
						// AUP >= 1.6
						$keyreference = $this->_buildKeyreference( 'plgaup_kunena_topic_reply', $message->id ) ;
						AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', $referreid, $keyreference, $datareference );
					} else {
						// AUP >= 1.5.12
						AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', $referreid, $message->id, $datareference );
					}
				} elseif ( $this->_checkRuleEnabled( 'plgaup_reply_kunena' ) ) {
					// AUP <= 1.5.11
					AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', $referreid, $message->id, $datareference );
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
					// AUP >= 1.5.12
					AlphaUserPointsHelper::newpoints( 'plgaup_kunena_message_delete', $aupid);
				} elseif ( $this->_checkRuleEnabled( 'plgaup_delete_post_kunena' ) ) {
					// AUP <= 1.5.11
					AlphaUserPointsHelper::newpoints( 'plgaup_delete_post_kunena', $aupid);
				}
			}
		}
	}

	public function onAfterThankyou($target, $actor, $message) {
		$infoTargetUser = (JText::_ ( 'COM_KUNENA_THANKYOU_GOT' ).': ' . KunenaFactory::getUser($target)->username );
		$infoRootUser = ( JText::_ ( 'COM_KUNENA_THANKYOU_SAID' ).': ' . KunenaFactory::getUser($actor)->username );
		if ( $this->_checkPermissions($message) ) {
			$auptarget = AlphaUserPointsHelper::getAnyUserReferreID( $target );
			$aupactor = AlphaUserPointsHelper::getAnyUserReferreID( $actor );

			if ( $this->_getAUPversion() < '1.5.12' ) {
				$ruleName = 'plgaup_thankyou_kunena';
			} elseif ( $this->_getAUPversion() >= '1.5.12' ) {
				$ruleName = 'plgaup_kunena_message_thankyou';
			}

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
			define ( '_AUP_MEDALS_LIVE_PATH', JURI::base ( true ) . '/components/com_alphauserpoints/assets/images/awards/icons/' );
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
		if (version_compare(JVERSION, '1.6','>')) {
			// FIXME: Joomla 1.6 can mix up groups and access levels
			if ($accesstype == 'joomla.level' && $category->access <= 2) {
				return true;
			} elseif ($category->pub_access == 1 || $category->pub_access == 2) {
				return true;
			} elseif ($category->admin_access == 1 || $category->admin_access == 2) {
				return true;
			}
			return false;
		} else {
			// Joomla access levels: 0 = public,  1 = registered
			// Joomla user groups:  29 = public, 18 = registered
			if ($accesstype == 'joomla.level' && $category->access <= 1) {
				return true;
			} elseif ($category->pub_access == 0 || $category->pub_access == - 1 || $category->pub_access == 18 || $category->pub_access == 29) {
				return true;
			} elseif ($category->admin_access == 18 || $category->admin_access == 29) {
				return true;
			}
			return false;
		}
	}

	private function _checkRuleEnabled($ruleName) {
		$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled($ruleName);
		return (bool) $ruleEnabled[0]->published;
	}

	private function _getPointsOnThankyou($ruleName) {
		$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled($ruleName);
		if ($ruleEnabled[0]->published) {
			if ( $this->_getAUPversion() < '1.6.0' ) {
				return $ruleEnabled[0]->content_items;
			} elseif ( $this->_getAUPversion() >= '1.6.0' ) {
				return $ruleEnabled[0]->points2;
			}
		}
		return;
	}
}
