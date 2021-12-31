<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>"
			  method="post"
			  enctype="multipart/form-data" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="install"/>
			<?php echo HTMLHelper::_('form.token'); ?>

			<fieldset>
				<legend><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?>
					- <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL_NEW'); ?></legend>

				<div>
					<label for="install_package"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD'); ?>
						:</label>
					<input class="input_box" name="install_package" type="file"/>
					<input class="btn" type="submit" name="submit"
						   value="<?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD_FILE'); ?> &amp; <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL'); ?>"/>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
