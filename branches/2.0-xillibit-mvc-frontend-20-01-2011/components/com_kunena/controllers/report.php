<?php
/**
 * @version		$Id: manage.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
require_once KPATH_SITE . '/lib/kunena.link.class.php';

/**
 * Kunena Report Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerReport extends KunenaController {
	public function __construct($config = array()) {
		parent::__construct($config);

		$id = JRequest::getInt('id', 0);
		$catid = JRequest::getInt ( 'catid', 0 );
		$config = KunenaFactory::getConfig ();
		$app = JFactory::getApplication ();
		$me = JFactory::getUser ();

		if ( !$id ) {
			JError::raiseError ( 404, JText::_ ( 'COM_KUNENA_UNAVAILABLE') );
			return false;
		}

		if (! $config->email || ! JMailHelper::isEmailAddress ( $config->email )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_EMAIL_INVALID' ), 'error' );
			$app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $catid, $id, NULL, NULL, $id, false ) );
			return false;
		}

		if ($config->reportmsg == 0) {
			$app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $catid, $id, NULL, NULL, $id, false ) );
			return false;
		}

		if ($me->id == 0) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_FORUM_UNAUTHORIZIED2' ), 'error' );
			$app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $catid, $id, NULL, NULL, $id, false ) );
			return false;
		}


		$this->baseurl = 'index.php?option=com_kunena&view=report';

	}

	function submit() {
		$reason = strval ( JRequest::getVar ( 'reason' ) );
		$text = strval ( JRequest::getVar ( 'text' ) );
		$id = JRequest::getInt ( 'id', 0 );
		$catid = JRequest::getInt ( 'catid', 0 );

		$me = JFactory::getUser ();
		$config = KunenaFactory::getConfig ();
		$app = JFactory::getApplication ();

		if (! JRequest::checkToken ()) {
			$this->app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $this->catid, $this->id, 0, NULL, $this->id, false ), COM_KUNENA_ERROR_TOKEN, 'error' );
			return false;
		}


		if (! empty ( $reason ) && ! empty ( $text )) {
			if ($id) {
				$model =	$this->getModel('report');
				$values = $model->getMailMessage($reason, $text,$id);
				$row = $model->getData($id);

				$acl = KunenaFactory::getAccessControl();
				$emailToList = $acl->getSubscribers($row->catid, $row->thread, false, true, true, $me->id);

				if (count ( $emailToList )) {
					jimport ( 'joomla.mail.helper' );

					$sender = JMailHelper::cleanAddress ( $config->board_title . ' ' . JText::_ ( 'COM_KUNENA_GEN_FORUM' ) . ': ' . $values['sendername'] );
					$subject = JMailHelper::cleanSubject ( $values['subject'] );
					$message = JMailHelper::cleanBody ( $values['message'] );

					foreach ( $emailToList as $emailTo ) {
						if (! $emailTo->email || ! JMailHelper::isEmailAddress ( $emailTo->email ))
							continue;

						JUtility::sendMail ( $config->email, $sender, $emailTo->email, $subject, $message );
					}

					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_REPORT_SUCCESS' ) );
					$app->redirect ( CKunenaLink::GetThreadPageURL ( 'view', $catid, $id, 0, NULL, $id, false ) );
				}
			}

		} else {
			// Do nothing empty subject or reason is empty
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_REPORT_FORG0T_SUB_MES' ) );
			$app->redirect ( CKunenaLink::GetReportURL () );
		}
  }

}