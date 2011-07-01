<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );

/**
 * Kunena Attachments Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerAttachments extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=attachments';
	}

	function delete() {
		$app =  JFactory::getApplication ();
		$db = JFactory::getDBO ();

		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		$cids = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if (! $cids) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_NO_ATTACHMENTS_SELECTED' ), 'error' );
			$app->redirect ( KunenaRoute::_($this->baseurl, false) );
		}

		foreach( $cids as $id ) {
			kimport ('kunena.forum.message.attachment.helper');
			$attachment = KunenaForumMessageAttachmentHelper::get($id);
			$attachment->delete();
		}

		$app->enqueueMessage ( JText::_('COM_KUNENA_ATTACHMENTS_DELETED_SUCCESSFULLY') );
		$app->redirect ( KunenaRoute::_($this->baseurl, false) );
	}
}
