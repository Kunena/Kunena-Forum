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

kimport ( 'kunena.model' );
kimport('kunena.forum.category.helper');

/**
 * Search Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelSearch extends KunenaModel {
	protected $error = null;

	protected function populateState() {
		$this->config = KunenaFactory::getConfig ();
		$this->me = KunenaFactory::getUser();

		// Get search word list
		$value = JString::trim ( JRequest::getString ( 'q', '' ) );
		if ($value == JText::_('COM_KUNENA_GEN_SEARCH_BOX')) {
			$value = '';
		}
		$this->setState ( 'searchwords', $value );

		$value = JRequest::getInt ( 'titleonly', 0 );
		$this->setState ( 'query.titleonly', $value );

		$value = JRequest::getString ( 'searchuser', '' );
		$this->setState ( 'query.searchuser', $value );

		$value = JRequest::getInt ( 'starteronly', 0 );
		$this->setState ( 'query.starteronly', $value );

		$value = JRequest::getInt ( 'exactname', 0 );
		$this->setState ( 'query.exactname', $value );

		$value = JRequest::getInt ( 'replyless', 0 );
		$this->setState ( 'query.replyless', $value );

		$value = JRequest::getInt ( 'replylimit', 0 );
		$this->setState ( 'query.replylimit', $value );

		$value = JRequest::getString ( 'searchdate', 365 );
		$this->setState ( 'query.searchdate', $value );

		$value = JRequest::getWord ( 'beforeafter', 'after' );
		$this->setState ( 'query.beforeafter', $value );

		$value = JRequest::getWord ( 'sortby', 'lastpost' );
		$this->setState ( 'query.sortby', $value );

		$value = JRequest::getWord ( 'order', 'dec' );
		$this->setState ( 'query.order', $value );

		$value = JRequest::getInt ( 'childforums', 0 );
		$this->setState ( 'query.childforums', $value );

		if (isset ( $_POST ['q'] ) || isset ( $_POST ['searchword'] )) {
			$value = JRequest::getVar ( 'catids', array (0), 'post', 'array' );
			JArrayHelper::toInteger($value);
		} else {
			$value = JRequest::getString ( 'catids', '0', 'get' );
			$value = explode ( ' ', $value );
			JArrayHelper::toInteger($value);
		}
		$this->setState ( 'query.catids', $value );

		$value = JRequest::getInt ( 'show', 0 );
		$this->setState ( 'query.show', $value );

		$value = $this->getInt ( 'limitstart', 0 );
		if ($value < 0) $value = 0;
		$this->setState ( 'list.start', $value );

		$value = $this->getInt ( 'limit', 0 );
		if ($value < 1) $value = $this->config->messages_per_page_search;
		$this->setState ( 'list.limit', $value );
	}

	public function setError($error) {
		$this->error = $error;
	}

	public function getError() {
		if ($this->error) return $this->error;
		else return;
	}

	protected function Buildquery() {
		$db = JFactory::getDBO ();
		$query = array();

		$categories = $this->getCategories ();
		/* if there are no forums to search in, set error and return */
		if (empty ( $categories )) {
			$this->setError(JText::_('COM_KUNENA_SEARCH_NOFORUM'));
			return;
		}

		foreach ( $this->getSearchWords() as $searchword ) {
			$searchword = $db->getEscaped ( JString::trim ( $searchword ) );
			if (empty ( $searchword ))
				continue;
			$matches = array ();
			$not = '';
			$operator = ' OR ';

			if ( substr($searchword, 0, 1) == '-' && strlen($searchword) > 1 ) {
				$not = 'NOT';
				$operator = 'AND';
				$searchword = JString::substr ( $searchword, 1 );
			}

			if (!$this->getState('query.titleonly')) {
				$querystrings [] = "(t.message {$not} LIKE '%{$searchword}%' {$operator} m.subject {$not} LIKE '%{$searchword}%')";
			} else {
				$querystrings [] = "(m.subject {$not} LIKE '%{$searchword}%')";
			}
		}

		//User searching
		$username = $this->getState('query.searchuser');
		if ($username) {
			if ($this->getState('query.exactname') == '1') {
				$querystrings [] = "m.name LIKE '" . $db->getEscaped ( $username ) . "'";
			} else {
				$querystrings [] = "m.name LIKE '%" . $db->getEscaped ( $username ) . "%'";
			}
		}

		$time = 0;
		switch ($this->getState('query.searchdate')) {
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
				$time = time () - 86400 * intval ( $this->getState('query.searchdate') ); //24*3600
				break;
			default :
				$time = time () - 86400 * 365;
				$searchdate = '365';
		}

		if ($time) {
			if ($this->getState('query.beforeafter') == 'after') {
				$querystrings [] = "m.time > '{$time}'";
			} else {
				$querystrings [] = "m.time <= '{$time}'";
			}
		}

		/* build query */
		$querystrings [] = "m.moved='0'";

		// Search normal / unapproved / deleted
		$querystrings [] = "m.hold='{$this->getState('query.show')}'";

		$search_forums = implode ( ',', array_keys($categories) );
		$querystrings [] = "m.catid IN ({$search_forums})";

		$query['where'] = implode ( ' AND ', $querystrings );

		$groupby = array ();
		if ($this->getState('query.order') == 'dec')
			$order1 = 'DESC';
		else
			$order1 = 'ASC';
		switch ($this->getState('query.sortby')) {
			case 'title' :
				$query['orderby'] = "m.subject {$order1}, m.time {$order1}";
				break;
			case 'views' :
				$query['orderby'] = "m.hits {$order1}, m.time {$order1}";
				break;
			/*
		case 'threadstart':
		$orderby = "m.time {$order1}, m.ordering {$order1}, m.hits {$order1}";
		break;
*/
			case 'forum' :
				$query['orderby'] = "m.catid {$order1}, m.time {$order1}, m.ordering {$order1}";
				break;
			/*
		case 'replycount':
		case 'postusername':
*/
			case 'lastpost' :
			default :
				$query['orderby'] = "m.time {$order1}, m.ordering {$order1}, m.catid {$order1}";
		}

		if (count ( $groupby ) > 0)
			$query['groupby'] = ' GROUP BY ' . implode ( ',', $groupby );
		else
			$query['groupby'] = '';

		return $query;
  }

	public function getTotal() {
		$db = JFactory::getDBO ();

		$querystings = $this->Buildquery();
		$sql = "SELECT COUNT(*) FROM #__kunena_messages AS m JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE {$querystings['where']} {$querystings['groupby']}";
		$db->setQuery ( $sql );
		$total = $db->loadResult ();
		KunenaError::checkDatabaseError();

		/* if there are no forums to search in, set error and return */
		if ($total == 0) {
			$this->setError(JText::_('COM_KUNENA_SEARCH_ERR_NOPOSTS'));
			return;
		}

		return $total;
	}

	public function getSearchWords() {
		// Accept individual words and quoted strings
		$splitPattern = '/[\s,]*\'([^\']+)\'[\s,]*|[\s,]*"([^"]+)"[\s,]*|[\s,]+/u';
		$searchwords = preg_split($splitPattern, $this->getState('searchwords'), 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$result = array ();
		foreach ( $searchwords as $word ) {
			// Do not accept one letter strings
			if (JString::strlen ( $word ) > 1)
				$result [] = $word;
		}
		return $result;
	}

	public function getResults() {
		$q = $this->getState('searchwords');
		if (!$q && !$this->getState('query.searchuser')) {
			$this->setError( JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD'));
			return;
		}

		$querystings = $this->Buildquery();

		/* get total */
		$total = $this->getTotal();

		if ($total < $this->getState('list.limitstart'))
			$limitstart = ( int ) ($total / $this->getState('list.limit'));

		/* get results */
		$db = JFactory::getDBO ();
		$sql = "SELECT m.id, m.subject, m.catid, m.thread, m.name, m.time, t.mesid, t.message,
						c.name AS catname, c.class_sfx
				FROM #__kunena_messages_text AS t JOIN #__kunena_messages AS m ON m.id=t.mesid
				JOIN #__kunena_categories AS c ON m.catid = c.id
				WHERE {$querystings['where']} {$querystings['groupby']} ORDER BY {$querystings['orderby']}";
		$db->setQuery ( $sql, 0, $this->config->messages_per_page_search );
		$rows = $db->loadObjectList ();
		KunenaError::checkDatabaseError();

		$this->str_kunena_errormsg = $sql . '<br />' . $db->getErrorMsg ();

		return $rows;
	}

	public function getUrlParams() {
		// Turn internal state into URL, but ignore default values
		$defaults = array ('titleonly' => 0, 'searchuser' => '', 'exactname' => 0, 'childforums' => 0, 'starteronly' => 0,
			'replyless' => 0, 'replylimit' => 0, 'searchdate' => '365', 'beforeafter' => 'after', 'sortby' => 'lastpost',
			'order' => 'dec', 'catids' => '0', 'show' => '0' );

		$url_params = '';
		$state = $this->getState();
		foreach ( $state as $param => $value ) {
			$paramparts = explode('.', $param);
			if ($paramparts[0] != 'query') continue;
			$param = $paramparts[1];

			if ($param == 'catids')
				$value = implode ( ' ', $value );
			if ($value != $defaults [$param])
				$url_params .= "&$param=" . urlencode ( $value );
		}
		return $url_params;
	}

	protected function getCategories() {
		$catids = $this->getState('query.catids');
		$childforums = $this->getState('query.childforums');
		$hold = $this->getState('query.show');
		if ($hold == 1) {
			$authorise = 'topic.approve';
		} elseif ($hold == 2 || $hold == 3) {
			$authorise = 'topic.undelete';
		} else {
			$authorise = 'topic.read';
		}
		if (in_array(0, $catids)) {
			// All categories was requested, return all
			return KunenaForumCategoryHelper::getCategories(false, false, $authorise);
		}
		// Get listed categories
		$categories = KunenaForumCategoryHelper::getCategories($catids, false, $authorise);
		if ($childforums) {
			// Add child forums
			$categories += KunenaForumCategoryHelper::getChildren($categories, 100, false, array('action'=>$authorise));
		}
		return $categories;
	}
}