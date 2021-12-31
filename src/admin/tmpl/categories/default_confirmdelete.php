<?php
/**
 * Kunena Component
 *
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
<div class="modal hide fade" id="catconfirmdelete" tabindex="-1" aria-labelledby="catconfirmdelete" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo Text::_('COM_KUNENA_CATEGORIES_CONFIRM_DELETE_TITLE_MODAL'); ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p><?php echo Text::_('COM_KUNENA_CATEGORIES_CONFIRM_DELETE_BODY_MODAL'); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCANCEL'); ?></button>
				<button type="button" class="btn btn-danger" onclick="Joomla.submitbutton('categories.remove');"><?php echo Text::_('COM_KUNENA_CATEGORIES_DELETE_MODAL_BUTTON_LABEL'); ?></button>
			</div>
		</div>
	</div>
</div>
