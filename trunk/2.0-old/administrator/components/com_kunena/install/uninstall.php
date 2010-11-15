<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

function com_uninstall()
{
    // Really nothing to do as the database table stay as they are.
    // Nothing to be removed from the database.
    // If somebody wants to truly remove that data phpAdmin is required to drop all
    // Kunena tables manually.
}
