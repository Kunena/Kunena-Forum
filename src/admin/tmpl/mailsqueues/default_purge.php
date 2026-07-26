<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Mailsqueues
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

?>
<div class="p-3">
    <div class="row">
        <p><?php echo Text::_('COM_KUNENA_ADMIN_MAILSQUEUE_PURGE_CONFIRM'); ?></p>
    </div>
</div>
<div class="btn-toolbar p-3">
    <button type="button" class="btn btn-danger ms-auto" data-bs-dismiss="modal">
        <?php echo Text::_('JCANCEL'); ?>
    </button>
    <button type="submit" id="batch-submit-button-id" class="btn btn-success" onclick="Joomla.submitbutton('mailsqueues.purge');return false;">
        <?php echo Text::_('JSUBMIT'); ?>
    </button>
</div>
