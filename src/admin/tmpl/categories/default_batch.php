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
<div class="modal fade" id="batchCategories" tabindex="-1" aria-labelledby="batchCategories" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="batchCategoriesLabel"><?php echo Text::_('COM_KUNENA_BATCH_OPTIONS'); ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
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
                                    <input type="radio" name="move_copy" value="move"/>
                                    <label>
                                        <?php echo Text::_('COM_KUNENA_BATCH_CATEGORY_MOVE') ?>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('JCANCEL'); ?></button>
        <button type="button" class="btn btn-primary"><?php echo Text::_('COM_KUNENA_BATCH_PROCESS'); ?></button>
      </div>
    </div>
  </div>
</div>