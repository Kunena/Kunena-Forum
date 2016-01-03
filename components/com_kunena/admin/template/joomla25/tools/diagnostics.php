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

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'components/com_kunena/media/css/admin.rtl.css' );

$task = JRequest::getCmd('test');
?>
<div id="kadmin">
	<div class="kadmin-left">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
		</div>
	</div>
	<div class="kadmin-right">

	<?php
	if ($task) :
		$rows = KunenaForumDiagnostics::getItems($task);
		$info = KunenaForumDiagnostics::getFieldInfo($task);
		$fields = array_keys((array) reset($rows));
	?>

	<div class="kadmin-functitle icon-config"><?php echo JText::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_DIAG_ON', $task); ?></div>
	<table class="adminform">
		<?php if ($rows) : ?>
		<tr>
			<?php foreach ($fields as $field) : ?>
			<th><?php echo $this->escape($field) ?></th>
			<?php endforeach ?>
		</tr>
		<?php foreach (KunenaForumDiagnostics::getItems($task) as $row) : ?>
		<tr>
			<?php foreach ($row as $field=>$value) : ?>
			<?php $special = isset($info[$field]) ? $info[$field] : '' ?>
			<td<?php echo $special && $special[0] != '_' ? ' class="'.$special.'"' : '' ?>><?php
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

	<div class="kadmin-functitle icon-config"><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS'); ?></div>
	<table class="adminform">
		<?php foreach (KunenaForumDiagnostics::getList() as $item) : ?>
		<?php $count = KunenaForumDiagnostics::count($item) ?>
		<tr>
			<td><?php echo $item ?></td>
			<?php if ($count) : ?>
			<td style="color:red"><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_TEST_FAILED') ?></td>
			<td><a href="<?php echo KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&layout=diagnostics&test={$item}"); ?>"><?php echo JText::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_NUMBER_OF_ISSUES', "<b>{$count}</b>") ?></a></td>
			<td>
				<?php echo KunenaForumDiagnostics::canFix($item) ? '<a href="'.KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&task=diagnostics&fix={$item}&".JSession::getFormToken().'=1').'">'.JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_FIX_ISSUES').'</a>' : '' ?>
				<?php echo KunenaForumDiagnostics::canDelete($item) ? '<a href="'.KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&task=diagnostics&delete={$item}&".JSession::getFormToken().'=1').'">'.JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_DELETE_BROKEN_ITEMS').'</a>' : '' ?></td>
			<?php else : ?>
			<td style="color:green"><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_TEST_PASSED') ?></td>
			<td><?php echo JText::_('COM_KUNENA_DIAGNOSTICS_LABEL_NO_ISSUES_FOUND') ?></td>
			<?php endif ?>
		</tr>
		<?php endforeach ?>
	</table>
	</div>
	<?php endif ?>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
<?php
