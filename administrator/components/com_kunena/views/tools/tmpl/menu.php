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
	<div class="kadmin-functitle icon-recount"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?></div>
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="" />
			<?php echo JHTML::_( 'form.token' ); ?>
			<table class="adminform">
				<tr>
					<td colspan="2"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_ISSUES') ?></td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_LEGACY') ?></td>
					<td><?php echo count($this->legacy) ?></td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_CONFLICTS') ?></td>
					<td><?php echo count($this->conflicts) ?></td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER_INVALID') ?></td>
					<td><?php echo count($this->invalid) ?></td>
				</tr>
			</table>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
<?php
