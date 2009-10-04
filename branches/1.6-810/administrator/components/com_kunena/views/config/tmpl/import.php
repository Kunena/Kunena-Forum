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
$this->_displayImportToolbar();

// Add the component HTML helper path.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the JavaScript behaviors.
JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
?>

<div id="comments-config">
	<fieldset>
		<legend><?php echo JText::_('Import/Export Help'); ?></legend>
		<p><?php echo JText::_('how to ...'); ?></p>
	</fieldset>

	<form action="index.php?option=com_kunena" method="post" name="adminForm" autocomplete="off" enctype="multipart/form-data">
		<fieldset>
			<legend><?php echo JText::_('Import'); ?></legend>

			<label for="import_file"><?php echo JText::_('Import from file'); ?></label><br />
			<input type="file" name="configFile" id="import_file" size="50" />

			<br /><br />

			<label for="import_string"><?php echo JText::_('Import from string'); ?></label><br />
			<textarea name="configString" rows="10" cols="50"></textarea>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>