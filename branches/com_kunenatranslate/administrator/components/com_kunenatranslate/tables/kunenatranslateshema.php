<?php
/**
 * @version $Id:  $
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class TableKunenaTranslate extends JTable
{
	/** Primary Key
	 * @var int
	 */
	var $id = null;
	
	function __construct(& $db){
		parent::__construct('#__kunenatranslate_label', 'id', $db);
	}
}