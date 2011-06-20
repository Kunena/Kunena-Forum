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
kimport ( 'kunena.html.pagination' );

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

		$this->assignRef ( 'message_ordering', $this->get ( 'MessageOrdering' ) );
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->assignRef ( 'pending',  $this->get ( 'UnapprovedCount' ) );
		$this->sections = isset($this->categories[0]) ? $this->categories[0] : array();

		$this->headerText = $this->title = JText::_('COM_KUNENA_THREADS_IN_FORUM').': '. $this->category->name;

		// Is user allowed to post new topic?
		$this->newTopicHtml = '';
		if ($this->category->getNewTopicCategory()->exists()) {
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

	function displayList($tpl = null) {
		$this->Itemid = $this->get ( 'Itemid' );
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if ($this->category->id && ! $this->category->authorise('read')) {
			$this->setError($this->category->getError());
		}
		$this->assignRef ( 'message_ordering', $this->get ( 'MessageOrdering' ) );
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->assignRef ( 'pending',  $this->get ( 'UnapprovedCount' ) );
		$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );
		$this->sections = isset($this->categories[0]) ? $this->categories[0] : array();

		if ($this->category->isSection()) {
// TODO: turn this on:
/*			if ($this->me->isAdmin(null)) {
				$this->category_manage = CKunenaLink::GetHrefLink(KunenaRoute::_('index.php?option=com_kunena&view=category&layout=manage&catid='.$this->category->id), $this->getButton ( 'moderate', JText::_('COM_KUNENA_BUTTON_MANAGE_CATEGORIES') ), $title = '', 'nofollow', 'kicon-button kbuttonmod btn-left', '', JText::_('COM_KUNENA_BUTTON_MANAGE_CATEGORIES_LONG'));
			}*/
		}
		if ($this->me->exists()) {
			$this->markAllReadURL = KunenaRoute::_();
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
		$this->app = JFactory::getApplication();

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

		$this->assignRef ( 'options', $this->get ( 'AdminOptions' ) );
		$this->assignRef ( 'moderators', $this->get ( 'AdminModerators' ) );
		$header = $this->category->exists() ? JText::sprintf('COM_KUNENA_CATEGORY_EDIT', $this->escape($this->category->name)) : JText::_('COM_KUNENA_CATEGORY_NEW');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );

		$this->display ();
	}

	function getLastPostUrl($category) {
		$lastPost = $category->getLastPosted();
		$channels = $category->getChannels();
		if (isset($channels[$lastPost->id])) $catid = $category->id;
		else $catid = $lastPost->id;

		$limit = $this->config->messages_per_page;
		$limitstart = intval($lastPost->getLastPostLocation() / $limit) * $limit;
		$anker = '';
		$query = array();
		$mesid = $lastPost->last_post_id;
		if ($mesid) {
			$layout = $this->me->getTopicLayout();
			if ($layout == 'threaded') $query[] = "&mesid={$mesid}";
			else $anker = '#'.$mesid;
		}
		if ($limitstart) {
			$query[] = "&limitstart={$limitstart}";
			$query[] = "&limit={$limit}";
		}
		$query = implode('', $query);
		return "index.php?option=com_kunena&view=topic&catid={$catid}&id={$lastPost->last_topic_id}{$query}{$anker}";
	}

	function getLastPostLink($category, $content = null, $title = null, $class = null) {
		$uri = $this->getLastPostUrl($category);
		if (!$content) $content = KunenaHtmlParser::parseText($category->getLastPosted()->last_topic_subject, 20);
		if ($title === null) $title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($category->getLastPosted()->last_topic_subject));
		return JHTML::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}

	public function getCategoryIcon($category, $thumb = false) {
		if (! $thumb) {
			if ($category->getNewCount()) {
				// Check Unread    Cat Images
				if (is_file ( JPATH_ROOT."/media/kunena/{$this->config->catimagepath}/{$category->id}_on.gif" )) {
					return "<img src=\"" . JURI::root(true) . "/media/kunena/{$this->config->catimagepath}/{$category->id}_on.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( $this->template->categoryIcons[1], JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
				}
			} else {
				// Check Read Cat Images
				if (is_file ( JPATH_ROOT."/media/kunena/{$this->config->catimagepath}/{$category->id}_off.gif" )) {
					return "<img src=\"" . JURI::root(true) . "/media/kunena/{$this->config->catimagepath}/{$category->id}_off.gif\" border=\"0\" class='kforum-cat-image' alt=\" \"  />";
				} else {
					return $this->getIcon ( $this->template->categoryIcons[0], JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		} elseif ($this->config->showchildcaticon) {
			if ($category->getNewCount()) {
				// Check Unread    Cat Images
				if (is_file ( JPATH_ROOT."/media/kunena/{$this->config->catimagepath}/{$category->id}_on_childsmall.gif" )) {
					return "<img src=\"" . JURI::root(true) . "/media/kunena/{$this->config->catimagepath}/{$category->id}_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( $this->template->categoryIcons[1].'-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
				}
			} else {
				// Check Read Cat Images
				if (is_file ( JPATH_ROOT."/media/kunena/{$this->config->catimagepath}/{$category->id}_off_childsmall.gif" )) {
					return "<img src=\"" . JURI::root(true) . "/media/kunena/{$this->config->catimagepath}/{$category->id}_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( $this->template->categoryIcons[0].'-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
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

	function displaySection($section) {
		$this->parentcategory = $this->category;
		$this->section = $section;
		$this->sectionURL = KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$this->section->id}");
		$this->sectionRssURL = $this->config->enablerss ? KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$this->section->id}&format=feed") : '';
		$this->sectionMarkReadURL = $this->me->exists() ? KunenaRoute::_("index.php?option=com_kunena&view=category&task=markread&catid={$this->section->id}") : '';
		echo $this->loadTemplate('section');
		$this->rowno = 0;
		$this->category = $this->parentcategory;
	}

	function displayCategory($category) {
		$this->rowno++;
		$this->category = $category;

		$usertype = $this->me->getType($this->category->id, true);
		$catid = $category->id;
		$lastPost = $category->getLastPosted();

		// TODO: add context (options, template) to caching
		$this->cache = true;
		$cache = JFactory::getCache('com_kunena', 'output');
		$cachekey = "list.item.{$this->getTemplateMD5()}.{$usertype}.{$catid}.{$lastPost->last_post_id}";
		$cachegroup = 'com_kunena.category';

		$contents = $cache->get($cachekey, $cachegroup);
		if (!$contents) {
			$this->categoryURL = KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}");
			$this->categoryRssURL = $this->config->enablerss ? KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}&format=feed") : '';
			$this->moderators = $this->config->listcat_show_moderators ? $category->getModerators(false) : array();
			$this->subcategories = empty($this->categories [$catid]) ? array() : $this->categories [$catid];
			$this->lastPost = $lastPost->last_post_id > 0;
			if ($this->lastPost) {
				$this->lastUser = KunenaFactory::getUser((int) $lastPost->last_post_userid);
				$this->lastUserName = $lastPost->last_post_guest_name;
				$this->lastPostSubject = $lastPost->last_topic_subject;
				$this->lastPostTime = $lastPost->last_post_time;
			}
			$contents = $this->loadTemplate('category');
			if ($usertype == 'guest') $contents = preg_replace_callback('|\[K=(\w+)(?:\:(\w+))?\]|', array($this, 'fillCategoryInfo'), $contents);
			if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
		} elseif ($usertype == 'guest') {
			echo $contents;
			return;
		}
		$contents = preg_replace_callback('|\[K=(\w+)(?:\:(\w+))?\]|', array($this, 'fillCategoryInfo'), $contents);
		echo $contents;
	}

	function fillCategoryInfo($matches) {
		switch ($matches[1]) {
			case 'ROW':
				return $this->rowno & 1 ? 'odd' : 'even';
			case 'CATEGORY_ICON':
				return $this->getCategoryIcon($this->category);
			case 'CATEGORY_NEW_SUFFIX':
				$new = empty($matches[2]) ? $this->category->getNewCount() : KunenaForumCategoryHelper::get($matches[2])->getNewCount();
				return $new ? '-new' : '';
			case 'CATEGORY_NEW_COUNT':
				$new = empty($matches[2]) ? $this->category->getNewCount() : KunenaForumCategoryHelper::get($matches[2])->getNewCount();
				return $new ? '<sup class="knewchar">(' . $new . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ")</sup>" : '';
			case 'DATE':
				$date = new KunenaDate($matches[2]);
				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
		}
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
			$usertype = $this->me->getType($this->category->id, true);

			// TODO: add context (options, template) to caching
			$this->cache = true;
			$cache = JFactory::getCache('com_kunena', 'output');
			$cachekey = "{$this->getTemplateMD5()}.{$usertype}.c{$this->category->id}.t{$this->topic->id}.p{$this->topic->last_post_id}";
			$cachegroup = 'com_kunena.topics';

			$contents = $cache->get($cachekey, $cachegroup);
			if (!$contents) {
				if ($this->category->id != $this->topic->category_id) {
					$this->categoryLink = $this->getCategoryLink($this->topic->getCategory()->getParent()) . ' / ' . $this->getCategoryLink($this->topic->getCategory());
				} else {
					$this->categoryLink = null;
				}
				$this->firstPostAuthor = $this->topic->getfirstPostAuthor();
				$this->firstPostTime = $this->topic->first_post_time;
				$this->firstUserName = $this->topic->first_post_guest_name;
				$this->lastPostAuthor = $this->topic->getLastPostAuthor();
				$this->lastPostTime = $this->topic->last_post_time;
				$this->lastUserName = $this->topic->last_post_guest_name;
				$this->keywords = $this->topic->getKeywords(false, ', ');
				$this->module = $this->getModulePosition('kunena_topic_' . $this->position);
				$this->message_position = $this->topic->getTotal() - ($this->topic->unread ? $this->topic->unread - 1 : 0);
				$this->pages = ceil ( $this->topic->getTotal() / $this->config->messages_per_page );
				if ($this->config->avataroncat) {
					$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarImage('klist-avatar', 'list');
				}

				if (is_object($lasttopic) && $lasttopic->ordering != $this->topic->ordering) {
					$this->spacing = 1;
				} else {
					$this->spacing = 0;
				}
				$contents = $this->loadTemplate('row');
				if ($usertype == 'guest') $contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
				if ($this->cache) $cache->store($contents, $cachekey, $cachegroup);
			}
			if ($usertype != 'guest') {
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
			}
			echo $contents;
			$lasttopic = $this->topic;
		}
	}

	function fillTopicInfo($matches) {
		switch ($matches[1]) {
			case 'ROW':
				return $matches[2].($this->position & 1 ? 'odd' : 'even').($this->topic->ordering ? " {$matches[2]}sticky" : '');
			case 'TOPIC_ICON':
				return $this->getTopicLink ( $this->topic, 'unread', $this->topic->getIcon() );
			case 'TOPIC_NEW_COUNT':
				return $this->topic->unread ? $this->getTopicLink ( $this->topic, 'unread', '<sup class="kindicator-new">(' . $this->topic->unread . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>' ) : '';
			case 'DATE':
				$date = new KunenaDate($matches[2]);
				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
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
		$pagination = new KunenaHtmlPagination ( $this->total, $this->state->get('list.start'), $this->state->get('list.limit') );
		$pagination->setDisplay($maxpages);
		return $pagination->getPagesLinks();
	}
}