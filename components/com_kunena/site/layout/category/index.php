<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Category.Index
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutCategoryIndex extends KunenaLayout
{
	public function getPaginationObject($maxpages) {
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}

	public function getCategoryIcon($category, $thumb = false) {
		$path	= JPATH_ROOT . '/media/kunena/' . $this->config->catimagepath . '/';
		$uri	= JUri::root(true) . '/media/kunena/' . $this->config->catimagepath . '/';

		if (!$thumb) {
			if ($category->getNewCount()) {
				// Check Unread Cat Images
				$file = $this->getCategoryIconFile($category->id . '_on', $path);
				if ($file) {
					return '<img src="' . $uri . $file .'" border="0" class="kforum-cat-image" alt=" " />';
				}
				return $this->getIcon ( $this->ktemplate->categoryIcons[1], JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
			}
			else {
				// Check Read Cat Images
				$file = $this->getCategoryIconFile($category->id . '_off', $path);
				if ($file) {
					return '<img src="' . $uri . $file .'" border="0" class="kforum-cat-image" alt=" " />';
				}
				return $this->getIcon ( $this->ktemplate->categoryIcons[0], JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
			}
		}
		elseif ($this->config->showchildcaticon) {
			if ($category->getNewCount()) {
				// Check Unread Cat Images
				$file = $this->getCategoryIconFile($category->id . '_on_childsmall', $path);
				if ($file) {
					return '<img src="' . $uri . $file .'" border="0" class="kforum-cat-image" alt=" " />';
				}
				return $this->getIcon ( $this->ktemplate->categoryIcons[1].'-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
			}
			else {
				// Check Read Cat Images
				$file = $this->getCategoryIconFile($category->id . '_off_childsmall', $path);
				if ($file) {
					return '<img src="' . $uri . $file .'" border="0" class="kforum-cat-image" alt=" " />';
				}
				return $this->getIcon ( $this->ktemplate->categoryIcons[0].'-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
			}
		}
		return '';
	}

	private function getCategoryIconFile($filename, $path = '') {
		$types	= array('.gif', '.png', '.jpg');

		foreach ($types as $ext) {
			if (is_file($path . $filename . $ext)) {
				return $filename . $ext;
			}
		}

		return false;
	}

	public function getMarkReadButtonURL($category_id, $numTopics) {
		// Is user allowed to mark forums as read?
		if (KunenaUserHelper::getMyself()->exists() && $numTopics) {
			$token = '&' . JSession::getFormToken() . '=1';

			$url = KunenaRoute::_("index.php?option=com_kunena&view=category&task=markread&catid={$category_id}{$token}");

			return $url;
		}

		return null;
	}

	public function getCategoryRSSURL($catid, $xhtml = true) {
		if (KunenaConfig::getInstance()->enablerss) {
			$params = '&catid=' . (int) $catid;
			return KunenaRoute::_ ( "index.php?option=com_kunena&view=topics&format=feed&layout=default&mode=topics{$params}", $xhtml );
		}

		return null;
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
}
