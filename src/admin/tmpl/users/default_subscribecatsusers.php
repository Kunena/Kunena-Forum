<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Users
 * @subpackage      Users
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="modal hide fade" id="subscribecatsusersModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo Text::_('COM_KUNENA_BATCH_SUBSCIRBE_USERS_CATEGORIES_MODAL_TITLE'); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body span11">
				<p><?php echo Text::_('COM_KUNENA_BATCH_SUBSCIRBE_USERS_CATEGORIES_TIP'); ?></p>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->modCatList; ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" type="button" onclick="document.id('catid').value='';" data-bs-dismiss="modal">
					<?php echo Text::_('JCANCEL'); ?>
				</button>
				<button class="btn btn-outline-primary" type="submit"
						onclick="Joomla.submitbutton('users.subscribeuserstocategories');">
					<?php echo Text::_('JSUBMIT'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
