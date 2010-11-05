<?php
/**
* @version $Id$
* Kunena Component - CThankyou class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.org All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
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

		$this->my			= &JFactory::getUser ();

		$this->config = KunenaFactory::getConfig ();
		$this->_db = &JFactory::getDBO();
		$this->_app =& JFactory::getApplication();

		kimport('thankyou');
	}

	/**
	 * Store ThankYou into the table
	 *
	 * @since 1.6
	 */
	function setThankyou(){
		if (JRequest::checkToken ( 'get' ) == false) {
				$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
				$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
				return;
		}
		if (!$this->my->id) {
			$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_LOGIN'));
			$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
		}
		if(!$this->config->showthankyou) {
			$this->_app->enqueueMessage(JText::_('COM_KUNENA_THANKYOU_DISABLED'));
			$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
		}
		require_once(KPATH_SITE.'/lib/kunena.posting.class.php');
		$post = new CKunenaPosting();
		if (!$post->action($this->pid)) {
			$errors = $post->getErrors();
			$this->_app->enqueueMessage(reset($post->getErrors()));
			$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
		}
		$this->targetuserid = $post->get('userid');
		//Check if the user already said thank you to this post
		if ($this->my->id == $this->targetuserid) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_THANKYOU_NOT_YOURSELF' ) );
			$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
			return;
		}
		$saidit = KunenaThankYou::checkIfThankYouAllready ( $this->pid, $this->my->id );
		if (! empty ( $saidit )) {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_THANKYOU_ALLREADY' ) );
			$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
			return;
		}

		//Perform the insert
		if (KunenaThankYou::storeThankYou ( $this->pid, $this->my->id, $this->targetuserid ) !== true)
			KunenaError::checkDatabaseError ();

		$activityIntegration = KunenaFactory::getActivityIntegration();
		$activityIntegration->onAfterThankyou($this->targetuserid, $this->my->username, $post);

		$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_THANKYOU_SUCCESS' ) );
		$this->_app->redirect ( CKunenaLink::GetMessageURL ( $this->pid, $this->catid, 0, false ) );
	}

	/**
	 * Get Thank You User by Message ID
	 * @param int $pid
	 * @return array usernames with profillink
	 * @since 1.6
	 */
	function getThankYouUser($pid){
		if($this->config->showthankyou){
			$named='';
			if(!$this->config->username) $named = 'name';
			$res = KunenaThankYou::getThankYouUsers($pid,$named);

			if( KunenaError::checkDatabaseError() ) return false;

			$thank_string = '';
			foreach( $res as $k=>$w){
				$thank_string[$k]= CKunenaLink::GetProfileLink($w->id, $w->username);
			}

			return $thank_string;
		}
	}
}

?>
