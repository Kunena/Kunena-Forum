<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined( '_JEXEC' ) or die();

class CKunenaThankyou {

	/**
	 * Contructor
	 *
	 * @since 1.6
	 */
	function __construct(){
		$this->pid			= JRequest::getInt('pid', '');
		$this->catid		= JRequest::getInt('catid','');
		$this->targetuserid	= JRequest::getInt('targetuserid','');

		$this->my			= &JFactory::getUser ();

		$this->config = KunenaFactory::getConfig ();
		$this->_db = &JFactory::getDBO();
		$this->_app =& JFactory::getApplication();

		kimport('thankyou');
	}

	/**
	 * Write the ThankYou into the table
	 *
	 * @since 1.6
	 */
	function setThankyou(){
		if($this->config->showthankyou && $this->my->id){
			//Check if the user already said thank you to this post
			$saidit = KunenaThankYou::checkifthx($this->pid,$this->my->id);
			if(!empty($saidit)){
				$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_ALLREADY'));
				$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->pid, $this->config->messages_per_page, $this->catid) );
				return;
			}

			//Perform the insert
			if(KunenaThankYou::insertthankyou($this->pid, $this->my->id, $this->targetuserid) !== true) KunenaError::checkDatabaseError();

			$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_SUCCESS'));
			$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->pid, $this->config->messages_per_page, $this->catid) );

		}else{
			$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_LOGIN'));
			$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->pid, $this->config->messages_per_page, $this->catid) );
		}
	}

	function getThankyouUser($pid){
		if($this->config->showthankyou){
			if(!$this->config->username) $named = 'name';
			$res = KunenaThankYou::getthxusers($pid,$named);

			if( KunenaError::checkDatabaseError() ) return false;

			foreach( $res as $k=>$w){
				if($k === 0){
					$thank_string .= CKunenaLink::GetProfileLink($w->id, $w->username);
				} else {
					$thank_string .= ', '.CKunenaLink::GetProfileLink($w->id, $w->username);
				}
			}

			return $thank_string;
		}
	}
}

?>
