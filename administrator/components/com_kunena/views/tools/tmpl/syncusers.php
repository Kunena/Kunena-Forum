<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="container-fluid">
<div class="row-fluid">
 <div class="span2">
	<div><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	</div>
		<!-- Right side -->
			<div class="span10"><h4><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></h4>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
				<input type="hidden" name="task" value="syncusers" />
				<?php echo JHtml::_( 'form.token' ); ?>

				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_SYNC_USERS_OPTIONS'); ?></legend>
					<table class="kadmin-adminform">
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_ADD'); ?></td>
							<td><input type="checkbox" checked="checked" name="useradd" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_ADD_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_DEL'); ?></td>
							<td><input type="checkbox" name="userdel" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_DEL_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_RENAME'); ?></td>
							<td><input type="checkbox" name="userrename" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_RENAME_DESC'); ?></td>
						</tr>
					</table>
				</fieldset>
			</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>