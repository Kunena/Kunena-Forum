<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

class KunenaActivityAlphaUserPoints extends KunenaActivity {
	protected $integration = null;

	public function __construct() {
		$aup = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if (! file_exists ( $aup ))
			return;
		require_once ($aup);
		$this->priority = 50;
		$this->_config = KunenaFactory::getConfig ();
	}

	private function _getAUPversion(){
		return AlphaUserPointsHelper::getAupVersion();
	}

	public function onAfterPost($message) {
		// Check for permisions of the current category - activity only if public or registered
		if (! empty ( $message->parent ) && ($message->parent->pub_access == 0 || $message->parent->pub_access == - 1)) {
			require_once KPATH_SITE.'/lib/kunena.link.class.php';
			$datareference = '<a href="' . CKunenaLink::GetMessageURL ( $message->get ( 'id' ), $message->get ( 'catid' ) ) . '">' . $message->get ( 'subject' ) . '</a>';
			if ( $this->_getAUPversion() < '1.5.12' ) {
				$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_newtopic_kunena' );
				if ($ruleEnabled) {
					AlphaUserPointsHelper::newpoints ( 'plgaup_newtopic_kunena', '', $message->get ( 'id' ), $datareference );
				} else {
					return;
				}
			} elseif ( $this->_getAUPversion() >= '1.5.12' ) {
				$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_kunena_topic_create' );
				if ($ruleEnabled) {
					AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_create', '', $message->get ( 'id' ), $datareference );
				} else {
					return;
				}
			}
		}
	}

	public function onAfterReply($message) {
		// Check for permisions of the current category - activity only if public or registered
		if (! empty ( $message->parent ) && ($message->parent->pub_access == 0 || $message->parent->pub_access == - 1)) {
			require_once KPATH_SITE.'/lib/kunena.link.class.php';
			$datareference = '<a href="' . CKunenaLink::GetMessageURL ( $message->get ( 'id' ), $message->get ( 'catid' ) ) . '">' . $message->get ( 'subject' ) . '</a>';
			if ($this->_config->alphauserpointsnumchars > 0) {
				// use if limit chars for a response
				if (JString::strlen ( $message->get ( 'message' ) ) > $this->_config->alphauserpointsnumchars) {
					if ( $this->_getAUPversion() < '1.5.12' ) {
						$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_reply_kunena' );
						if ($ruleEnabled) {
							AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $message->get ( 'id' ), $datareference );
						} else {
							return;
						}
					} elseif ( $this->_getAUPversion() >= '1.5.12' ) {
						$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_kunena_topic_reply' );
						if ($ruleEnabled) {
							AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', '', $message->get ( 'id' ), $datareference );
						} else {
							return;
						}
					}
				}
			} else {
				if ( $this->_getAUPversion() < '1.5.12' ) {
					$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_reply_kunena' );
					if ($ruleEnabled) {
						AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $message->get ( 'id' ), $datareference );
					} else {
						return;
					}
				} elseif ( $this->_getAUPversion() >= '1.5.12' ) {
					$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_kunena_topic_reply' );
					if ($ruleEnabled) {
						AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', '', $message->get ( 'id' ), $datareference );
					} else {
						return;
					}
				}
			}
		}
	}

	public function onAfterDelete($message) {
		// Check for permisions of the current category - activity only if public or registered
		if (! empty ( $message->parent ) && ($message->parent->pub_access == 0 || $message->parent->pub_access == - 1)) {
			$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $message->parent->userid );
			if ( $aupid ) {
				if ( $this->_getAUPversion() < '1.5.12' ) {
					$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_delete_post_kunena' );
					if ($ruleEnabled) {
						AlphaUserPointsHelper::newpoints( 'plgaup_delete_post_kunena', $aupid);
					} else {
						return;
					}
				} elseif ( $this->_getAUPversion() >= '1.5.12' ) {
					$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_kunena_message_delete' );
					if ($ruleEnabled) {
						AlphaUserPointsHelper::newpoints( 'plgaup_kunena_message_delete', $aupid);
					} else {
						return;
					}
				}
			}
		}
	}

	public function onAfterThankyou($thankyoutargetid, $username, $message) {
		$infoTargetUser = (JText::_ ( 'COM_KUNENA_THANKYOU_GOT' ).': ' . $username);
		$infoRootUser = ( JText::_ ( 'COM_KUNENA_THANKYOU_SAID' ).': ' . $message->parent->name );
		if (! empty ( $message->parent ) && ($message->parent->pub_access == 0 || $message->parent->pub_access == - 1)) {
			$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $thankyoutargetid );

			if ( $this->_getAUPversion() < '1.5.12' ) {
				$ruleName = 'plgaup_thankyou_kunena';
				$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( $ruleName );
				$usertargetpoints = intval($ruleEnabled[0]->content_items);
			} elseif ( $this->_getAUPversion() >= '1.5.12' ) {
				$ruleName = 'plgaup_kunena_message_thankyou';
				$ruleEnabled = AlphaUserPointsHelper::checkRuleEnabled( $ruleName );
				$usertargetpoints = intval($ruleEnabled[0]->content_items);
			} else {
				return;
			}

			if ( $aupid && $usertargetpoints && $ruleEnabled ) {
				// for target user
				AlphaUserPointsHelper::newpoints($ruleName , $aupid, '', $infoTargetUser, $usertargetpoints);
				// for who has gived the thank you
				AlphaUserPointsHelper::newpoints($ruleName , '', '', $infoRootUser );
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
}
