<?php
/**
 * Kunena Component
 *
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
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
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
						<input class="btn btn-outline-primary" type="submit" name="submit"
							   value="<?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_UPLOAD_FILE'); ?> &amp; <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_INSTALL'); ?>"/>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
