<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Announcements Controller
 *
 * @since		2.0
 */
class KunenaControllerAnnouncement extends KunenaController {

	public function edit() {
		if (! JRequest::checkToken ()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$now = new JDate();
		$fields = array();
		$fields['title'] = JRequest::getString ( 'title' );
		$fields['description'] = JRequest::getVar ( 'description', '', 'string', JREQUEST_ALLOWRAW );
		$fields['sdescription'] = JRequest::getVar ( 'sdescription', '', 'string', JREQUEST_ALLOWRAW );
		$fields['created'] = JRequest::getString ( 'created', $now->toMysql() );
		$fields['published'] = JRequest::getInt ( 'published', 1 );
		$fields['showdate'] = JRequest::getInt ( 'showdate', 1 );

		$id = JRequest::getInt ('id');
		$announcement = KunenaForumAnnouncementHelper::get($id);
		$announcement->bind($fields);
		if (!$announcement->authorise($id ? 'edit' : 'create') || !$announcement->save()) {
			$this->app->enqueueMessage ( $announcement->getError(), 'error');
			$this->redirectBack ();
		}

		$this->app->enqueueMessage ( JText::_ ( $id ? 'COM_KUNENA_ANN_SUCCESS_EDIT' : 'COM_KUNENA_ANN_SUCCESS_ADD' ) );
		$this->setRedirect ($announcement->getLayoutUrl('default', false));
	}

	public function delete($id) {
		if (! JRequest::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$id = JRequest::getInt ('id');
		$announcement = KunenaForumAnnouncementHelper::get($id);
		if (!$announcement->authorise('delete') || !$announcement->delete()) {
			$this->app->enqueueMessage ( $announcement->getError(), 'error');
			$this->redirectBack ();
		}

		$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ANN_DELETED' ) );
		$this->redirectBack ();
	}
}