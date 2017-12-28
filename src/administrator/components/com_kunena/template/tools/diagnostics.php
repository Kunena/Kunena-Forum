<?php
/**
 * Kunena Component
 * @package     Kunena.Administrator.Template
 * @subpackage  SyncUsers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAdminViewTools $this

?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">

		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="diagnostics" />
			<?php echo JHtml::_('form.token'); ?>

		</form>

		<?php
		if (!empty($task)) :
			$rows = KunenaForumDiagnostics::getItems($task);
			$info = KunenaForumDiagnostics::getFieldInfo($task);
			$fields = array_keys((array) reset($rows));
		?>

		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo JText::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_DIAG_ON', $task); ?></th>
				</tr>
			</thead>
			<?php if ($rows) : ?>
			<tr>
				<?php foreach ($fields as $field) : ?>
				<th><?php echo $this->escape($field) ?></th>
				<?php endforeach ?>
			</tr>
			<?php foreach (KunenaForumDiagnostics::getItems($task) as $row) : ?>
			<tr>
				<?php foreach ($row as $field => $value) : ?>
				<?php $special = isset($info[$field]) ? $info[$field] : '' ?>
				<td<?php echo $special && $special[0] != '_' ? ' class="' . $special . '"' : '' ?>><?php
					if ($special &&  $special[0] == '_') {
						echo $info[$special] . $this->escape($value);
					} else {
						echo $this->escape($value);
					}
				?></td>
				<?php endforeach ?>
			</tr>
			<?php endforeach ?>
			<?php else : ?>
			<tr><td><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_NO_ISSUES_FOUND') ?></td></tr>
			<?php endif ?>
		</table>

		<?php else : ?>

		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS'); ?></th>
				</tr>
			</thead>
			<?php foreach (KunenaForumDiagnostics::getList() as $item) : ?>
			<?php $count = KunenaForumDiagnostics::count($item) ?>
			<tr>
				<td><?php echo $item ?></td>
				<?php if ($count) : ?>
				<td style="color:red;"><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_TEST_FAILED') ?></td>
				<td><?php echo JText::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_NUMBER_OF_ISSUES', "<b>{$count}</b>") ?></td>
				<td>
					<?php echo KunenaForumDiagnostics::canFix($item) ? '<a href="' . KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&task=diagnostics&fix={$item}&" . JSession::getFormToken() . '=1') . '">' . JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_FIX_ISSUES') . '</a>' : '' ?>
					<?php echo KunenaForumDiagnostics::canDelete($item) ? '<a href="' . KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&task=diagnostics&delete={$item}&" . JSession::getFormToken() . '=1') . '">' . JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_DELETE_BROKEN_ITEMS') . '</a>' : '' ?></td>
				<?php else : ?>
				<td style="color:green;"><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_TEST_PASSED') ?></td>
				<td><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_NO_ISSUES_FOUND') ?></td>
				<?php endif ?>
			</tr>
			<?php endforeach ?>
		</table>

		<?php endif ?>

	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
