<?php
/**
 * @version $Id:$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2009 Kunena All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die('Restricted access');

require_once (JPATH_ROOT .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
require_once (KUNENA_PATH_TABLES .DS. 'table.php');

class KunenaTableCategories extends KunenaTable 
{
	var $id = null;
	var $parent = null;
	var $name = null;
	var $cat_emoticon = null;
	var $locked = null;
	var $alert_admin = null;
	var $moderated = null;
	var $moderators = null;
	var $pub_access = null;
	var $pub_recurse = null;
	var $admin_access = null;
	var $admin_recurse = null;
	var $ordering = null;
	var $future2 = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $review = null;
	var $hits = null;
	var $description = null;
	var $headerdesc = null;
	var $class_sfx = null;
	var $id_last_msg = null;
	var $numTopics = null;
	var $numPosts = null;
	var $time_last_msg = null;

	function __construct( &$database )
	{
		parent::__construct( '#__kunena_categories', 'id', $database );
	}

	function store( $updateNulls=false ) {
		$ret = parent::store($updateNulls);
		if ($ret) {
			// we must reset fbSession (allowed), when forum record was changed
			$this->_db->setQuery("UPDATE #__kunena_sessions SET allowed='na'");
			$this->_db->query() or trigger_dberror("Unable to update sessions.");
		} 
		return $ret;
	}

}
