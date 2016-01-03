<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Models
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Search Model for Kunena
 *
 * @since        2.0
 */
class KunenaModelSearch extends KunenaModel
{
	protected $error = null;
	protected $total = false;
	protected $messages = false;

	protected function populateState()
	{
		// Get search word list
		$value = JString::trim($this->app->input->get('query', '', 'string'));

		if (empty($value))
		{
			$value = JString::trim($this->app->input->get('q', '', 'string'));
		}

		if ($value == JText::_('COM_KUNENA_GEN_SEARCH_BOX'))
		{
			$value = '';
		}

		$this->setState('searchwords', $value);

		$value = JRequest::getInt('titleonly', 0);
		$this->setState('query.titleonly', $value);

		$value = JRequest::getString('searchuser', '');
		$this->setState('query.searchuser', $value);

		$value = JRequest::getInt('starteronly', 0);
		$this->setState('query.starteronly', $value);

		$value = JRequest::getInt('exactname', 0);
		$this->setState('query.exactname', $value);

		$value = JRequest::getInt('replyless', 0);
		$this->setState('query.replyless', $value);

		$value = JRequest::getInt('replylimit', 0);
		$this->setState('query.replylimit', $value);

		$value = JRequest::getString('searchdate', $this->config->searchtime);
		$this->setState('query.searchdate', $value);

		$value = JRequest::getWord('beforeafter', 'after');
		$this->setState('query.beforeafter', $value);

		$value = JRequest::getWord('sortby', 'lastpost');
		$this->setState('query.sortby', $value);

		$value = JRequest::getWord('order', 'dec');
		$this->setState('query.order', $value);

		$value = JRequest::getInt('childforums', 1);
		$this->setState('query.childforums', $value);

		$value = JRequest::getInt('topic_id', 0);
		$this->setState('query.topic_id', $value);

		if (isset ($_POST ['query']) || isset ($_POST ['searchword']))
		{
			$value = JRequest::getVar('catids', array(0), 'post', 'array'); // Array of integers
			JArrayHelper::toInteger($value);
		}
		else
		{
			$value = JRequest::getString('catids', '0', 'get'); // String of integers
			$value = explode(' ', $value);
			JArrayHelper::toInteger($value);
		}

		$this->setState('query.catids', $value);

		// FIXME: support search topic
		if (isset ($_POST ['q']) || isset ($_POST ['searchword']))
		{
			$value = JRequest::getVar('ids', array(0), 'post', 'array');
			JArrayHelper::toInteger($value);
		}
		else
		{
			$value = JRequest::getString('ids', '0', 'get');
			$value = explode(' ', $value);
			JArrayHelper::toInteger($value);
		}

		$this->setState('query.ids', $value);

		$value = JRequest::getInt('show', 0);
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

	protected function buildWhere()
	{
		$db           = JFactory::getDBO();
		$querystrings = array();

		foreach ($this->getSearchWords() as $searchword)
		{
			$searchword = $db->escape(JString::trim($searchword));

			if (empty ($searchword))
			{
				continue;
			}

			$not      = '';
			$operator = ' OR ';

			if (substr($searchword, 0, 1) == '-' && strlen($searchword) > 1)
			{
				$not        = 'NOT';
				$operator   = 'AND';
				$searchword = JString::substr($searchword, 1);
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

		//User searching
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

		$time = 0;

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
				$time = time() - 86400 * intval($this->getState('query.searchdate')); //24*3600
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

		$topic_id = $this->getState('query.topic_id');

		if ($topic_id)
		{
			$querystrings [] = "m.id = '{$topic_id}'";
		}

		return implode(' AND ', $querystrings);
	}

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

	public function getTotal()
	{
		$q = $this->getState('searchwords');

		if (!$q && !$this->getState('query.searchuser'))
		{
			$this->setError(JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD'));

			return 0;
		}

		if ($this->total === false)
		{
			$this->getResults();
		}

		/* if there are no forums to search in, set error and return */
		if ($this->total == 0)
		{
			$this->setError(JText::_('COM_KUNENA_SEARCH_ERR_NOPOSTS'));

			return 0;
		}

		return $this->total;
	}

	public function getSearchWords()
	{
		// Accept individual words and quoted strings
		$splitPattern = '/[\s,]*\'([^\']+)\'[\s,]*|[\s,]*"([^"]+)"[\s,]*|[\s,]+/u';
		$searchwords  = preg_split($splitPattern, $this->getState('searchwords'), 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$result = array();
		foreach ($searchwords as $word)
		{
			// Do not accept one letter strings
			if (JString::strlen($word) > 1)
			{
				$result [] = $word;
			}
		}

		return $result;
	}

	public function getResults()
	{
		if ($this->messages !== false)
		{
			return $this->messages;
		}

		$q = $this->getState('searchwords');

		if (!$q && !$this->getState('query.searchuser'))
		{
			$this->setError(JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD'));

			return array();
		}

		/* get results */
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
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SEARCH_NORESULTS_FOUND', $q));
		}

		return $this->messages;
	}

	public function getUrlParams()
	{
		// Turn internal state into URL, but ignore default values
		$defaults = array('titleonly' => 0, 'searchuser' => '', 'exactname' => 0, 'childforums' => 0, 'starteronly' => 0,
		                  'replyless' => 0, 'replylimit' => 0, 'searchdate' => '365', 'beforeafter' => 'after', 'sortby' => 'lastpost',
		                  'order'     => 'dec', 'catids' => '0', 'show' => '0', 'topic_id' => 0);

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

			if ($param == 'catids')
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
