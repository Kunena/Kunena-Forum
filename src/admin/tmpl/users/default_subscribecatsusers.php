<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Users
 * @subpackage      Users
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="modal fade" id="subscribecatsusersModal" tabindex="-1" aria-labelledby="subscribecatsusersModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="subscribecatsusersModalLabel"><?php echo Text::_('COM_KUNENA_BATCH_SUBSCIRBE_USERS_CATEGORIES_MODAL_TITLE'); ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><?php echo Text::_('COM_KUNENA_BATCH_SUBSCIRBE_USERS_CATEGORIES_TIP'); ?></p>
                    <div class="control-group">
                        <div class="controls">
                            <?php echo $this->modCatList; ?>
                        </div>
                    </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCANCEL'); ?></button>
        <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('users.subscribeuserstocategories');"><?php echo Text::_('JSUBMIT'); ?></button>
      </div>
    </div>
  </div>
</div>
