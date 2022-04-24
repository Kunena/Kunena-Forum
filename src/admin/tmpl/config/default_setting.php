<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Config
 * @subpackage      Configuration
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" method="post"
	  name="settingFormModal"
	  id="settingFormModal">

	<div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><?php echo Text::_('COM_KUNENA_CONFIG_MODAL_TITLE'); ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p><?php echo Text::_('COM_KUNENA_CONFIG_MODAL_DESCRIPTION'); ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCANCEL'); ?></button>
					<button type="submit" class="btn btn-primary"
					onclick="document.getElementById('settingFormModal').submit();"><?php echo Text::_('JSUBMIT'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="config.setDefault"/>
	<?php echo HTMLHelper::_('form.token') ?>
</form>
