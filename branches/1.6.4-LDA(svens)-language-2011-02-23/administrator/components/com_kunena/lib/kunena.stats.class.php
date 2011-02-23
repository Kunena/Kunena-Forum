<?php
/**
* @version $Id:kunena.stats.class.php 96 2010-01-23 21:55:09Z fxstein $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


/**
* Kunena stats support class
* @package com_kunena
*/
class jbStats {

	/**
	 * Total messages in Kunena
	 * @param  string date start
	 * @param string date end
	 * @return int
	 */
	function get_total_messages($start='',$end='') {
		$kunena_db = &JFactory::getDBO();
		$where=array();
		if (!empty($start))
			$where[]='time > UNIX_TIMESTAMP(\'' . $start. '\')';
		if (!empty($end))
			$where[]='time < UNIX_TIMESTAMP(\'' . $end . '\')';
		$query='SELECT COUNT(*) FROM #__kunena_messages WHERE moved=0 AND hold=0';
		if (count($where)>0)
			$query.=' AND '.implode(' AND ',$where);
		$kunena_db->setQuery($query);
		return intval($kunena_db->loadResult());
	}

	/**
	 * Total topics in Kunena
	 * @param  string date start
	 * @param string date end
	 * @return int
	 */
	function get_total_topics($start='',$end='') {
		$kunena_db = &JFactory::getDBO();
		$where=array();
		if (!empty($start))
			$where[]='time > UNIX_TIMESTAMP(\'' . $start. '\')';
		if (!empty($end))
			$where[]='time < UNIX_TIMESTAMP(\'' . $end . '\')';
		$query='SELECT COUNT(*) FROM #__kunena_messages WHERE moved=0 AND hold=0 AND parent=0';
		if (count($where)>0)
			$query.=' AND '.implode(' AND ',$where);
		$kunena_db->setQuery($query);
		return intval($kunena_db->loadResult());
	}

	/**
	 * Get top topics
	 * @return array
	 */
	function get_top_topics() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT * FROM #__kunena_messages WHERE parent = 0 ' .
				'AND hits > 0  ORDER BY hits DESC LIMIT 5');
		$results=$kunena_db->loadObjectList();
		KunenaError::checkDatabaseError();

		return count($results) > 0 ? $results : array();
	}

	/**
	 * Total sections in Kunena
	 * @return int
	 */
	function get_total_sections() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT COUNT(*) FROM #__kunena_categories WHERE parent=0');
		return intval($kunena_db->loadResult());
	}
	/**
	 * Get top categories
	 * @return array
	 */
	function get_top_categories() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT catid,COUNT(id) as totalmsg FROM #__kunena_messages' .
				' GROUP BY c.id ORDER BY catid LIMIT 5');
		$results=$kunena_db->loadObjectList();
		KunenaError::checkDatabaseError();

		if (count($results)>0) {
				$ids=implode(',',$results);
				$kunena_db->setQuery('SELECT name FROM #__kunena_categories WHERE id IN ('.$ids.') ORDER BY catid');
				$names=$kunena_db->loadResultArray();
				$i=0;
				foreach ($results as $result)
					$result->name=$names[$i++];
		}
		else
			$results=array();
		return $results;
	}
	/**
	 * Total categories in Kunena
	 * @return int
	 */
	function get_total_categories() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT COUNT(*) FROM #__kunena_categories WHERE parent>0');
		return intval($kunena_db->loadResult());
	}

	/**
	 * Latest Joomla members
	 * @return string
	 */
	function get_latest_member() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT username FROM #__users WHERE block=0 AND activation=\'\' ORDER BY id DESC LIMIT 1');
		return $kunena_db->loadResult();
	}

	/**
	 * Total joomla members
	 * @return int
	 */
	function get_total_members() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT COUNT(*) FROM #__users');
		return intval($kunena_db->loadresult());
	}

	/**
	 * Top posters
	 * @return array
	 */
	function get_top_posters() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT s.userid,s.posts,u.username FROM #__kunena_users as s ' .
				"\n INNER JOIN  #__users as u ON s.userid=u.id" .
				"\n WHERE s.posts > 0 ORDER BY s.posts DESC LIMIT 10");
		return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
	}

	/**
	 * Top profiles
	 * @return array
	 */
	function get_top_profiles() {
		$kunena_db = &JFactory::getDBO();
		$kunena_db->setQuery('SELECT s.userid,s.uhits,u.username FROM #__kunena_users as s ' .
				"\n INNER JOIN  #__users as u ON s.userid=u.id" .
				"\n WHERE s.uhits > 0 ORDER BY s.uhits DESC LIMIT 10");
		return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
	}

	/**
	 * CB top profiles
	 * @return array
	 */
	 function get_top_cbprofiles() {
	 	$kunena_db = &JFactory::getDBO();
 		$kunena_db->setQuery("SELECT u.username AS user, p.hits FROM #__users AS u"
			. "\n LEFT JOIN #__comprofiler AS p ON p.user_id = u.id"
			. "\n WHERE p.hits > 0 ORDER BY p.hits DESC LIMIT 10");
		return count($kunena_db->loadObjectList()) > 0 ? $kunena_db->loadObjectList() : array();
	 }
}

?>