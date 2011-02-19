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
		$this->Itemid = $this->get ( 'Itemid' );
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if (! $this->category->authorise('read')) {
				$this->setError($this->category->getError());
		}

		$this->assignRef ( 'topics', $this->get ( 'Topics' ) );
		$this->assignRef ( 'total', $this->get ( 'Total' ) );
		$this->assignRef ( 'topicActions', $this->get ( 'TopicActions' ) );
		$this->assignRef ( 'actionMove', $this->get ( 'ActionMove' ) );
		$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );

		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->sections = isset($this->categories[0]) ? $this->categories[0] : array();

		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->headerText = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->category->name;

		// Is user allowed to post new topic?
		$this->newTopicHtml = '';
		if ($this->category->authorise ( 'topic.create', null, true )) {
			$this->newTopicHtml = CKunenaLink::GetPostNewTopicLink ( $this->category->id, $this->getButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}

		// Is user allowed to mark forums as read?
		$this->markReadHtml = '';
		if ($this->me->exists() && $this->total) {
			$this->markReadHtml = CKunenaLink::GetCategoryActionLink ( 'markread', $this->category->id, $this->getButton ( 'markread', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_MARKFORUMREAD_LONG') );
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
				$this->subscribeCatHtml = CKunenaLink::GetCategoryActionLink ( 'subscribe', $this->category->id, $this->getButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_CATEGORY_LONG') );
			} else {
				$this->subscribeCatHtml = CKunenaLink::GetCategoryActionLink ( 'unsubscribe', $this->category->id, $this->getButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_CATEGORY_LONG') );
			}
		}

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
			return;
		}

		//meta description and keywords
		$page = intval ( $this->state->get('list.start') / $this->state->get('list.limit') ) + 1;
		$pages = intval ( $this->category->getTopics() / $this->state->get('list.limit') ) + 1;

		$parentCategory = $this->category->getParent();
		$metaKeys = $this->escape ( JText::_('COM_KUNENA_CATEGORIES') . ", {$parentCategory->name}, {$this->category->name}, {$this->config->board_title}, " . JFactory::getApplication()->getCfg ( 'sitename' ) );
		$metaDesc = $this->document->get ( 'description' ) . '. ' . $this->escape ( "{$parentCategory->name} ({$page}/{$pages}) - {$this->category->name} - {$this->config->board_title}" );
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );

		$this->setTitle( JText::sprintf('COM_KUNENA_VIEW_CATEGORY_DEFAULT', $this->category->name) . " ({$page}/{$pages})" );

		$this->display ($tpl);
	}

	function displayIndex($tpl = null) {
		$this->Itemid = $this->get ( 'Itemid' );
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if ($this->category->id && ! $this->category->authorise('read')) {
			$this->setError($this->category->getError());
		}
		$this->assignRef ( 'topic_ordering', $this->get ( 'MessageOrdering' ) );
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );
		$this->sections = isset($this->categories[0]) ? $this->categories[0] : array();

		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		if (!$this->category->parent_id) {
			if ($this->me->isAdmin(null)) {
				$this->category_manage = CKunenaLink::GetHrefLink(KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage&catid='.$this->category->id), $this->getButton ( 'moderate', JText::_('COM_KUNENA_BUTTON_MANAGE_CATEGORIES') ), $title = '', 'nofollow', 'kicon-button kbuttonmod btn-left', '', JText::_('COM_KUNENA_BUTTON_MANAGE_CATEGORIES_LONG'));
			}
		}

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
			return;
		}
		// meta description and keywords
		$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
		$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . JFactory::getApplication ()->getCfg ( 'sitename' ));

		$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;
		$this->document->setMetadata ( 'keywords', $metaKeys );
		$this->document->setDescription ( $metaDesc );

		$this->setTitle ( JText::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT') );

		$this->display ($tpl);
	}

	function displayUser($tpl = null) {
		$this->Itemid = $this->get ( 'Itemid' );
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->me = KunenaFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');

			// meta description and keywords
			$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
			$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . JFactory::getApplication ()->getCfg ( 'sitename' ));

			$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;
			$this->document->setMetadata ( 'keywords', $metaKeys );
			$this->document->setDescription ( $metaDesc );

			$this->setTitle ( JText::_('COM_KUNENA_VIEW_CATEGORIES_USER') );
			$this->display ($tpl);
		}
	}

	function displayManage($tpl) {
		$admin = KunenaForumCategoryHelper::getCategories(false, false, 'admin');
		if (empty($admin)) {
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
			$this->displayNoAccess($this->getErrors());
			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena',JPATH_ADMINISTRATOR);

		$this->assignRef ( 'categories', $this->get ( 'AdminCategories' ) );
		$this->assignRef ( 'navigation', $this->get ( 'AdminNavigation' ) );
		$header = JText::_('COM_KUNENA_ADMIN');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );

		$this->display ($tpl);
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

	function getCategoryLink($category, $content = null) {
		if (!$content) $content = $this->escape($category->name);
		return JHTML::_('kunenaforum.link', "index.php?option=com_kunena&view=category&catid={$category->id}", $content, $this->escape($category->name), '', 'follow');
	}
	function getTopicLink($topic, $action, $content = null, $title = null, $class = null) {
		$uri = JURI::getInstance("index.php?option=com_kunena&view=topic&id={$topic->id}&action={$action}");
		if ($uri->getVar('action') !== null) {
			$uri->delVar('action');
			$uri->setVar('catid', $this->category->id);
			/*if ($this->Itemid) {
				$uri->setVar('Itemid', $this->Itemid);
			}*/
			$limit = max(1, $this->config->messages_per_page);
			if (is_numeric($action)) {
				if ($action) $uri->setVar('limitstart', $action * $limit);
			} else {
				switch ($action) {
					case 'first':
						$position = $topic->getPostLocation($topic->first_post_id, $this->topic_ordering);
						$uri->setFragment($topic->first_post_id);
						break;
					case 'last':
						$position = $topic->getPostLocation($topic->last_post_id, $this->topic_ordering);
						$uri->setFragment($topic->last_post_id);
						break;
					case 'unread':
						$lastread = $topic->lastread ? $topic->lastread : $topic->last_post_id;
						$position = $topic->getPostLocation($lastread, $this->topic_ordering);
						$uri->setFragment($lastread);
						break;
				}
			}
			if (isset($position)) {
				$limitstart = intval($position / $limit) * $limit;
				if ($limitstart) $uri->setVar('limitstart', $limitstart);
			}
		}
		if (!$content) $content = KunenaHtmlParser::parseText($topic->subject);
		if ($title === null) $title = $this->escape($topic->subject);
		return JHTML::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}

	public function getCategoryIcon($category, $thumb = false) {
		if (! $thumb) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if ($category->getNewCount()) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_on.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_on.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kunreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_off.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_off.gif\" border=\"0\" class='kforum-cat-image' alt=\" \"  />";
					} else {
						return $this->getIcon ( 'kreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_notlogin.gif" )) {
					return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_notlogin.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( 'knotloginforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		} elseif ($this->config->showchildcaticon) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if ($category->getNewCount()) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_on_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kunreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_off_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				// Not Login Cat Images
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_notlogin_childsmall.gif" )) {
					return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( 'knotloginforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		}
		return '';
	}

	function displayInfoMessage() {
		$this->common->header = $this->escape($this->category->name);
		$this->common->body = '<p>'.JText::sprintf('COM_KUNENA_VIEW_CATEGORIES_INFO_EMPTY', $this->escape($this->category->name)).'</p>';
		$this->common->html = true;
		echo $this->common->display('default');
	}

	function displayCategories() {
		if ($this->sections) {
			$this->subcategories = true;
			echo $this->loadTemplate('subcategories');
		}
	}

	function displayRows() {
		$lasttopic = NULL;
		$this->position = 0;
		foreach ( $this->topics as $this->topic ) {
			$this->position++;
			$this->keywords = $this->topic->getKeywords(false, ', ');
			$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
			$this->message_position = $this->topic->getTotal() - ($this->topic->unread ? $this->topic->unread - 1 : 0);
			$this->pages = ceil ( $this->topic->getTotal() / $this->config->messages_per_page );
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