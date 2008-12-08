<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

// Include the component HTML helpers.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHTML::_('behavior.tooltip');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();
?>
<h1>Kunena Dashboard</h1>