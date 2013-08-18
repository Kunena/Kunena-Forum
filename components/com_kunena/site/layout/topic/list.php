<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutTopicList extends KunenaLayout
{
	function displayRows() {
		$lasttopic = NULL;
		$this->position = 0;

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'user');
		$params->set('kunena_layout', 'topics');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array ('kunena.topics', &$this->topics, &$params, 0));

		foreach ( $this->topics as $this->topic ) {
			$this->position++;
			$this->category = $this->topic->getCategory();

			$this->categoryLink = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
			$this->firstPostAuthor = $this->topic->getfirstPostAuthor();
			$this->firstPostTime = $this->topic->first_post_time;
			$this->firstUserName = $this->topic->first_post_guest_name;
			$this->lastPostAuthor = $this->topic->getLastPostAuthor();
			$this->lastPostTime = $this->topic->last_post_time;
			$this->lastUserName = $this->topic->last_post_guest_name;
			$this->keywords = $this->topic->getKeywords(false, ', ');
			$this->message_position = $this->topic->posts - ($this->topic->unread ? $this->topic->unread - 1 : 0);
			$this->pages = ceil ( $this->topic->getTotal() / $this->config->messages_per_page );
			if ($this->config->avataroncat) {
				$this->topic->avatar = KunenaFactory::getUser($this->topic->last_post_userid)->getAvatarImage('klist-avatar', 'list');
			}

			if (is_object($lasttopic) && $lasttopic->ordering != $this->topic->ordering) {
				$this->spacing = 1;
			} else {
				$this->spacing = 0;
			}
			$contents = $this->subLayout('Topic/List/Row')->setProperties($this->getProperties())->setLayout($this->getLayout());

			echo $contents;

			$lasttopic = $this->topic;
		}
	}

	function getPagination($maxpages) {
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);
		return $pagination->getPagesLinks();
	}

	function displayTimeFilter($id = 'kfilter-select-time', $attrib = 'class="kinputbox" onchange="this.form.submit()" size="1"') {
		// make the select list for time selection
		$timesel[] = JHtml::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
		$timesel[] = JHtml::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
		$timesel[] = JHtml::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
		$timesel[] = JHtml::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
		$timesel[] = JHtml::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
		$timesel[] = JHtml::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
		$timesel[] = JHtml::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
		$timesel[] = JHtml::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
		$timesel[] = JHtml::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
		$timesel[] = JHtml::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
		echo JHtml::_('select.genericlist', $timesel, 'sel', $attrib, 'value', 'text', $this->state->get('list.time'), $id);
	}
}
