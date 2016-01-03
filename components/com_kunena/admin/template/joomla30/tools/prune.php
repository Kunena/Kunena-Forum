<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Prune
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
			<input type="hidden" name="task" value="prune" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_PRUNE'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td colspan="2"><?php echo JText::_('COM_KUNENA_A_PRUNE_DESC') ?></td>
					</tr>
					<tr>
						<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NOPOSTS') ?></td>
						<td>
							<div class="input-append">
								<input class="span3" type="text" name="prune_days" value="30" />
								<span class="add-on"><?php echo JText::_('COM_KUNENA_A_PRUNE_DAYS') ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_PRUNE_DELETEORTRASH') ?></td>
						<td><?php echo $this->listtrashdelete ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_A_PRUNE_NAME') ?></td>
						<td><?php echo $this->forumList ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_A_PRUNE_CONTROL_OPTIONS') ?></td>
						<td><?php echo $this->controloptions ?></td>
					</tr>
					<tr>
						<td ><?php echo JText::_('COM_KUNENA_A_PRUNE_KEEP_STICKY') ?></td>
						<td><?php echo $this->keepSticky ?></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
