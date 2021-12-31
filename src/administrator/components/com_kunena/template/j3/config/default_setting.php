<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Users
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" method="post"
	  name="settingFormModal"
	  id="settingFormModal">
	<div class="modal hide fade" id="settingModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<input type="hidden" name="task" value="setdefault"/>
				<?php echo HTMLHelper::_('form.token') ?>

				<div class="modal-header">
					<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
					<h3><?php echo Text::_('COM_KUNENA_CONFIG_MODAL_TITLE'); ?></h3>
				</div>
				<div class="modal-body span11">
					<p><?php echo Text::_('COM_KUNENA_CONFIG_MODAL_DESCRIPTION'); ?></p>
				</div>
				<div class="modal-footer">
					<button class="btn" type="button" data-dismiss="modal">
						<?php echo Text::_('JCANCEL'); ?>
					</button>
					<button class="btn btn-primary" type="submit"
							onclick="document.getElementById('settingFormModal').submit();">
						<?php echo Text::_('JSUBMIT'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
