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

kimport ( 'kunena.view' );
kimport ( 'kunena.html.parser' );

/**
 * Category View
 */
class KunenaViewCategory extends KunenaView {
	protected $pagination = null;

	function displayDefault($tpl = null) {
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if (! $this->category->authorise('read')) {
			$this->setError($this->category->getError());
		} else {
			$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
			$this->assignRef ( 'total', $this->get ( 'Total' ) );
			$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );
		}
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );

		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->headerText = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->category->name;
		if ($this->category->authorise ( 'moderate' )) {
			$this->actionMove = true;
			$this->actionDropdown[] = JHTML::_('select.option', 'none', '&nbsp;');
			$this->actionDropdown[] = JHTML::_('select.option', 'move', JText::_('COM_KUNENA_MOVE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'delete', JText::_('COM_KUNENA_DELETE_SELECTED'));
			if($this->config->mod_see_deleted == '1' || $this->me->isAdmin($this->category->id)) {
				$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
				$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
			}
		}

		// Is user allowed to post new topic?
		$this->newTopicHtml = '';
		if ($this->category->authorise ( 'topic.create', null, true )) {
			$this->newTopicHtml = CKunenaLink::GetPostNewTopicLink ( $this->category->id, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}

		// Is user allowed to mark forums as read?
		$this->markReadHtml = '';
		if ($this->me->exists() && $this->total) {
			$this->markReadHtml = CKunenaLink::GetCategoryActionLink ( 'markread', $this->category->id, CKunenaTools::showButton ( 'markread', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD_LONG') );
		}

		$this->subscribeCatHtml = '';
		// Is user allowed to subscribe category?
		if ($this->category->authorise ( 'subscribe', null, true )) {
			// FIXME: add into library:
			$db = JFactory::getDBO();
			$query = "SELECT subscribed
				FROM #__kunena_user_categories
				WHERE user_id={$db->Quote($this->me->userid)} AND category_id={$db->Quote($this->category->id)}";
			$db->setQuery ( $query );
			$subscribed = $db->loadResult ();
			if (KunenaError::checkDatabaseError()) return;

			if (!$subscribed) {
				$this->subscribeCatHtml = CKunenaLink::GetCategoryActionLink ( 'subscribe', $this->category->id, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY_LONG') );
			} else {
				$this->subscribeCatHtml = CKunenaLink::GetCategoryActionLink ( 'unsubscribe', $this->category->id, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG') );
			}
		}

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			//meta description and keywords
			$page = intval ( $this->state->get('list.start') / $this->state->get('list.limit') ) + 1;
			$pages = intval ( $this->category->getTopics() / $this->state->get('list.limit') ) + 1;

			$parentCategory = $this->category->getParent();
			$metaKeys = $this->escape ( JText::_('COM_KUNENA_CATEGORIES') . ", {$parentCategory->name}, {$this->category->name}, {$this->config->board_title}, " . JFactory::getApplication()->getCfg ( 'sitename' ) );
			$metaDesc = $this->document->get ( 'description' ) . '. ' . $this->escape ( "{$parentCategory->name} ({$page}/{$pages}) - {$this->category->name} - {$this->config->board_title}" );
			$this->document->setMetadata ( 'keywords', $metaKeys );
			$this->document->setDescription ( $metaDesc );

			$this->setTitle( JText::sprintf('COM_KUNENA_VIEW_CATEGORY_DEFAULT', $this->category->name) . " ({$page}/{$pages})" );

			$this->display ();
		}
	}

	function displayCreate() {
		$this->displayEdit();
	}

	function displayEdit() {
		$this->assignRef ( 'category', $this->get ( 'AdminCategory' ) );
		if ($this->category === false) {
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
			$this->displayNoAccess($this->getErrors());
			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena',JPATH_ADMINISTRATOR);

		$this->assignRef ( 'me', KunenaFactory::getUser() );
		$this->assignRef ( 'options', $this->get ( 'AdminOptions' ) );
		$this->assignRef ( 'moderators', $this->get ( 'AdminModerators' ) );
		$header = $this->category->exists() ? JText::sprintf('COM_KUNENA_CATEGORY_EDIT', $this->escape($this->category->name)) : JText::_('COM_KUNENA_CATEGORY_NEW');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );

		$this->display ();
	}

	function displayAnnouncement() {
		if ($this->config->showannouncement > 0) {
			require_once(KUNENA_PATH_LIB .DS. 'kunena.announcement.class.php');
			$ann = new CKunenaAnnouncement();
			$ann->getAnnouncement();
			$ann->displayBox();
		}
	}

	function displaySubCategories() {
		$children = $this->category->getChildren();
		if (!empty($children)) {
			$params = array('catid'=>$this->category->id);
			KunenaForum::display('categories', 'default', 'clean', $params);
			$this->subcategories = true;
		}
	}

	function displayRows() {
		$lasttopic = NULL;
		$this->position = 0;
		foreach ( $this->topics as $this->topic ) {
			$this->position++;
			$this->keywords = $this->topic->getKeywords(false, ', ');
			$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
			$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
			$this->pages = ceil ( $this->topic->posts / $this->config->messages_per_page );
			if ($this->config->avataroncat) {
				$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarLink('klist-avatar', 'list');
			}

			if (is_object($lasttopic) && $lasttopic->ordering != $this->topic->ordering) {
				$this->spacing = 1;
			} else {
				$this->spacing = 0;
			}
			echo $this->loadTemplate('row');
			$lasttopic = $this->topic;
		}
	}

	function getTopicClass($prefix='k', $class='topic') {
		$class = $prefix . $class;
		$txt = $class . (($this->position & 1) + 1);
		if ($this->topic->ordering) {
			$txt .= '-stickymsg';
		}
		if ($this->topic->getCategory()->class_sfx) {
			$txt .= ' ' . $class . (($this->position & 1) + 1);
			if ($this->topic->ordering) {
				$txt .= '-stickymsg';
			}
			$txt .= $this->escape($this->topic->getCategory()->class_sfx);
		}
		if ($this->topic->hold == 1) $txt .= ' '.$prefix.'unapproved';
		else if ($this->topic->hold) $txt .= ' '.$prefix.'deleted';
		return $txt;
	}

	function getPagination($maxpages) {
		if (empty ( $this->pagination )) {
			$limit = $this->state->get ( 'list.limit' );
			$page = floor ( $this->state->get ( 'list.start' ) / $limit ) + 1;
			$totalpages = max(1, floor ( ($this->total-1) / $limit ) + 1);
			$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
			$endpage = $startpage + $maxpages;
			if ($endpage > $totalpages) {
				$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
				$endpage = $totalpages;
			}

			$this->pagination = '<ul class="kpagination">';
			$this->pagination .= '<li class="page">' . JText::_ ( 'COM_KUNENA_PAGE' ) . '</li>';

			if (($startpage) > 1) {
				if ($endpage < $totalpages)
					$endpage --;
				$this->pagination .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $this->category->id, 1, 1, $rel = 'follow' ) . '</li>';
				if (($startpage) > 2) {
					$this->pagination .= '<li class="more">...</li>';
				}
			}

			for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
				if ($page == $i) {
					$this->pagination .= '<li class="active">' . $i . '</li>';
				} else {
					$this->pagination .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $this->category->id, $i, $i, $rel = 'follow' ) . '</li>';
				}
			}

			if ($endpage < $totalpages) {
				if ($endpage < $totalpages - 1) {
					$this->pagination .= '<li class="more">...</li>';
				}

				$this->pagination .= '<li>' . CKunenaLink::GetCategoryPageLink ( 'showcat', $this->category->id, $totalpages, $totalpages, $rel = 'follow' ) . '</li>';
			}

			$this->pagination .= '</ul>';
		}
		return $this->pagination;
	}
}