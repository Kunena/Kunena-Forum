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

		// TODO: need to load more than one category, because the user can select more than one category
		$category = KunenaForumCategoryHelper::get(JRequest::getInt ( 'prune_forum', 0 ));
		if (!$category->authorise('admin')) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CHOOSEFORUMTOPRUNE' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		// Convert days to seconds for timestamp functions...
		$prune_days = JRequest::getInt ( 'prune_days', 36500 );
		$prune_date = JFactory::getDate()->toUnix() - ($prune_days * 86400);

		// delete too sticky
		$stickydelete = JRequest::getInt( 'stickydelete', 0);
		if ( $stickydelete ) $sticky = ' AND ordering=0';
		else $sticky = '';

		$trashdelete = JRequest::getInt( 'trashdelete', 0);

		$where = array();
		$where[] = ' AND tt.last_post_time < '.$prune_date.$sticky;

		$hold='0';
		$trashdelete = JRequest::getString( 'controloptions', 0);
		if ( $trashdelete == 'noreplies' ) {
			$where[] = 'AND tt.posts=1';
		} elseif( $trashdelete == 'locked' ) {
			$where[] = 'AND tt.locked>0';
		} elseif( $trashdelete == 'deleted' ) {
			$hold = '2';
		} elseif( $trashdelete == 'unapproved' ) {
			$hold = '1';
		}

		$where = implode(' ', $where);

		// Get up to 100 oldest topics to be deleted
		$params = array(
			'hold' => $hold,
			'where'=> $where,
		);

		if ( $trashdelete ) $category->purge($prune_date, '', $params);
		else $category->trash(JRequest::getInt ( 'prune_forum', 0 ), $prune_date, $params, '');

		if ( $trashdelete ) $app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNEDELETED') . " {$deleted}/{$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		else $app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNETRASHED') . " {$deleted}/{$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
