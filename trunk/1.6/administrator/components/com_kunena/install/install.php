<?php
/**
 * @version		$Id: install.php 1 2009-01-01 07:18:07Z mahagr $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$app = JFactory::getApplication();
$app->redirect(JRoute::_('index.php?option=com_kunena&view=install', false));