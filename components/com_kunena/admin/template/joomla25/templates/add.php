<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Templates
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTemplates $this */
?>
<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>" method="post" enctype="multipart/form-data" id="adminForm" name="adminForm">
						<input type="hidden" name="task" value="install" />
						<?php echo JHtml::_( 'form.token' ); ?>

						<fieldset>
							<legend><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> - <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_NEW'); ?></legend>

							<div>
								<label for="install_package"><?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD' ); ?>:</label>
								<input class="input_box" name="install_package" type="file" />
								<input class="btn" type="submit" name="submit" value="<?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD_FILE' ); ?> &amp; <?php echo JText::_( 'COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL' ); ?>" />
							</div>
						</fieldset>
					</form>
				</div>

				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
