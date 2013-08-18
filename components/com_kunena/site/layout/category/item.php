<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Category.Item
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutCategoryItem extends KunenaLayout
{
	function displayCategories() {
		if ($this->sections) {
			$this->subcategories = true;
			echo $this->subLayout('Category/Index')->setProperties($this->getProperties())->setLayout('subcategories');
		}
	}

	function displayCategoryActions() {
		if (!$this->category->isSection()) {
			echo $this->subLayout('Category/Item/Actions')->setProperties($this->getProperties());
		}
	}

	function displayRows() {
		$lasttopic = NULL;
		$this->position = 0;

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'category');
		$params->set('kunena_layout', 'default');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.topics', &$this->topics, &$params, 0));

		foreach ( $this->topics as $this->topic ) {
			$this->position++;
			$usertype = $this->me->getType($this->category->id, true);

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
			$contents = $this->subLayout('Category/Item/Row')->setProperties($this->getProperties());
			if ($usertype == 'guest') $contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);

			if ($usertype != 'guest') {
				$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
			}
			echo $contents;
			$lasttopic = $this->topic;
		}
	}

	function getLastPostLink($category, $content = null, $title = null, $class = null) {
		$lastTopic = $category->getLastTopic();
		$channels = $category->getChannels();
		if (!isset($channels[$lastTopic->category_id])) $category = $lastTopic->getCategory();
		$uri = $lastTopic->getUri($category, 'last');

		if (!$content) $content = KunenaHtmlParser::parseText($category->getLastTopic()->subject, 20);
		if ($title === null) $title = JText::sprintf('COM_KUNENA_TOPIC_LAST_LINK_TITLE', $this->escape($category->getLastTopic()->subject));
		return JHtml::_('kunenaforum.link', $uri, $content, $title, $class, 'nofollow');
	}

	function getPagination($maxpages) {
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);
		return $pagination->getPagesLinks();
	}
}
