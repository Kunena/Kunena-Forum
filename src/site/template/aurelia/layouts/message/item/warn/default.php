<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserWarnings;

/*
 * The box to give the author of a message a warning: the points and the reason. It posts to the task warn of the user.
 */
$author = KunenaUserHelper::get((int) $this->message->userid);
$id     = (int) $this->message->id;
?>
<div id="warn<?php echo $id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="warnModalLabel<?php echo $id; ?>" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post">
                <input type="hidden" name="task" value="warn"/>
                <input type="hidden" name="mesid" value="<?php echo $id; ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="warnModalLabel<?php echo $id; ?>">
                        <?php echo Text::sprintf('COM_KUNENA_WARNING_TITLE', $this->escape($author->getName())); ?>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="warn-points-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_WARNING_POINTS'); ?></label>
                        <select class="form-select" id="warn-points-<?php echo $id; ?>" name="points">
                            <?php for ($points = KunenaUserWarnings::MIN_POINTS; $points <= KunenaUserWarnings::MAX_POINTS; $points++) : ?>
                                <option value="<?php echo $points; ?>"><?php echo $points; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="warn-reason-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_WARNING_REASON'); ?></label>
                        <textarea class="form-control" id="warn-reason-<?php echo $id; ?>" name="reason" rows="2" maxlength="255" required></textarea>
                        <div class="form-text"><?php echo Text::_('COM_KUNENA_WARNING_REASON_DESC'); ?></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo Text::_('COM_KUNENA_CANCEL'); ?></button>
                    <button type="submit" class="btn btn-warning"><?php echo Text::_('COM_KUNENA_BUTTON_MESSAGE_WARN'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
