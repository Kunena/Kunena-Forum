<?php
/**
 * @version $Id: add.php 4416 2011-02-16 08:43:29Z xillibit $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-template"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> - <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_NEW'); ?></div>
		<form enctype="multipart/form-data" action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table class="adminform">
				<tr>
					<th colspan="2"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD' ); ?></th>
				</tr>
				<tr>
					<td width="120">
						<label for="install_package"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_PACKAGE_FILE' ); ?>:</label>
					</td>
					<td>
						<input class="input_box" name="install_package" type="file" size="57" />
						<input class="button" type="submit" name="submit" value="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD_FILE' ); ?> &amp; <?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL' ); ?>" />
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="templates" />
			<input type="hidden" name="task" value="install" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>