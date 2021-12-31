<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Tools
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

?>
<div class="modal hide fade" id="trashmenuconfirmationModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
				<h3><?php echo Text::_('COM_KUNENA_VIEW_TOOLS_RESTOREMENU_CONFIRMATION_TRASH_MODAL_TITLE'); ?></h3>
			</div>
			<div class="modal-body span11">
				<p><?php echo Text::_('COM_KUNENA_VIEW_TOOLS_RESTOREMENU_CONFIRMATION_TRASH_TIP'); ?></p>
				<div class="control-group">
					<div class="controls">
						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" type="button" data-dismiss="modal">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
				<button class="btn btn-warning" type="submit" onclick="Joomla.submitbutton('trashmenu');">
					<?php echo Text::_('COM_KUNENA_TOOLS_BUTTON_RESTOREMENU_CONFIRMATION_PROCESS'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
