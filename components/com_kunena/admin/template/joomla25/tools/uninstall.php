<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Prune
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
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
			<input type="hidden" name="task" value="uninstall" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td colspan="2"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></td>
					</tr>
					<tr>
						<td width="20%"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_LOGIN') ?></td>
						<td>
							<div>
								<input class="span3" type="text" name="username" value="" />
							</div>
						</td>
					</tr>
					<tr>
						<td width="20%"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PASSWORD') ?></td>
						<td>
							<div>
								<input class="span3" type="password" name="password" value="" />
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td> <button type="submit" class="btn btn-danger"><?php echo JText::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
