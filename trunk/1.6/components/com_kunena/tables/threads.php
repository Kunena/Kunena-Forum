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

class KunenaTableThreads extends KunenaTable 
{
	var $id = null;
	var $catid = null;
	var $subject = null;
	var $topic_emoticon = null;
	var $locked = null;
	var $hold = null;
	var $ordering = null;
	var $posts = null;
	var $hits = null;
	var $moved_id = null;
	var $first_post_id = null;
	var $first_post_time = null;
	var $first_post_userid = null;
	var $first_message = null;
	var $last_post_id = null;
	var $last_post_time = null;
	var $last_post_userid = null;
	var $last_message = null;
	
	function __construct( &$database )
	{
		parent::__construct( '#__kunena_threads', 'id', $database );
	}
}