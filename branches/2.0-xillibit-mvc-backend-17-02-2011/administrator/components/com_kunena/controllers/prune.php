<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );
kimport ( 'kunena.forum.message.attachment.helper' );

/**
 * Kunena Prune Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminControllerPrune extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=prune';
	}

	function doprune() {
		$app = JFactory::getApplication ();
		if (!JRequest::checkToken()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}
		$category = KunenaForumCategoryHelper::get(JRequest::getInt ( 'prune_forum', 0 ));
		if (!$category->authorise('admin')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CHOOSEFORUMTOPRUNE' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		// Convert days to seconds for timestamp functions...
		$prune_days = JRequest::getInt ( 'prune_days', 36500 );
		$prune_date = JFactory::getDate()->toUnix() - ($prune_days * 86400);

		$trashdelete = JRequest::getInt( 'trashdelete', 0);

		// Get up to 100 oldest topics to be deleted
		$params = array(
			'orderby'=>'tt.last_post_time ASC',
			'where'=>"AND tt.last_post_time<{$prune_date} AND ordering=0",
		);
		list($count, $topics) = KunenaForumTopicHelper::getLatestTopics($category->id, 0, 100, $params);
		$deleted = 0;
		foreach ( $topics as $topic ) {
			$deleted++;
			$topic->delete(false);
		}
		KunenaUserHelper::recount();
		KunenaForumCategoryHelper::recount();
		KunenaForumMessageAttachmentHelper::cleanup();
		$app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNEDELETED') . " {$deleted}/{$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
