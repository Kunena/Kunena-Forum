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
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/*
 * The box to ban the author of a message in one step: the duration and the reasons. It posts to the task ban of the user.
 */
$author  = KunenaUserHelper::get((int) $this->message->userid);
$isAdmin = KunenaUserHelper::getMyself()->isAdmin();
$id      = (int) $this->message->id;
$days    = max(0, (int) ($this->config->quickBanDays ?? 7));

$durations = [0 => 'COM_KUNENA_BAN_QUICK_PERMANENT', 1 => 'COM_KUNENA_BAN_QUICK_1_DAY', 7 => 'COM_KUNENA_BAN_QUICK_1_WEEK', 30 => 'COM_KUNENA_BAN_QUICK_1_MONTH', 365 => 'COM_KUNENA_BAN_QUICK_1_YEAR'];

// The start value can be a number of days which is not in the list.
if (!isset($durations[$days])) {
    $durations = [$days => Text::sprintf('COM_KUNENA_BAN_QUICK_DAYS', $days)] + $durations;
}
?>
<div id="ban<?php echo $id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="banModalLabel<?php echo $id; ?>" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post">
                <input type="hidden" name="task" value="ban"/>
                <input type="hidden" name="quick" value="1"/>
                <input type="hidden" name="userid" value="<?php echo (int) $author->userid; ?>"/>
                <input type="hidden" name="return" value="<?php echo $this->escape(Uri::getInstance()->toString(['scheme', 'host', 'port', 'path', 'query'])); ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="banModalLabel<?php echo $id; ?>">
                        <?php echo Text::sprintf('COM_KUNENA_BAN_QUICK_TITLE', $this->escape($author->getName())); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($isAdmin) : ?>
                        <div class="mb-3">
                            <label class="form-label" for="ban-level-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_BAN_BANLEVEL'); ?></label>
                            <select class="form-select" id="ban-level-<?php echo $id; ?>" name="banlevel">
                                <option value="0"<?php echo empty($this->config->quickBanLevel) ? ' selected' : ''; ?>><?php echo Text::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'); ?></option>
                                <option value="1"<?php echo !empty($this->config->quickBanLevel) ? ' selected' : ''; ?>><?php echo Text::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA'); ?></option>
                            </select>
                        </div>
                    <?php else : ?>
                        <input type="hidden" name="banlevel" value="0"/>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label" for="ban-days-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_BAN_EXPIRETIME'); ?></label>
                        <select class="form-select" id="ban-days-<?php echo $id; ?>" name="ban_days">
                            <?php foreach ($durations as $value => $label) : ?>
                                <option value="<?php echo (int) $value; ?>"<?php echo (int) $value === $days ? ' selected' : ''; ?>><?php echo Text::_($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="ban-public-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_BAN_PUBLICREASON'); ?></label>
                        <textarea class="form-control" id="ban-public-<?php echo $id; ?>" name="reason_public" rows="2"></textarea>
                        <div class="form-text"><?php echo Text::_('COM_KUNENA_BAN_PUBLICREASON_DESC'); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="ban-private-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></label>
                        <textarea class="form-control" id="ban-private-<?php echo $id; ?>" name="reason_private" rows="2"></textarea>
                        <div class="form-text"><?php echo Text::_('COM_KUNENA_BAN_PRIVATEREASON_DESC'); ?></div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ban-delposts-<?php echo $id; ?>" name="bandelposts" value="bandelposts"<?php echo !empty($this->config->quickBanDeletePosts) ? ' checked' : ''; ?>/>
                        <label class="form-check-label" for="ban-delposts-<?php echo $id; ?>"><?php echo Text::_('COM_KUNENA_BAN_QUICK_DELETE_POSTS'); ?></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php echo Text::_('COM_KUNENA_CANCEL'); ?></button>
                    <button type="submit" class="btn btn-danger"><?php echo Text::_('COM_KUNENA_BUTTON_MESSAGE_BAN'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
