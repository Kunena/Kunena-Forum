<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-syncusers"><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></div>
		<form action="index.php" method="post" name="adminForm">
				<input type="hidden" name="view" value="syncusers" />
				<input type="hidden" name="task" value="sync" />
				<?php echo JHTML::_( 'form.token' ); ?>

				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_SYNC_USERS_OPTIONS'); ?></legend>
					<table cellpadding="4" class="kadmin-adminform" cellspacing="0" border="0" width="100%">
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_CACHE'); ?></td>
							<td><input type="checkbox" name="usercache" value="1" checked="checked" /></td>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_CACHE_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_SYNC_USERS_ADD'); ?></td>
							<td><input type="checkbox" name="useradd" value="1" /></td>
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
<?php
