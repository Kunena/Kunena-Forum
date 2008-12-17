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

JHTML::_('behavior.switcher');
JHTML::_('behavior.tooltip');
JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHTML::stylesheet('system.css', 'administrator/templates/system/css/');
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');
?>

<div id="comments-config">
	<fieldset>
		<div style="float: right">
			<button type="button" onclick="submitbutton('config.import');">
				<?php echo JText::_('COMMENTS_IMPORT');?>
			</button>
			<button type="button" onclick="window.location = 'index.php?option=com_kunena&amp;task=config.export';">
				<?php echo JText::_('COMMENTS_EXPORT');?>
			</button>
			<button type="button" onclick="window.location = '<?php echo JRoute::_('index.php?option=com_kunena&view=config&tmpl=component'); ?>';">
				<?php echo JText::_('COMMENTS_CANCEL');?>
			</button>
		</div>
		<div class="configuration" >
			<?php echo JText::_('COMMENTS_CONFIG_TOOLBAR_TITLE'); ?>
		</div>
	</fieldset>

	<form action="index.php?option=com_kunena" method="post" name="adminForm" autocomplete="off" enctype="multipart/form-data">
		<fieldset>
			<legend><?php echo JText::_('COMMENTS_CONFIG_IMPORT'); ?></legend>

			<label for="import_file"><?php echo JText::_('COMMENTS_CONFIG_IMPORT_FROM_FILE'); ?></label><br />
			<input type="file" name="configFile" id="import_file" size="50" />

			<br /><br />

			<label for="import_string"><?php echo JText::_('COMMENTS_CONFIG_IMPORT_FROM_STRING'); ?></label><br />
			<textarea name="configString" rows="10" cols="50"></textarea>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>