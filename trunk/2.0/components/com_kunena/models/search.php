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

		// Search words
		$value = JString::trim ( JRequest::getVar ( 'q', '' ) );
		$this->setState ( 'searchwords', $value );

		$value = JRequest::getInt ( 'titleonly', 0 );
		$this->setState ( 'query.titleonly', $value );

		$value = JRequest::getVar ( 'searchuser', '' );
		$this->setState ( 'query.searchuser', $value );

		$value = JRequest::getInt ( 'starteronly', 0 );
		$this->setState ( 'query.starteronly', $value );

		$value = JRequest::getInt ( 'exactname', 0 );
		$this->setState ( 'query.exactname', $value );

		$value = JRequest::getInt ( 'replyless', 0 );
		$this->setState ( 'query.replyless', $value );

		$value = JRequest::getInt ( 'replylimit', 0 );
		$this->setState ( 'query.replylimit', $value );

		$value = JRequest::getVar ( 'searchdate', 365 );
		$this->setState ( 'query.searchdate', $value );

		$value = JRequest::getVar ( 'beforeafter', 'after' );
		$this->setState ( 'query.beforeafter', $value );

		$value = JRequest::getVar ( 'sortby', 'lastpost' );
		$this->setState ( 'query.sortby', $value );

		$value = JRequest::getVar ( 'order', 'dec' );
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
		$this->setState ( 'query.catids', implode(',', $value) );

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
		$arr_searchwords = $this->getSearchStrings();

		$search_forums = $this->get_search_forums ( $this->getState('query.catids'), $this->getState('query.childforums') );
		/* if there are no forums to search in, set error and return */
		if (empty ( $search_forums )) {
			$this->setError(JText::_('COM_KUNENA_SEARCH_NOFORUM'));
			return;
		}

		for($x = 0; $x < count ( $arr_searchwords ); $x ++) {
			$searchword = $arr_searchwords [$x];
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

			if ($this->getState('query.titleonly') == '0') {
				$querystrings [] = "(t.message {$not} LIKE '%{$searchword}%' {$operator} m.subject {$not} LIKE '%{$searchword}%')";
			} else {
				$querystrings [] = "(m.subject {$not} LIKE '%{$searchword}%')";
			}
		}

		//User searching
		if (JString::strlen ( $this->getState('query.searchuser') ) > 0) {
			if ($this->getState('query.exactname') == '1') {
				$querystrings [] = "m.name LIKE '" . $db->getEscaped ( $this->getState('query.searchuser') ) . "'";
			} else {
				$querystrings [] = "m.name LIKE '%" . $db->getEscaped ( $this->getState('query.searchuser') ) . "%'";
			}
		}

		$time = 0;
		switch ($this->getState('query.searchdate')) {
			case 'lastvisit' :
				$db->setQuery ( "SELECT lasttime FROM #__kunena_sessions WHERE userid={$db->Quote($this->me->userid)}" );
				$time = $db->loadResult ();
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

		//Search also unapproved, trash
		$this->show = array();
		if ( CKunenaTools::isModerator($this->me->userid) && $this->getState('query.show')>0 ) {
			$search_forums_array = explode(',', $search_forums);
			$search_forums = array();
			foreach ($search_forums_array as $currforum) {
				if (CKunenaTools::isModerator($this->me->userid, $currforum)) $search_forums[] = $currforum;
			}
			if (empty($search_forums)) return;
			$search_forums = implode ( ',', $search_forums );
			$querystrings [] = "m.hold='".(int)$this->getState('query.show') ."'";
		} else {
			$querystrings [] = "m.hold='0'";
		}
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

	public function getSearchStrings() {
		$q = $this->getState('searchword');
		if ($q == JText::_('COM_KUNENA_GEN_SEARCH_BOX'))
			$q = '';
		$this->setState('searchword', $q);
		$arr_searchwords = preg_split('/[\s,]*\'([^\']+)\'[\s,]*|[\s,]*"([^"]+)"[\s,]*|[\s,]+/u', $q, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$arr_kunena_searchstrings = array ();
		foreach ( $arr_searchwords as $q ) {
			$q = JString::trim ( $q );
			if (JString::strlen ( $q ) > 1) // make something setError..

			$arr_kunena_searchstrings [] = $q;
		}

		return $arr_kunena_searchstrings;
  }

	public function getResults() {
		$db = JFactory::getDBO ();
		$q = $this->getState('searchword');

		$do_search = TRUE;

		if (JString::strlen ( $this->getState('query.searchuser') ) > 0)
			$do_search = TRUE;

		$str_kunena_username = $this->getState('query.searchuser');

		if ($do_search != TRUE) {
			$this->setError( JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD'));
			return;
		}

		$querystings = $this->Buildquery();

		/* get total */
		$total = $this->getTotal();

		if ($total < $this->getState('list.limitstart'))
			$limitstart = ( int ) ($total / $this->getState('list.limit'));

		/* get results */
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
		$defaults = array ('titleonly' => 0, 'searchuser' => '', 'exactname' => 0, 'childforums' => 0, 'starteronly' => 0,
			'replyless' => 0, 'replylimit' => 0, 'searchdate' => '365', 'beforeafter' => 'after', 'sortby' => 'lastpost',
			'order' => 'dec', 'catids' => '0', 'show' => '0' );

		$state = $this->getState();
		$url_params = '';
		foreach ( $state as $param => $value ) {
			$paramparts = explode('.', $param);
			if ($paramparts[0] != 'query') continue;
			$param = $paramparts[1];

			if ($param == 'catids')
				$value = strtr ( $value, ',', ' ' );
			if ($value != $defaults [$param])
				$url_params .= "&$param=" . urlencode ( $value );
		}
		return $url_params;
	}

	protected function get_search_forums($catids, $childforums = 1) {
		kimport('kunena.forum.category.helper');
		$catids = explode ( ',', $catids );
		if (in_array(0, $catids)) {
			return implode ( ",", array_keys(KunenaForumCategoryHelper::getCategories()));
		}
		$categories = KunenaForumCategoryHelper::getCategories($catids);
		if ($childforums) {
			$categories += KunenaForumCategoryHelper::getChildren($categories, 100);
		}
		return implode ( ",", array_keys($categories) );
	}
}