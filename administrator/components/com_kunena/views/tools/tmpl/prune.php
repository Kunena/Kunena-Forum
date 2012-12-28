<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Prune
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
			<div class="span10"><h4><?php echo JText::_('COM_KUNENA_A_PRUNE'); ?></h4>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="task" value="prune" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<table class="adminform">
			<tr>
				<td colspan="2"><?php echo JText::_('COM_KUNENA_A_PRUNE_DESC') ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NOPOSTS') ?></td>
				<td><input type="text" name="prune_days" value="30" /><?php echo "&nbsp;". JText::_('COM_KUNENA_A_PRUNE_DAYS') ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_PRUNE_DELETEORTRASH') ?></td>
				<td colspan="2"><?php echo $this->listtrashdelete ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NAME') ?></td>
				<td><?php echo $this->forumList ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_CONTROL_OPTIONS') ?></td>
				<td><?php echo $this->controloptions ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_KEEP_STICKY') ?></td>
				<td><?php echo $this->keepSticky ?></td>
			</tr>
		</table>
		</form>
    <div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
    </div>
	
</div>
