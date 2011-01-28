<?php
/**
 * @version		$Id: category.php 4203 2011-01-15 20:42:56Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
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

	public function setError($error) {
		$this->error = $error;
	}

	public function getError() {
		if ($this->error) return $this->error;
		else return;
	}

	protected function Buildquery($params) {
		$db = JFactory::getDBO ();
		$this->me = KunenaFactory::getUser();
		$query = array();
		$arr_searchwords = $this->getSearchStrings();

		$search_forums = $this->get_search_forums ( $params ['catids'], $params ['childforums'] );
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

			if ($params ['titleonly'] == '0') {
				$querystrings [] = "(t.message {$not} LIKE '%{$searchword}%' {$operator} m.subject {$not} LIKE '%{$searchword}%')";
			} else {
				$querystrings [] = "(m.subject {$not} LIKE '%{$searchword}%')";
			}
		}

		//User searching
		if (JString::strlen ( $params ['searchuser'] ) > 0) {
			if ($params ['exactname'] == '1') {
				$querystrings [] = "m.name LIKE '" . $db->getEscaped ( $params ['searchuser'] ) . "'";
			} else {
				$querystrings [] = "m.name LIKE '%" . $db->getEscaped ( $params ['searchuser'] ) . "%'";
			}
		}

		$time = 0;
		switch ($params ['searchdate']) {
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
				$time = time () - 86400 * intval ( $this->params ['searchdate'] ); //24*3600
				break;
			default :
				$time = time () - 86400 * 365;
				$searchdate = '365';
		}

		if ($time) {
			if ($params ['beforeafter'] == 'after') {
				$querystrings [] = "m.time > '{$time}'";
			} else {
				$querystrings [] = "m.time <= '{$time}'";
			}
		}

		/* build query */
		$querystrings [] = "m.moved='0'";

		//Search also unapproved, trash
		$this->show = array();
		if ( CKunenaTools::isModerator($this->me->userid) && $params['show']>0 ) {
			$search_forums_array = explode(',', $search_forums);
			$search_forums = array();
			foreach ($search_forums_array as $currforum) {
				if (CKunenaTools::isModerator($this->me->userid, $currforum)) $search_forums[] = $currforum;
			}
			if (empty($search_forums)) return;
			$search_forums = implode ( ',', $search_forums );
			$querystrings [] = "m.hold='".(int)$params['show'] ."'";
		} else {
			$querystrings [] = "m.hold='0'";
		}
		$querystrings [] = "m.catid IN ({$search_forums})";

		$query['where'] = implode ( ' AND ', $querystrings );

		$groupby = array ();
		if ($params ['order'] == 'dec')
			$order1 = 'DESC';
		else
			$order1 = 'ASC';
		switch ($params ['sortby']) {
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

		$app = JFactory::getApplication ();
		$params = $app->getUserState('com_kunena.search');
		$querystings = $this->Buildquery($params);
		$where = $querystings['where'];
		$groupby = $querystings['groupby'];

		$sql = "SELECT COUNT(*) FROM #__kunena_messages AS m JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE {$where} {$groupby}";
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
		$app = JFactory::getApplication ();
		$q = $app->getUserState('com_kunena.searchword');

		if ($q == JText::_('COM_KUNENA_GEN_SEARCH_BOX'))
			$q = '';
		$this->q = $q;
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
		$this->config = KunenaFactory::getConfig ();
		$this->me = KunenaFactory::getUser();
		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();
		$params = $app->getUserState('com_kunena.search');
		$q = $app->getUserState('com_kunena.searchword');

		$do_search = TRUE;

		if (JString::strlen ( $params ['searchuser'] ) > 0)
			$do_search = TRUE;

		$str_kunena_username = $params ['searchuser'];

		if ($do_search != TRUE) {
			$this->setError( JText::_('COM_KUNENA_SEARCH_ERR_SHORTKEYWORD'));
			return;
		}

		$querystings = $this->Buildquery($params);
		$where = $querystings['where'];
		$groupby = $querystings['groupby'];
		$orderby = $querystings['orderby'];

		/* get total */
		$total = $this->getTotal();

		if ($total < $params ['limitstart'])
			$limitstart = ( int ) ($total / $params ['limit']);

		/* get results */
		$sql = "SELECT m.id, m.subject, m.catid, m.thread, m.name, m.time, t.mesid, t.message,
						c.name AS catname, c.class_sfx
				FROM #__kunena_messages_text AS t JOIN #__kunena_messages AS m ON m.id=t.mesid
				JOIN #__kunena_categories AS c ON m.catid = c.id
				WHERE {$where} {$groupby} ORDER BY {$orderby}";
		$db->setQuery ( $sql, 0, $this->config->messages_per_page_search );
		$rows = $db->loadObjectList ();
		KunenaError::checkDatabaseError();

		$this->str_kunena_errormsg = $sql . '<br />' . $db->getErrorMsg ();

		return $rows;
	}

	public function getUrlParams() {
		$app = JFactory::getApplication ();

		$params = $app->getUserState('com_kunena.search');
		$url_params = '';
		foreach ( $params as $param => $value ) {
			if ($param == 'catids')
				$value = strtr ( $value, ',', KUNENA_URL_LIST_SEPARATOR );
			if ($value != $this->defaults [$param])
				$url_params .= "&amp;$param=" . urlencode ( $value );
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