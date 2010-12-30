<?php
/**
 * @version $Id$
 * Kunena Search Plugin
 * @package Kunena Search
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ( '' );

require_once(JPATH_ADMINISTRATOR.'/components/com_kunena/api.php');

// Kunena detection and version check
$minKunenaVersion = '1.6.2';
if (! class_exists ( 'Kunena' ) || Kunena::versionBuild () < 3892) {
	return;
}
// Kunena online check
if (! Kunena::enabled ()) {
	return;
}

$app = JFactory::getApplication ();
$app->registerEvent ( 'onSearch', 'plgSearchKunena' );
$app->registerEvent ( 'onSearchAreas', 'plgSearchKunenaAreas' );

JPlugin::loadLanguage ( 'plg_search_kunenasearch', JPATH_ADMINISTRATOR );

//Then define a function to return an array of search areas.
function &plgSearchKunenaAreas() {
	static $areas = array();
	if (empty($areas)) {
		$areas['kunena'] = JText::_('PLG_KUNENASEARCH_FORUM');
	}
	return $areas;
}

//Then the real function has to be created. The database connection should be made.
//The function will be closed with an } at the end of the file.
function plgSearchKunena($text, $phrase = '', $ordering = '', $areas = null) {
	$db = JFactory::getDBO ();
	$user = JFactory::getUser ();
	$kconfig = KunenaFactory::getConfig ();

	//If the array is not correct, return it:
	if (is_array ( $areas )) {
		if (! array_intersect ( $areas, array_keys ( plgSearchKunenaAreas () ) )) {
			return array ();
		}
	}

	$plugin = JPluginHelper::getPlugin ( 'search', 'kunenasearch' );
	$pluginParams = new JParameter ( $plugin->params );

	//And define the parameters. For example like this..
	$limit = $pluginParams->def ( 'search_limit', 50 );
	$contentLimit = $pluginParams->def ( 'content_limit', 40 );
	$shBbcode = $pluginParams->def ( 'show_bbcode', 1 );

	//Use the function trim to delete spaces in front of or at the back of the searching terms
	$text = trim ( $text );

	//Return Array when nothing was filled in
	if ($text == '') {
		return array ();
	}

	//After this, you have to add the database part. This will be the most difficult part, because this changes per situation.
	//In the coding examples later on you will find some of the examples used by Joomla! 1.5 core Search Plugins.
	//It will look something like this.
	$wheres = array ();
	switch ($phrase) {

		//search exact
		case 'exact' :
			$text = $db->Quote ( '%' . $db->getEscaped ( $text, true ) . '%', false );
			$wheres2 = array ();
			$wheres2 [] = 'm.subject LIKE ' . $text . ' OR t.message LIKE ' . $text;
			$where = '(' . implode ( ') OR (', $wheres2 ) . ')';
			break;

		//search all or any
		case 'all' :
		case 'any' :

		//set default
		default :
			$words = explode ( ' ', $text );
			$wheres = array ();
			foreach ( $words as $word ) {
				$word = $db->Quote ( '%' . $db->getEscaped ( $word, true ) . '%', false );
				$wheres2 = array ();
				$wheres2 [] = 'm.subject LIKE ' . $word . ' OR t.message LIKE ' . $word;
				$wheres [] = implode ( ' OR ', $wheres2 );
			}
			$where = '(' . implode ( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
			break;
	}

	//ordering of the results
	switch ($ordering) {

		//oldest first
		case 'oldest' :
			$order = 'created ASC';
			break;

		//popular first
		case 'popular' :
			$order = 'mm.hits ASC, created DESC';
			break;

		//newest first
		case 'newest' :
			$order = 'created DESC';
			break;

		//alphabetic, ascending
		case 'alpha' :
		//default setting: alphabetic, ascending
		default :
			$order = 'm.subject ASC, created DESC';
	}

	$ksession = KunenaFactory::getSession(true);
	$query = "SELECT m.id, m.subject AS title, m.catid, m.thread, m.name, m.time AS created, t.mesid, t.message AS text, m.ordering, mm.hits, c.name AS section, 1 AS browsernav
		FROM #__kunena_messages_text AS t
		INNER JOIN #__kunena_messages AS m ON m.id=t.mesid
		INNER JOIN #__kunena_messages AS mm ON mm.id=m.thread
		INNER JOIN #__kunena_categories AS c ON m.catid = c.id
		WHERE {$where} AND m.moved=0 AND m.hold=0 AND mm.hold=0 AND mm.catid IN ({$ksession->allowed}) GROUP BY m.thread ORDER BY {$order}";

	//Set query
	$db->setQuery ( $query, 0, $limit );
	$rows = $db->loadObjectList ();

	//The 'output' of the displayed link
	kimport('html.parser');
	require_once KPATH_SITE . '/class.kunena.php';
	require_once KPATH_SITE . '/lib/kunena.smile.class.php';
	require_once KPATH_SITE . '/lib/kunena.link.class.php';
	foreach ( $rows as $key => $row ) {
		if ($shBbcode) {
			$row->text = KunenaParser::parseBBCode ( $row->text );
		} else {
			$row->text = KunenaParser::stripBBCode ( $row->text );
		}

		$row->title = JString::substr ( $row->title, '0', $contentLimit );
		$row->section = $row->section;
		$rows [$key]->href = CKunenaLink::GetMessageURL ( $row->id, $row->catid );
	}

	//Return the search results in an array
	return $rows;
}
