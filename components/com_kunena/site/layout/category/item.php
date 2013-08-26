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
