<?php
/**
* @version $Id: fb_db_iterator.class.php 804 2008-07-12 18:15:06Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
class fb_DB_Iterator {
    /**
    * iterator class to iterate on db results
    * currently supporting optimized: mysql, mysqli
    * no fallback implemented if identification fails!
    * finally this class tries to omit Joomlas bad behaviour on cycling through
    * huge results and loading anything into local php memory
    *
    * Extension by Miro Dietiker, MD Systems, http://www.md-systems.ch
    */
    var $db;
    var $result;
    var $ctype = 'mysql';
    /**
    * @param string $db
    *             the joomla database connection
    */
    function fb_DB_Iterator($db) {
        $this->db = $db;
        # decide connector... pretty ugly in joomla since there's nothing like related config
        if(function_exists('mysql_ping')) {
            if(!@mysql_ping($db->_resource)) {
      	        $this->ctype = 'mysqli';
            }
        }
        $this->result = $db->query();
    }

    function loadNextObject(&$object) {
        if(!$this->result) {
            return FALSE;
        }
        # look /joomla/database.php@458 and may bind to existing object?!
        if($this->ctype=='mysqli') {
            $object = mysqli_fetch_object($this->result);
        } else {
        	$object = mysql_fetch_object($this->result);
        }
        if($object===NULL || $object===FALSE) {
        	return FALSE;
        }
        return TRUE;
    }

    function Reset() {
        if($this->ctype=='mysqli') {
            mysqli_data_seek($this->result, 0);
        } else {
            mysql_data_seek($this->result, 0);
        }
        return TRUE;
    }

    function Free() {
    	// free resource
    	if(is_resource($this->db))
    	{
            if($this->ctype=='mysqli') {
    		    mysqli_free_result($this->result);
            } else {
            	mysql_free_result($this->result);
            }
    	}
        return TRUE;
    }
}

?>