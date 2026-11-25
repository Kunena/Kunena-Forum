<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaModerationQueue;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$unapproved = $this->tool['unapproved'];
$reports    = $this->tool['reports'];
$link       = static fn (array $m) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $m['catid'] . '&id=' . (int) $m['thread'] . '&mesid=' . (int) $m['id']) . '#' . (int) $m['id'];
$plain      = static fn (string $text) => mb_substr(trim(preg_replace('/\s+/u', ' ', preg_replace('/\[[^\]]{1,40}\]/u', '', $text) ?? $text) ?? $text), 0, 160);
$cleanup    = static fn (int $userid) => Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . $userid);
$states     = [0 => ['COM_KUNENA_TOOLS_QUEUE_STATE_PUBLISHED', 'ok'], 1 => ['COM_KUNENA_TOOLS_QUEUE_STATE_UNAPPROVED', 'warn']];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $unapproved['total'] || $reports ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-gavel" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo Text::plural('COM_KUNENA_TOOLS_QUEUE_SUMMARY', (int) $unapproved['total'], \count($reports), KunenaModerationQueue::REPORT_DAYS) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_INTRO') ?></p>
            </div>
        </div>
    </section>

    <section class="kd-section" aria-labelledby="kd-queue-unapproved">
        <h3 id="kd-queue-unapproved" class="kd-section__title"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_UNAPPROVED') ?></h3>

        <?php if (!$unapproved['items']) : ?>
            <p class="kd-section__desc"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_UNAPPROVED_NONE') ?></p>
        <?php else : ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                <input type="hidden" name="task" value="tools.moderateQueue">
                <?php echo HTMLHelper::_('form.token') ?>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <input type="checkbox" class="form-check-input" data-kd-all="kd-queue"
                                           aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                                </td>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_MESSAGE') ?></th>
                                <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_AUTHOR') ?></th>
                                <th scope="col" class="w-15 d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unapproved['items'] as $row) : ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input kd-queue" name="ids[]" value="<?php echo (int) $row['id'] ?>" id="kd-queue-<?php echo (int) $row['id'] ?>">
                                    </td>
                                    <th scope="row">
                                        <label for="kd-queue-<?php echo (int) $row['id'] ?>" class="visually-hidden"><?php echo $this->escape($row['subject']) ?></label>
                                        <a href="<?php echo $this->escape($link($row)) ?>" target="_blank" rel="noopener"><?php echo $this->escape($row['subject']) ?></a>
                                        <?php if ((int) $row['parent'] === 0) : ?>
                                            <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_NEW_TOPIC') ?></span>
                                        <?php endif ?>
                                        <br><span class="kd-form__hint"><?php echo $this->escape($plain((string) $row['message'])) ?></span>
                                        <br><span class="kd-form__hint"><?php echo HTMLHelper::_('date', (int) $row['time'], Text::_('DATE_FORMAT_LC2')) ?></span>
                                    </th>
                                    <td>
                                        <?php if ((int) $row['userid'] > 0) : ?>
                                            <a href="<?php echo $cleanup((int) $row['userid']) ?>"><?php echo $this->escape($row['name']) ?></a>
                                        <?php else : ?>
                                            <?php echo $this->escape($row['name']) ?> <span class="kd-form__hint">(<?php echo Text::_('COM_KUNENA_TOOLS_SPAM_GUEST') ?>)</span>
                                        <?php endif ?>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?php echo $this->escape((string) $row['category']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($unapproved['total'] > \count($unapproved['items'])) : ?>
                    <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_N_MORE', $unapproved['total'] - \count($unapproved['items'])) ?></p>
                <?php endif ?>
                <?php if ($this->canAdmin) : ?>
                    <div class="kd-form__actions">
                        <button type="submit" class="kd-btn kd-btn--primary" name="queue_action" value="<?php echo KunenaModerationQueue::APPROVE ?>">
                            <span class="fa-solid fa-check" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_APPROVE') ?>
                        </button>
                        <button type="submit" class="kd-btn kd-btn--danger" name="queue_action" value="<?php echo KunenaModerationQueue::TRASH ?>" data-kd-confirm>
                            <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_TRASH') ?>
                        </button>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
                <?php endif ?>
            </form>
        <?php endif ?>
    </section>

    <section class="kd-section" aria-labelledby="kd-queue-reports">
        <h3 id="kd-queue-reports" class="kd-section__title"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_REPORTS') ?></h3>

        <?php if (!$reports) : ?>
            <p class="kd-section__desc"><?php echo Text::sprintf('COM_KUNENA_TOOLS_QUEUE_REPORTS_NONE', KunenaModerationQueue::REPORT_DAYS) ?></p>
        <?php else : ?>
            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_MESSAGE') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_REASON') ?></th>
                            <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_REPORTER') ?></th>
                            <th scope="col" class="w-10"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report) :
                            $message = $report['message'];
                            [$state, $tone] = $message ? ($states[(int) $message['hold']] ?? ['COM_KUNENA_TOOLS_QUEUE_STATE_TRASHED', 'ok']) : ['COM_KUNENA_TOOLS_QUEUE_STATE_GONE', 'ok']; ?>
                            <tr>
                                <th scope="row">
                                    <?php if ($message) : ?>
                                        <a href="<?php echo $this->escape($link($message)) ?>" target="_blank" rel="noopener"><?php echo $this->escape($message['subject']) ?></a>
                                        <br><span class="kd-form__hint">
                                            <?php if ((int) $message['userid'] > 0) : ?>
                                                <a href="<?php echo $cleanup((int) $message['userid']) ?>"><?php echo $this->escape($message['name']) ?></a>
                                            <?php else : ?>
                                                <?php echo $this->escape($message['name']) ?>
                                            <?php endif ?>
                                        </span>
                                    <?php else : ?>
                                        #<?php echo (int) $report['mesid'] ?>
                                    <?php endif ?>
                                </th>
                                <td>
                                    <?php echo $this->escape($report['reason']) ?>
                                    <?php if ($report['text'] !== '') : ?>
                                        <br><span class="kd-form__hint"><?php echo $this->escape(mb_substr($report['text'], 0, 200)) ?></span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($report['reporter']) ?>
                                    <br><span class="kd-form__hint"><?php echo HTMLHelper::_('date', $report['time'], Text::_('DATE_FORMAT_LC4')) ?></span>
                                </td>
                                <td><span class="kd-pill kd-pill--<?php echo $tone ?>"><?php echo Text::_($state) ?></span></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>
    </section>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
