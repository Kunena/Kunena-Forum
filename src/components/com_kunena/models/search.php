<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Search Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelSearch extends KunenaModel
{
	protected $error = null;

	protected $total = false;

	protected $messages = false;

	/**
	 * @throws Exception
	 */
	protected function populateState()
	{
		// Get search word list
		$value = Joomla\String\StringHelper::trim($this->app->input->get('query', '', 'string'));

		if (empty($value))
		{
			$value = Joomla\String\StringHelper::trim($this->app->input->get('q', '', 'string'));
		}

		if ($value == JText::_('COM_KUNENA_GEN_SEARCH_BOX'))
		{
			$value = '';
		}

		$this->setState('searchwords', $value);

		$value = JFactory::getApplication()->input->getInt('titleonly', 0);
		$this->setState('query.titleonly', $value);

		$value = JFactory::getApplication()->input->getString('searchuser', '');
		$this->setState('query.searchuser', rtrim($value));

		$value = JFactory::getApplication()->input->getInt('starteronly', 0);
		$this->setState('query.starteronly', $value);

		if (!$this->config->pubprofile && !JFactory::getUser()->guest || $this->config->pubprofile)
		{
			$value = JFactory::getApplication()->input->getInt('exactname', 0);
			$this->setState('query.exactname', $value);
		}

		$value = JFactory::getApplication()->input->getInt('replyless', 0);
		$this->setState('query.replyless', $value);

		$value = JFactory::getApplication()->input->getInt('replylimit', 0);
		$this->setState('query.replylimit', $value);

		$value = JFactory::getApplication()->input->getString('searchdate', $this->config->searchtime);
		$this->setState('query.searchdate', $value);

		$value = JFactory::getApplication()->input->getString('searchatdate', null);
		$this->setState('query.searchatdate', $value);

		$value = JFactory::getApplication()->input->getWord('beforeafter', 'after');
		$this->setState('query.beforeafter', $value);

		$value = JFactory::getApplication()->input->getWord('sortby', 'lastpost');
		$this->setState('query.sortby', $value);

		$value = JFactory::getApplication()->input->getWord('order', 'dec');
		$this->setState('query.order', $value);

		$value = JFactory::getApplication()->input->getInt('childforums', 1);
		$this->setState('query.childforums', $value);

		$value = JFactory::getApplication()->input->getInt('topic_id', 0);
		$this->setState('query.topic_id', $value);

		if (isset($_POST ['query']) || isset($_POST ['searchword']))
		{
			$value = JFactory::getApplication()->input->get('catids', array(0), 'post', 'array');
			Joomla\Utilities\ArrayHelper::toInteger($value);
		}
		else
		{
			$value = JFactory::getApplication()->input->getString('catids', '0', 'get');
			$value = explode(' ', $value);
			Joomla\Utilities\ArrayHelper::toInteger($value);
		}

		$this->setState('query.catids', $value);

		if (isset($_POST ['q']) || isset($_POST ['searchword']))
		{
			$value = JFactory::getApplication()->input->get('ids', array(0), 'post', 'array');
			Joomla\Utilities\ArrayHelper::toInteger($value);

			if ($value[0] > 0)
			{
				$this->setState('query.ids', $value);
			}
		}
		else
		{
			$value = JFactory::getApplication()->input->getString('ids', '0', 'get');
			Joomla\Utilities\ArrayHelper::toInteger($value);

			if ($value[0] > 0)
			{
				$this->setState('query.ids', $value);
			}
		}


		$value = JFactory::getApplication()->input->getInt('show', 0);
		$this->setState('query.show', $value);

		$value = $this->getInt('limitstart', 0);

		if ($value < 0)
		{
			$value = 0;
		}

		$this->setState('list.start', $value);

		$value = $this->getInt('limit', 0);

		if ($value < 1 || $value > 100)
		{
			$value = $this->config->messages_per_page_search;
		}

		$this->setState('list.limit', $value);
	}

	/**
	 * @return string
	 */
	protected function buildWhere()
	{
		$db           = JFactory::getDBO();
		$querystrings = array();

		foreach ($this->getSearchWords() as $searchword)
		{
			$searchword = $db->escape(Joomla\String\StringHelper::trim($searchword));

			if (empty($searchword))
			{
				continue;
			}

			$not      = '';
			$operator = ' OR ';

			if (substr($searchword, 0, 1) == '-' && strlen($searchword) > 1)
			{
				$not        = 'NOT';
				$operator   = 'AND';
				$searchword = Joomla\String\StringHelper::substr($searchword, 1);
			}

			if (!$this->getState('query.titleonly'))
			{
				$querystrings [] = "(t.message {$not} LIKE '%{$searchword}%' {$operator} m.subject {$not} LIKE '%{$searchword}%')";
			}
			else
			{
				$querystrings [] = "(m.subject {$not} LIKE '%{$searchword}%')";
			}
		}

		if (!$this->config->pubprofile && !JFactory::getUser()->guest || $this->config->pubprofile)
		{
			// User searching
			$username = $this->getState('query.searchuser');

			if ($username)
			{
				if ($this->getState('query.exactname') == '1')
				{
					$querystrings [] = "m.name LIKE '" . $db->escape($username) . "'";
				}
				else
				{
					$querystrings [] = "m.name LIKE '%" . $db->escape($username) . "%'";
				}
			}
		}

		$time = 0;
		$searchatdate = $this->getState('query.searchatdate');

		if (empty($searchatdate) || $searchatdate==JFactory::getDate()->format('m/d/Y'))
		{
			switch ($this->getState('query.searchdate'))
			{
				case 'lastvisit' :
					$time = KunenaFactory::GetSession()->lasttime;
					break;
				case 'all' :
					break;
				case '1' :
				case '7' :
				case '14' :
				case '30' :
				case '90' :
				case '180' :
				case '365' :
					$time = time() - 86400 * intval($this->getState('query.searchdate'));
					break;
				default :
					$time = time() - 86400 * 365;
			}

			if ($time)
			{
				if ($this->getState('query.beforeafter') == 'after')
				{
					$querystrings [] = "m.time > '{$time}'";
				}
				else
				{
					$querystrings [] = "m.time <= '{$time}'";
				}
			}
		}
		else
		{
			$time_start_day = JFactory::getDate($this->getState('query.searchatdate'))->toUnix();
			$time_end_day = new DateTime($this->getState('query.searchatdate'));
			$time_end_day->add(new DateInterval("PT23H59M59S"));

			$querystrings[] = " m.time > {$time_start_day} AND m.time < {$time_end_day->getTimestamp()}";
		}

		$topic_id = $this->getState('query.topic_id');

		if ($topic_id)
		{
			$querystrings [] = "m.id = '{$topic_id}'";
		}

		return implode(' AND ', $querystrings);
	}

	/**
	 * @return string
	 */
	protected function buildOrderBy()
	{
		if ($this->getState('query.order') == 'dec')
		{
			$order1 = 'DESC';
		}
		else
		{
			$order1 = 'ASC';
		}

		switch ($this->getState('query.sortby'))
		{
			case 'title' :
				$orderby = "m.subject {$order1}, m.time {$order1}";
				break;
			case 'views' :
				$orderby = "m.hits {$order1}, m.time {$order1}";
				break;
			case 'forum' :
				$orderby = "m.catid {$order1}, m.time {$order1}";
				break;
			case 'lastpost' :
			default :
				$orderby = "m.time {$order1}";
		}

		return $orderby;
	}

	/**
	 * @return boolean|integer
	 */
	public function getTotal()
	{
		$text = $this->getState('searchwords');
		$q = strlen($text);

		if ($q < 3 && !$this->getState('query.searchuser') && JFactory::getApplication()->input->getString('childforums'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD'), 'error');

			return 0;
		}

		if ($this->total === false)
		{
			$this->getResults();
		}

		// If there are no forums to search in, set error and return

		if ($this->total == 0)
		{
			$this->setError(JText::_('COM_KUNENA_SEARCH_ERR_NOPOSTS'));

			return 0;
		}

		return $this->total;
	}

	/**
	 * @return array
	 */
	public function getSearchWords()
	{
		// Accept individual words and quoted strings
		$splitPattern = '/[\s,]*\'([^\']+)\'[\s,]*|[\s,]*"([^"]+)"[\s,]*|[\s,]+/u';
		$searchwords  = preg_split($splitPattern, $this->getState('searchwords'), 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$result = array();

		foreach ($searchwords as $word)
		{
			// Do not accept one letter strings
			if (Joomla\String\StringHelper::strlen($word) > 1)
			{
				$result [] = $word;
			}
		}

		return $result;
	}

	/**
	 * @return boolean
	 */
	public function getResults()
	{
		if ($this->messages !== false)
		{
			return $this->messages;
		}

		$text = $this->getState('searchwords');
		$q = strlen($text);

		if (!$this->getState('query.searchuser'))
		{
			if ($q < 3)
			{
				return false;
			}
		}

		// Get results

		$hold = $this->getState('query.show');

		if ($hold == 1)
		{
			$mode = 'unapproved';
		}
		elseif ($hold >= 2)
		{
			$mode = 'deleted';
		}
		else
		{
			$mode = 'recent';
		}

		$params     = array(
			'mode'        => $mode,
			'childforums' => $this->getState('query.childforums'),
			'where'       => $this->buildWhere(),
			'orderby'     => $this->buildOrderBy(),
			'starttime'   => -1
		);
		$limitstart = $this->getState('list.start');
		$limit      = $this->getState('list.limit');
		list($this->total, $this->messages) = KunenaForumMessageHelper::getLatestMessages($this->getState('query.catids'), $limitstart, $limit, $params);

		if ($this->total < $limitstart)
		{
			$this->setState('list.start', intval($this->total / $limit) * $limit);
		}

		$topicids = array();
		$userids  = array();

		foreach ($this->messages as $message)
		{
			$topicids[$message->thread] = $message->thread;
			$userids[$message->userid]  = $message->userid;
		}

		if ($topicids)
		{
			$topics = KunenaForumTopicHelper::getTopics($topicids);

			foreach ($topics as $topic)
			{
				$userids[$topic->first_post_userid] = $topic->first_post_userid;
			}
		}

		KunenaUserHelper::loadUsers($userids);
		KunenaForumMessageHelper::loadLocation($this->messages);

		if (empty($this->messages))
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SEARCH_NORESULTS_FOUND', '<strong>' . $text . '</strong>'));
		}

		return $this->messages;
	}

	/**
	 * @return string
	 */
	public function getUrlParams()
	{
		// Turn internal state into URL, but ignore default values
		$defaults = array('titleonly' => 0, 'searchuser' => '', 'exactname' => 0, 'childforums' => 0, 'starteronly' => 0,
							'replyless' => 0, 'replylimit' => 0, 'searchdate' => '365', 'beforeafter' => 'after', 'sortby' => 'lastpost',
							'order'     => 'dec', 'catids' => '0', 'show' => '0', 'topic_id' => 0, 'ids' => 0, 'searchatdate' => '');

		$url_params = '';
		$state      = $this->getState();

		foreach ($state as $param => $value)
		{
			$paramparts = explode('.', $param);

			if ($paramparts[0] != 'query')
			{
				continue;
			}

			$param = $paramparts[1];

			if ($param == 'catids' || $param == 'ids')
			{
				$value = implode(' ', $value);
			}

			if ($value != $defaults [$param])
			{
				$url_params .= "&$param=" . urlencode($value);
			}
		}

		return $url_params;
	}

	/**
	 * @param        $view
	 * @param   string $searchword
	 * @param   int    $limitstart
	 * @param   int    $limit
	 * @param   string $params
	 * @param   bool   $xhtml
	 *
	 * @return boolean
	 */
	public function getSearchURL($view, $searchword = '', $limitstart = 0, $limit = 0, $params = '', $xhtml = true)
	{
		$config   = KunenaFactory::getConfig();
		$limitstr = "";

		if ($limitstart > 0)
		{
			$limitstr .= "&limitstart=$limitstart";
		}

		if ($limit > 0 && $limit != $config->messages_per_page_search)
		{
			$limitstr .= "&limit=$limit";
		}

		if ($searchword)
		{
			$searchword = '&query=' . urlencode($searchword);
		}

		return KunenaRoute::_("index.php?option=com_kunena&view={$view}{$searchword}{$params}{$limitstr}", $xhtml);
	}
}
