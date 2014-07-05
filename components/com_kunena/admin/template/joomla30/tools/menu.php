<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage SyncUsers
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
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td colspan="4"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_ISSUES') ?></td>
					</tr>
					<tr>
						<th width="20%"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_LEGACY') ?></th>
						<th colspan="3"><?php echo count($this->legacy) ?></th>
					</tr>
					<?php foreach ($this->legacy as $item) : ?>
					<tr>
						<td></td>
						<td><?php echo "/{$item->route} ({$item->menutype}: {$item->id})" ?></td>
						<td><?php echo $item->link ?></td>
						<td><?php echo ($item->published == 0 ? JText::_('COM_KUNENA_UNPUBLISHED') : ($item->published < 0 ? JText::_('COM_KUNENA_TRASHED') : JText::_('COM_KUNENA_PUBLISHED')))  ?></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<th><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_CONFLICTS') ?></th>
						<th colspan="2"><?php echo count($this->conflicts) ?></th>
					</tr>
					<?php foreach ($this->conflicts as $item) : ?>
					<tr>
						<td></td>
						<td><?php echo "/{$item->route} ({$item->menutype}: {$item->id})" ?></td>
						<td><?php echo $item->link ?></td>
						<td><?php echo ($item->published == 0 ? JText::_('COM_KUNENA_UNPUBLISHED') : ($item->published < 0 ? JText::_('COM_KUNENA_TRASHED') : JText::_('COM_KUNENA_PUBLISHED')))  ?></td>
					</tr>
					<?php endforeach ?>
					<tr>
						<th><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_INVALID') ?></th>
						<th colspan="2"><?php echo count($this->invalid) ?></th>
					</tr>
					<?php foreach ($this->invalid as $item) : ?>
					<tr>
						<td></td>
						<td><?php echo "/{$item->route} ({$item->menutype}: {$item->id})" ?></td>
						<td><?php echo $item->link ?></td>
						<td><?php echo ($item->published == 0 ? JText::_('COM_KUNENA_UNPUBLISHED') : ($item->published < 0 ? JText::_('COM_KUNENA_TRASHED') : JText::_('COM_KUNENA_PUBLISHED')))  ?></td>
					</tr>
					<?php endforeach ?>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
