<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Prune
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
	<div class="kadmin-functitle icon-prune"><?php echo JText::_('COM_KUNENA_A_PRUNE'); ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
		<input type="hidden" name="view" value="prune" />
		<input type="hidden" name="task" value="doprune" />
		<?php echo JHTML::_( 'form.token' ); ?>

		<table class="adminform" cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<td colspan="2"><?php echo JText::_('COM_KUNENA_A_PRUNE_DESC') ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_PRUNE_DELETEORTRASH') ?></td>
				<td colspan="2"><?php echo $this->listtrashdelete ?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NAME') ?></td>
				<td><?php echo $this->forumList?></td>
			</tr>
			<tr>
				<td width="20%"><?php echo JText::_('COM_KUNENA_A_PRUNE_NOPOSTS') ?></td>
				<td><input type="text" name="prune_days" value="30" /><?php echo "&nbsp;". JText::_('COM_KUNENA_A_PRUNE_DAYS') ?></td>
			</tr>
		</table>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
