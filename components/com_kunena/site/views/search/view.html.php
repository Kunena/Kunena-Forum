<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Search View
 */
class KunenaViewSearch extends KunenaView
{
	function displayDefault($tpl = null)
	{
		$this->message_ordering = $this->me->getMessageOrdering();
		//TODO: Need to move the select markup outside of view.  Otherwise difficult to stylize

		$this->searchwords = $this->get('SearchWords');
		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus($this->me));

		$this->results = array();
		$this->total   = $this->get('Total');

		if ($this->total)
		{
			$this->results      = $this->get('Results');
			$this->search_class = ' open';
			$this->search_style = ' style="display: none;"';
			$this->search_title = JText::_('COM_KUNENA_TOGGLER_EXPAND');
		}
		else
		{
			$this->search_class = ' close';
			$this->search_style = '';
			$this->search_title = JText::_('COM_KUNENA_TOGGLER_COLLAPSE');
		}

		$this->selected = ' selected="selected"';
		$this->checked  = ' checked="checked"';
		$this->error    = $this->get('Error');

		$this->_prepareDocument();

		$this->render('Search', $tpl);
	}

	function displaySearchResults()
	{
		if ($this->results)
		{
			echo $this->loadTemplateFile('results');
		}
	}

	function displayModeList($id, $attributes = '')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_SEARCH_SEARCH_POSTS'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_SEARCH_SEARCH_TITLES'));
		echo JHtml::_('select.genericlist', $options, 'titleonly', $attributes, 'value', 'text', $this->state->get('query.titleonly'), $id);
	}

	function displayDateList($id, $attributes = '')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'lastvisit', JText::_('COM_KUNENA_SEARCH_DATE_LASTVISIT'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_SEARCH_DATE_YESTERDAY'));
		$options[] = JHtml::_('select.option', '7', JText::_('COM_KUNENA_SEARCH_DATE_WEEK'));
		$options[] = JHtml::_('select.option', '14', JText::_('COM_KUNENA_SEARCH_DATE_2WEEKS'));
		$options[] = JHtml::_('select.option', '30', JText::_('COM_KUNENA_SEARCH_DATE_MONTH'));
		$options[] = JHtml::_('select.option', '90', JText::_('COM_KUNENA_SEARCH_DATE_3MONTHS'));
		$options[] = JHtml::_('select.option', '180', JText::_('COM_KUNENA_SEARCH_DATE_6MONTHS'));
		$options[] = JHtml::_('select.option', '365', JText::_('COM_KUNENA_SEARCH_DATE_YEAR'));
		$options[] = JHtml::_('select.option', 'all', JText::_('COM_KUNENA_SEARCH_DATE_ANY'));
		echo JHtml::_('select.genericlist', $options, 'searchdate', $attributes, 'value', 'text', $this->state->get('query.searchdate'), $id);
	}

	function displayBeforeAfterList($id, $attributes = '')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'after', JText::_('COM_KUNENA_SEARCH_DATE_NEWER'));
		$options[] = JHtml::_('select.option', 'before', JText::_('COM_KUNENA_SEARCH_DATE_OLDER'));
		echo JHtml::_('select.genericlist', $options, 'beforeafter', $attributes, 'value', 'text', $this->state->get('query.beforeafter'), $id);
	}

	function displaySortByList($id, $attributes = '')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'title', JText::_('COM_KUNENA_SEARCH_SORTBY_TITLE'));
//		$options[]	= JHtml::_('select.option',  'replycount', JText::_('COM_KUNENA_SEARCH_SORTBY_POSTS') );
		$options[] = JHtml::_('select.option', 'views', JText::_('COM_KUNENA_SEARCH_SORTBY_VIEWS'));
//		$options[]	= JHtml::_('select.option',  'threadstart', JText::_('COM_KUNENA_SEARCH_SORTBY_START') );
		$options[] = JHtml::_('select.option', 'lastpost', JText::_('COM_KUNENA_SEARCH_SORTBY_POST'));
//		$options[]	= JHtml::_('select.option',  'postusername', JText::_('COM_KUNENA_SEARCH_SORTBY_USER') );
		$options[] = JHtml::_('select.option', 'forum', JText::_('COM_KUNENA_CATEGORY'));
		echo JHtml::_('select.genericlist', $options, 'sortby', $attributes, 'value', 'text', $this->state->get('query.sortby'), $id);
	}

	function displayOrderList($id, $attributes = '')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'inc', JText::_('COM_KUNENA_SEARCH_SORTBY_INC'));
		$options[] = JHtml::_('select.option', 'dec', JText::_('COM_KUNENA_SEARCH_SORTBY_DEC'));
		echo JHtml::_('select.genericlist', $options, 'order', $attributes, 'value', 'text', $this->state->get('query.order'), $id);
	}

	function displayLimitList($id, $attributes = '')
	{
		// Limit value list
		$options   = array();
		$options[] = JHtml::_('select.option', '5', JText::_('COM_KUNENA_SEARCH_LIMIT5'));
		$options[] = JHtml::_('select.option', '10', JText::_('COM_KUNENA_SEARCH_LIMIT10'));
		$options[] = JHtml::_('select.option', '15', JText::_('COM_KUNENA_SEARCH_LIMIT15'));
		$options[] = JHtml::_('select.option', '20', JText::_('COM_KUNENA_SEARCH_LIMIT20'));
		echo JHtml::_('select.genericlist', $options, 'limit', $attributes, 'value', 'text', $this->state->get('list.limit'), $id);
	}

	function displayCategoryList($id, $attributes = '')
	{
		//category select list
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_SEARCH_SEARCHIN_ALLCATS'));

		$cat_params = array('sections' => true);
		echo JHtml::_('kunenaforum.categorylist', 'catids[]', 0, $options, $cat_params, $attributes, 'value', 'text', $this->state->get('query.catids'), $id);
	}

	function displayRows()
	{
		$this->row(true);

		// Run events
		$params = new JRegistry();
		$params->set('ksource', 'kunena');
		$params->set('kunena_view', 'search');
		$params->set('kunena_layout', 'default');

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('kunena');

		$dispatcher->trigger('onKunenaPrepare', array('kunena.messages', &$this->results, &$params, 0));

		foreach ($this->results as $this->message)
		{
			$this->topic        = $this->message->getTopic();
			$this->category     = $this->message->getCategory();
			$this->categoryLink = $this->getCategoryLink($this->category->getParent()) . ' / ' . $this->getCategoryLink($this->category);
			$ressubject         = KunenaHtmlParser::parseText($this->message->subject);
			$resmessage         = $this->parse($this->message->message, 500, $this->message);

			$profile          = KunenaFactory::getUser((int) $this->message->userid);
			$this->useravatar = $profile->getAvatarImage('kavatar', 'post');

			foreach ($this->searchwords as $searchword)
			{
				if (empty ($searchword))
				{
					continue;
				}

				// FIXME: enable highlighting, but only after we can be sure that we do not break html
				//$ressubject = preg_replace("/" . preg_quote($searchword, '/') . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $ressubject);
				//$resmessage = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $resmessage );
			}

			$this->author      = $this->message->getAuthor();
			$this->topicAuthor = $this->topic->getAuthor();
			$this->topicTime   = $this->topic->first_post_time;
			$this->subjectHtml = $ressubject;
			$this->messageHtml = $resmessage;

			$contents = $this->loadTemplateFile('row');
			$contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w-_]+))?\]|', array($this, 'fillTopicInfo'), $contents);
			echo $contents;
		}
	}

	function fillTopicInfo($matches)
	{
		switch ($matches[1])
		{
			case 'ROW':
				return $matches[2] . $this->row() . ($this->topic->ordering ? " {$matches[2]}sticky" : '');
			case 'TOPIC_ICON':
				return $this->topic->getIcon();
			case 'DATE':
				$date = new KunenaDate($matches[2]);

				return $date->toSpan('config_post_dateformat', 'config_post_dateformat_hover');
		}
	}

	function getPaginationObject($maxpages)
	{
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}

	function getPagination($maxpages)
	{
		return $this->getPaginationObject($maxpages)->getPagesLinks();
	}

	protected function _prepareDocument()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
			}
		}
	}
}
