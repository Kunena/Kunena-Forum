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
	<div class="kadmin-functitle icon-recount"><?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
				<input type="hidden" name="task" value="recount" />
				<?php echo JHTML::_( 'form.token' ); ?>
				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_A_RECOUNT_OPTIONS'); ?></legend>
					<table class="kadmin-adminform">
						<tr>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_TOPICS'); ?></td>
							<td><input type="checkbox" checked="checked" name="topics" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_TOPICS_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERTOPICS'); ?></td>
							<td><input type="checkbox" checked="checked" name="usertopics" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERTOPICS_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_CATEGORIES'); ?></td>
							<td><input type="checkbox" checked="checked" name="categories" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_CATEGORIES_DESC'); ?></td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERS'); ?></td>
							<td><input type="checkbox" checked="checked" name="users" value="1" /></td>
							<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERS_DESC'); ?></td>
						</tr>
					</table>
				</fieldset>
			</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
<?php
