<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTools $this */
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="syncusers" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></legend>
				<table class="table table-bordered table-striped">
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
					<tr>
						<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_DELLIFE'); ?></td>
						<td><input type="checkbox" name="userdellife" value="1" /></td>
						<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_DELLIFE_DESC'); ?></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
