<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator.Template
 * @subpackage    Categories
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="modal hide fade" id="catconfirmdelete">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
				<h3><?php echo Text::_('COM_KUNENA_CATEGORIES_CONFIRM_DELETE_TITLE_MODAL'); ?></h3>
			</div>
			<div class="modal-body span11">
				<p><?php echo Text::_('COM_KUNENA_CATEGORIES_CONFIRM_DELETE_BODY_MODAL'); ?></p>
				<div class="control-group">
					<div class="controls">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" type="button"
						data-dismiss="modal">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
				<button class="btn btn-danger" type="submit" onclick="Joomla.submitbutton('remove');">
					<?php echo Text::_('COM_KUNENA_CATEGORIES_DELETE_MODAL_BUTTON_LABEL'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
