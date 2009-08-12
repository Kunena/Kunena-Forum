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

class KunenaTableSmilies extends KunenaTable 
{
	var $id = null;
	var $code = null;
	var $location = null;
	var $greylocation = null;
	var $emoticonbar = null;

	function __construct( &$database )
	{
		parent::__construct( '#__kunena_smileys', 'id', $database );
	}
}