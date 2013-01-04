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

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'components/com_kunena/media/css/admin.rtl.css' );

$task = JRequest::getCmd('test');
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">

	<?php
	if ($task) :
		$rows = KunenaForumDiagnostics::getItems($task);
		$info = KunenaForumDiagnostics::getFieldInfo($task);
		$fields = array_keys((array) reset($rows));
	?>

	<div class="kadmin-functitle icon-config"><?php echo JText::sprintf('Diagnostics on %s', $task); ?></div>
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
		<tr><td><?php echo JText::_('No issues found!') ?></td></tr>
		<?php endif ?>
	</table>

	<?php else : ?>

	<div class="kadmin-functitle icon-config"><?php echo JText::_('Diagnostics'); ?></div>
	<table class="adminform">
		<?php foreach (KunenaForumDiagnostics::getList() as $item) : ?>
		<?php $count = KunenaForumDiagnostics::count($item) ?>
		<tr>
			<td><?php echo $item ?></td>
			<?php if ($count) : ?>
			<td style="color:red"><?php echo JText::_('TEST FAILED') ?></td>
			<td><a href="<?php echo KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&layout=diagnostics&test={$item}"); ?>"><?php echo JText::sprintf('%s issues', "<b>{$count}</b>") ?></a></td>
			<td>
				<?php echo KunenaForumDiagnostics::canFix($item) ? '<a href="'.KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&task=diagnostics&fix={$item}&".JUtility::getToken().'=1').'">FIX ISSUES</a>' : '' ?>
				<?php echo KunenaForumDiagnostics::canDelete($item) ? '<a href="'.KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&task=diagnostics&delete={$item}&".JUtility::getToken().'=1').'">DELETE BROKEN ITEMS</a>' : '' ?></td>
			<?php else : ?>
			<td style="color:green"><?php echo JText::_('TEST PASSED') ?></td>
			<td><?php echo JText::_('No issues') ?></td>
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
