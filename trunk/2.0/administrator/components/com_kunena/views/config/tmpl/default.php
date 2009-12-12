<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

// Display the main toolbar.
$this->_displayMainToolbar();

// Add the component HTML helper path.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the JavaScript behaviors.
JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
?>

<div id="config">
	<form action="index.php?option=com_kunena" method="post" name="adminForm" autocomplete="off">
		<div id="config-document">
			<div id="page-general">
				<?php echo $this->loadTemplate('general'); ?>
			</div>

			<div id="page-user">
				<?php echo $this->loadTemplate('user'); ?>
			</div>

			<div id="page-layout">
				<?php echo $this->loadTemplate('layout'); ?>
			</div>

			<div id="page-editor">
				<?php echo $this->loadTemplate('editor'); ?>
			</div>

			<div id="page-communication">
				<?php echo $this->loadTemplate('communication'); ?>
			</div>

			<div id="page-integration">
				<?php echo $this->loadTemplate('integration'); ?>
			</div>

			<div id="page-security">
				<?php echo $this->loadTemplate('security'); ?>
			</div>

			<div id="page-logging">
				<?php echo $this->loadTemplate('logging'); ?>
			</div>

			<div id="page-advanced">
				<?php echo $this->loadTemplate('advanced'); ?>
			</div>
		</div>
		<input type="hidden" name="view" value="config" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>