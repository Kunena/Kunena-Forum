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
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-recount"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<?php echo JHTML::_( 'form.token' ); ?>
			<table class="adminform">
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
					<th width="20%"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_CONFLICTS') ?></th>
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
					<th width="20%"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_INVALID') ?></th>
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
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
<?php
