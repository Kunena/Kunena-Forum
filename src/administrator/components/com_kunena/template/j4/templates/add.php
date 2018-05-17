<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;


?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div class="col-md-2 d-none d-md-block sidebar">
			<div id="sidebar">
				<nav class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j4/common/menu.php'; ?></nav>
			</div>
		</div>
		<div id="j-main-container" class="col-md-10" role="main">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>"
				  method="post"
				  enctype="multipart/form-data" id="adminForm" name="adminForm">
				<input type="hidden" name="task" value="install"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<fieldset>
					<legend><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?>
						- <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_NEW'); ?></legend>

					<div>
						<label for="install_package"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD'); ?>
							:</label>
						<input class="input_box" name="install_package" type="file"/>
						<input class="btn btn-default" type="submit" name="submit"
							   value="<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD_FILE'); ?> &amp; <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL'); ?>"/>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
