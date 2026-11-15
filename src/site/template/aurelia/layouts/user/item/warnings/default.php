<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

// The warnings of a user ($this->warnings, the newest first): his active points ($this->active) and the threshold of the automatic
// ban ($this->threshold), each warning with its points, reason, moderator, message and expiry; a moderator may remove one
// ($this->canRemove).
$now = time();
?>
<div class="kunena-warnings">
    <p class="kunena-warnings-total">
        <strong><?php echo Text::sprintf('COM_KUNENA_WARNINGS_ACTIVE', (int) $this->active); ?></strong>
        <?php if ((int) $this->threshold > 0) : ?>
            <span class="text-muted"><?php echo Text::sprintf('COM_KUNENA_WARNINGS_THRESHOLD', (int) $this->threshold); ?></span>
        <?php endif; ?>
    </p>

    <?php if (!$this->warnings) : ?>
        <p class="text-muted"><?php echo Text::_('COM_KUNENA_WARNINGS_NONE'); ?></p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-sm kunena-warnings-list">
                <caption class="visually-hidden"><?php echo Text::_('COM_KUNENA_WARNINGS'); ?></caption>
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_WARNING_DATE'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_WARNING_POINTS'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_WARNING_REASON'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_WARNING_BY'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_WARNING_EXPIRES'); ?></th>
                        <?php if ($this->canRemove) : ?>
                            <th scope="col"><span class="visually-hidden"><?php echo Text::_('COM_KUNENA_WARNING_REMOVE'); ?></span></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->warnings as $warning) :
                        $expires = (int) $warning->expires_time;
                        $active  = $expires === 0 || $expires > $now;
                        $message = (int) $warning->message_id ? KunenaMessageHelper::get((int) $warning->message_id) : null; ?>
                        <tr class="<?php echo $active ? 'kunena-warning-active' : 'kunena-warning-expired text-muted'; ?>">
                            <td><?php echo KunenaDate::getInstance((int) $warning->created_time)->toSpan('config_postDateFormat', 'config_postDateFormatHover'); ?></td>
                            <td><?php echo (int) $warning->points; ?></td>
                            <td>
                                <?php echo $this->escape((string) $warning->reason); ?>
                                <?php if ($message && $message->exists() && $message->isAuthorised('read')) : ?>
                                    <br/><a href="<?php echo $message->getUrl(); ?>"><?php echo Text::_('COM_KUNENA_WARNING_MESSAGE'); ?></a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo KunenaUserHelper::get((int) $warning->moderator_id)->getLink(null, null, ''); ?></td>
                            <td>
                                <?php if ($expires === 0) : ?>
                                    <?php echo Text::_('COM_KUNENA_WARNING_NEVER'); ?>
                                <?php elseif ($active) : ?>
                                    <?php echo KunenaDate::getInstance($expires)->toKunena('date'); ?>
                                <?php else : ?>
                                    <?php echo Text::_('COM_KUNENA_WARNING_EXPIRED'); ?>
                                <?php endif; ?>
                            </td>
                            <?php if ($this->canRemove) : ?>
                                <td>
                                    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post" class="d-inline">
                                        <input type="hidden" name="task" value="unwarn"/>
                                        <input type="hidden" name="id" value="<?php echo (int) $warning->id; ?>"/>
                                        <?php echo HTMLHelper::_('form.token'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm(<?php echo $this->escape(json_encode(Text::_('COM_KUNENA_WARNING_REMOVE_CONFIRM'))); ?>);">
                                            <?php echo Text::_('COM_KUNENA_WARNING_REMOVE'); ?>
                                        </button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
