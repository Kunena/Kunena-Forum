<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator.Template
 * @subpackage    Categories
 *
 * @copyright     Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="p-3">
    <div class="row">
        <p><?php echo Text::_('COM_KUNENA_BATCH_TIP'); ?></p>
        <div class="control-group">
            <div class="controls">
                <label id="batch-choose-action-lbl" for="batch-category-id">
                    <?php echo Text::_('COM_KUNENA_BATCH_CATEGORY_LABEL'); ?>
                </label>
                <fieldset id="batch-choose-action" class="combo">
                    <?php echo $this->batchCategories; ?>
                    <div id="batch-move-copy" class="control-group radio">
                        <div class="controls">
                            <input type="radio" name="move_copy" value="move" />
                            <label>
                                <?php echo Text::_('COM_KUNENA_BATCH_CATEGORY_MOVE') ?>
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<div class="btn-toolbar p-3">
    <button type="button" class="btn btn-danger ms-auto" data-bs-dismiss="modal">
        <?php echo Text::_('JCANCEL'); ?>
    </button>
    <button type="submit" id='batch-submit-button-id' class="btn btn-success" data-submit-task='categories.batchCategories'>
        <?php echo Text::_('COM_KUNENA_BATCH_PROCESS'); ?>
    </button>
</div>