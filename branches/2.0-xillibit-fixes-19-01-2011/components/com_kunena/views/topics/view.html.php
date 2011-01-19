<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Topics View
 */
class KunenaViewTopics extends KunenaView {
	function displayDefault($tpl = null) {
		$this->layout = 'default';
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->headerText =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->actionDropdown[] = JHTML::_('select.option', '', '&nbsp;');
		$this->actionMove = false;
		if (CKunenaTools::isModerator ( $this->me->userid )) {
			$this->actionMove = true;
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDel', JText::_('COM_KUNENA_DELETE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkMove', JText::_('COM_KUNENA_MOVE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
		}

		//meta description and keywords
		$limit = $this->state->get('list.limit');
		$page = intval($this->state->get('list.start')/$limit)+1;
		$total = intval($this->total/$limit)+1;
		$pagesTxt = "{$page}/{$total}";
		$app = JFactory::getApplication();
		$metaKeys = $this->headerText . $this->escape ( ", {$this->config->board_title}, " ) . $app->getCfg ( 'sitename' );
		$metaDesc = $this->headerText . $this->escape ( " ({$pagesTxt}) - {$this->config->board_title}" );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );
		$this->document->setTitle ( "{$this->title} ({$pagesTxt}) - {$this->config->board_title}" );

		$this->display($tpl);
	}

	function displayUser($tpl = null) {
		$this->layout = 'user';
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->headerText =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->actionDropdown[] = JHTML::_('select.option', '', '&nbsp;');
		$this->actionMove = false;
		if (CKunenaTools::isModerator ( $this->me->userid )) {
			$this->actionMove = true;
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDel', JText::_('COM_KUNENA_DELETE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkMove', JText::_('COM_KUNENA_MOVE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
		}

		//meta description and keywords
		$limit = $this->state->get('list.limit');
		$page = intval($this->state->get('list.start')/$limit)+1;
		$total = intval($this->total/$limit)+1;
		$pagesTxt = "{$page}/{$total}";
		$app = JFactory::getApplication();
		$metaKeys = $this->headerText . $this->escape ( ", {$this->config->board_title}, " ) . $app->getCfg ( 'sitename' );
		$metaDesc = $this->headerText . $this->escape ( " ({$pagesTxt}) - {$this->config->board_title}" );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );
		$this->document->setTitle ( "{$this->title} ({$pagesTxt}) - {$this->config->board_title}" );

		$this->display($tpl);
	}

	function displayPosts($tpl = null) {
		$this->layout = 'posts';
		$this->assignRef ( 'messages', $this->get ( 'Messages' ) );
		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->headerText =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->actionDropdown[] = JHTML::_('select.option', '', '&nbsp;');
		$this->actionMove = false;
		if (CKunenaTools::isModerator ( $this->me->userid )) {
			$this->actionMove = true;
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDel', JText::_('COM_KUNENA_DELETE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkMove', JText::_('COM_KUNENA_MOVE_SELECTED'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkDelPerm', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
			$this->actionDropdown[] = JHTML::_('select.option', 'bulkRestore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
		}

		//meta description and keywords
		$limit = $this->state->get('list.limit');
		$page = intval($this->state->get('list.start')/$limit)+1;
		$total = intval($this->total/$limit)+1;
		$pagesTxt = "{$page}/{$total}";
		$app = JFactory::getApplication();
		$metaKeys = $this->headerText . $this->escape ( ", {$this->config->board_title}, " ) . $app->getCfg ( 'sitename' );
		$metaDesc = $this->headerText . $this->escape ( " ({$pagesTxt}) - {$this->config->board_title}" );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;

		$this->document->setMetadata ( 'robots', 'noindex, follow' );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );
		$this->document->setTitle ( "{$this->title} ({$pagesTxt}) - {$this->config->board_title}" );

		$this->display($tpl);
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
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
			KunenaForum::display('categories', 'default', 'list');
			$this->subcategories = true;
		}
	}

	function displayRows() {
		if ($this->layout == 'posts') {
			$this->displayPostRows();
		} else {
			$this->displayTopicRows();
		}
	}

	function displayTopicRows() {
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

	function displayPostRows() {
		$lasttopic = NULL;
		$this->position = 0;
		foreach ( $this->messages as $this->message ) {
			if (!isset($this->topics[$this->message->thread])) {
				// TODO: INTERNAL ERROR
				return;
			}
			$this->topic = $this->topics[$this->message->thread];
			$this->position++;
			$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
			$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
			$this->pages = ceil ( $this->topic->posts / $this->config->messages_per_page );
			if ($this->config->avataroncat) {
				$this->topic->avatar = KunenaFactory::getUser($this->message->userid)->getAvatarLink('klist-avatar', 'list');
			}
			$this->spacing = 0;
			echo $this->loadTemplate('row');
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

	function displayStats() {
		if ($this->config->showstats > 0) {
			require_once(KUNENA_PATH_LIB .DS. 'kunena.stats.class.php');
			$kunena_stats = CKunenaStats::getInstance ( );
			$kunena_stats->showFrontStats ();
		}
	}

	function displayWhoIsOnline() {
		if ($this->config->showwhoisonline > 0) {
			require_once KPATH_SITE.'/lib/kunena.who.class.php';
			$who = CKunenaWhoIsOnline::getInstance();
			$who->displayWhoIsOnline();
		}
	}

	function getPagination($func, $maxpages) {
		$limit = $this->state->get ( 'list.limit' );
		$page = floor ( $this->state->get ( 'list.start' ) / $limit ) + 1;
		$totalpages = max(1, floor ( ($this->total-1) / $limit ) + 1);

		if ( $func != 'latest' ) $func = 'latest&do='.$func;

		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if (($startpage) > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetLatestPageLink ( $func, 1, 'follow', '' ) . '</li>';
			if (($startpage) > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetLatestPageLink ( $func, $i, 'follow', '' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetLatestPageLink ( $func, $totalpages, 'follow', '' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}
}