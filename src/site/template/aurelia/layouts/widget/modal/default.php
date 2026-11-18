<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

/**
 * A dialog with a text field (the status message of the user): a native <dialog> (dialog.js), opened by a button with
 * command="show-modal" and commandfor="<id>". The field and the submit button belong to the form around the dialog.
 */
$id = $this->escape($this->id);
?>
<dialog id="<?php echo $id; ?>" class="kunena-dialog" aria-labelledby="<?php echo $id; ?>-title">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="<?php echo $id; ?>-title"><?php echo $this->label; ?></h3>
            <button type="button" class="btn-close" command="close" commandfor="<?php echo $id; ?>"
                    aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
        </div>
        <div class="modal-body">
            <p id="<?php echo $id; ?>-description"><?php echo $this->description; ?></p>
            <div class="control-group">
                <div class="controls">
                    <textarea name="<?php echo $this->escape($this->name); ?>" style="resize: none; text-align: left;" class="input-block-level form-control"
                              rows="3" maxlength="255" aria-labelledby="<?php echo $id; ?>-title" aria-describedby="<?php echo $id; ?>-description" autofocus><?php
                                echo $this->escape((string) $this->data); ?></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline-primary border" type="submit" id="btn_statustext">
                <?php echo Text::_('JSUBMIT'); ?>
            </button>
            <button class="btn btn-outline-primary border" type="button" command="close" commandfor="<?php echo $id; ?>">
                <?php echo Text::_('JCANCEL'); ?>
            </button>
        </div>
    </div>
</dialog>
