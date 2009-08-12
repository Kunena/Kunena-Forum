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

class KunenaTableMessages extends KunenaTable 
{
	var $id = null;	
	var $parent = null;	
	var $thread = null;	
	var $catid = null;	
	var $name = null;	
	var $userid = null;	
	var $email = null;	
	var $subject = null;	
	var $time = null;	
	var $ip = null;	
	var $topic_emoticon = null;	
	var $locked = null;	
	var $hold = null;	
	var $ordering = null;	
	var $hits = null;	
	var $moved = null;	
	var $modified_by = null;	
	var $modified_time = null;	
	var $modified_reason = null;	
	var $message = null;
	
	function __construct( &$database )
	{
		parent::__construct( '#__kunena_messages', 'id', $database );
	}
}