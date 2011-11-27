<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Prune Controller
 *
 * @since 2.0
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

		$categories = KunenaForumCategoryHelper::getCategories(JRequest::getVar ( 'prune_forum', array(0) ), false, 'admin');
		if (!$categories) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CHOOSEFORUMTOPRUNE' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		// Convert days to seconds for timestamp functions...
		$prune_days = JRequest::getInt ( 'prune_days', 36500 );
		$prune_date = JFactory::getDate()->toUnix() - ($prune_days * 86400);

		$trashdelete = JRequest::getInt( 'trashdelete', 0);

		$where = array();
		$where[] = " AND tt.last_post_time < {$prune_date}";

		$controloptions = JRequest::getString( 'controloptions', 0);
		if ( $controloptions == 'answered' ) {
			$where[] = 'AND tt.posts>1';
		} elseif ( $controloptions == 'unanswered' ) {
			$where[] = 'AND tt.posts=1';
		} elseif( $controloptions == 'locked' ) {
			$where[] = 'AND tt.locked>0';
		} elseif( $controloptions == 'deleted' ) {
			$where[] = 'AND tt.hold IN (2,3)';
		} elseif( $controloptions == 'unapproved' ) {
			$where[] = 'AND tt.hold=1';
		} elseif( $controloptions == 'shadow' ) {
			$where[] = 'AND tt.moved_id>0';
		} elseif( $controloptions == 'normal' ) {
			$where[] = 'AND tt.locked=0';
		} elseif( $controloptions == 'all' ) {
			// No filtering
		} else {
			$where[] = 'AND 0';
		}

		// Keep sticky topics?
		if (JRequest::getInt( 'keepsticky', 1)) $where[] = ' AND tt.ordering=0';

		$where = implode(' ', $where);

		$params = array(
			'where'=> $where,
		);

		$count = 0;
		foreach ($categories as $category) {
			if ( $trashdelete ) $count += $category->purge($prune_date, $params);
			else $count += $category->trash($prune_date, $params);
		}

		if ( $trashdelete ) $app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNEDELETED') . " {$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		else $app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNETRASHED') . " {$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
