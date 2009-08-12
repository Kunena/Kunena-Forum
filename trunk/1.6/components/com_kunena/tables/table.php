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
jimport('joomla.database.table');

class KunenaTable extends JTable
{
	function store($updateNulls=false) {
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		if (!$ret)
		{
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
		}

		return $ret;
	}
}