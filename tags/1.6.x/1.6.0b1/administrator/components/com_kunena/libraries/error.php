<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

defined( '_JEXEC' ) or die();

class KunenaError {
	function checkDatabaseError() {
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			$app = JFactory::getApplication();
			$my = JFactory::getUser();
			$acl = KunenaFactory::getAccessControl();
			if ($acl->isAdmin ($my)) {
				$app->enqueueMessage ( 'Kunena '.JText::sprintf ( 'COM_KUNENA_INTERNAL_ERROR_ADMIN', '<a href="http:://www.kunena.com/">www.kunena.com</a>' ), 'error' );
			} else {
				$app->enqueueMessage ( 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' ), 'error' );
			}
			return true;
		}
		return false;
	}

	function getDatabaseError() {
		$db = JFactory::getDBO();
		if ($db->getErrorNum ()) {
			$app = JFactory::getApplication();
			$my = JFactory::getUser();
			$acl = KunenaFactory::getAccessControl();
			if ($acl->isAdmin ($my)) {
				return $db->getErrorMsg();
			} else {
				return 'Kunena '.JText::_ ( 'COM_KUNENA_INTERNAL_ERROR' );
			}
		}
	}
}