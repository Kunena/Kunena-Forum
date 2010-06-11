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

	function __construct(){
		$this->pid			= JRequest::getInt('pid', '');
		$this->catid		= JRequest::getInt('catid','');
		$this->targetuserid	= JRequest::getInt('targetuserid','');

		$this->my			= &JFactory::getUser ();

		$this->config = KunenaFactory::getConfig ();
		$this->_db = &JFactory::getDBO();
		$this->_app =& JFactory::getApplication();
	}

	function setThankyou(){
		if($this->config->showthankyou && $this->my->id){
			//Check if the user already said thank you to this post
			$query = "SELECT userid FROM #__kunena_thankyou WHERE postid={$this->pid} AND userid={$this->my->id}";
			$this->_db->setQuery ( $query );
			$saidit = $this->_db->loadObject ();

			if( KunenaError::checkDatabaseError() ) return false;

			if($saidit->userid){
				$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_ALLREADY'));
				$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->pid, $this->config->messages_per_page, $this->catid) );
				return;
			}

			$query = "INSERT INTO #__kunena_thankyou SET postid={$this->pid} , catid={$this->catid} , userid={$this->my->id} , targetuserid={$this->targetuserid}";
			$this->_db->setQuery( $query );
			$this->_db->query();
			if(KunenaError::checkDatabaseError()) return false;

			$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_SUCCESS'));
			$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->pid, $this->config->messages_per_page, $this->catid) );

		}else{
			$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_LOGIN'));
			$this->_app->redirect ( CKunenaLink::GetLatestPageAutoRedirectURL ( $this->pid, $this->config->messages_per_page, $this->catid) );
		}
	}

	function getThankyouUser($pid){
		if($this->config->username){
			$query	= "SELECT #__users.username, #__users.id FROM #__users LEFT JOIN #__kunena_thankyou ON #__users.id = #__kunena_thankyou.userid WHERE #__kunena_thankyou.postid={$pid}";
		} else {
			$query	= "SELECT #__users.name AS username, #__users.id FROM #__users LEFT JOIN #__kunena_thankyou ON #__users.id = #__kunena_thankyou.userid WHERE #__kunena_thankyou.postid={$pid}";
		}
		$this->_db->setQuery($query);
		$res	= $this->_db->loadObjectList();

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

?>
