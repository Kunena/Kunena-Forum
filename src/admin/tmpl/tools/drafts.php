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
use Kunena\Forum\Libraries\Tools\KunenaDraftsSchedule;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$drafts    = $this->tool['drafts'];
$scheduled = $this->tool['scheduled'];
$age       = $this->tool['age'];
$date      = static fn (int $time) => $time ? HTMLHelper::_('date', $time, Text::_('DATE_FORMAT_LC5')) : '—';
$topic     = static fn (array $row) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['catid'] . '&id=' . (int) $row['thread'], false);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-file-pen" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo Text::plural('COM_KUNENA_TOOLS_DRAFTS_N_DRAFTS', (int) $drafts['total']) ?> ·
                    <?php echo Text::plural('COM_KUNENA_TOOLS_DRAFTS_N_SCHEDULED', \count($scheduled)) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <section class="kd-panel" aria-labelledby="kd-scheduled-title">
        <h3 id="kd-scheduled-title" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_SCHEDULED') ?></h3>
        <?php if (!$scheduled) : ?>
            <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_NO_SCHEDULED') ?></p>
        <?php else : ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                <input type="hidden" name="task" value="tools.publishScheduled">
                <input type="hidden" name="age" value="<?php echo $age ?>">
                <?php echo HTMLHelper::_('form.token') ?>
                <div class="kd-scroll">
                    <table class="table">
                        <tbody>
                            <?php foreach ($scheduled as $row) :
                                $id = (int) $row['id']; ?>
                                <tr>
                                    <td class="w-1 text-center">
                                        <?php if (!(int) $row['scheduled_review']) : ?>
                                            <input type="checkbox" class="form-check-input" name="ids[]" value="<?php echo $id ?>" id="kd-scheduled-<?php echo $id ?>">
                                        <?php endif ?>
                                    </td>
                                    <th scope="row">
                                        <label for="kd-scheduled-<?php echo $id ?>"><?php echo $this->escape((string) $row['subject']) ?></label>
                                        <br><span class="kd-form__hint"><?php echo $this->escape((string) $row['name']) ?> ·
                                            <a href="<?php echo $this->escape($topic($row)) ?>" target="_blank" rel="noopener"><?php echo $this->escape((string) $row['topic']) ?></a></span>
                                    </th>
                                    <td class="w-20">
                                        <?php echo $date((int) $row['scheduled_time']) ?>
                                        <?php if ((int) $row['scheduled_review']) : ?>
                                            <br><span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_REVIEW') ?></span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($this->canAdmin) : ?>
                    <div class="kd-form__actions">
                        <button type="submit" class="kd-btn kd-btn--primary" data-kd-confirm>
                            <span class="fa-solid fa-paper-plane" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_PUBLISH') ?>
                        </button>
                        <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_PUBLISH_HINT') ?></span>
                    </div>
                <?php endif ?>
            </form>
        <?php endif ?>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="drafts">
        <div class="kd-form__field">
            <label for="kd-drafts-age"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_AGE') ?></label>
            <select class="form-select" id="kd-drafts-age" name="age">
                <?php foreach (KunenaDraftsSchedule::AGES as $value) : ?>
                    <option value="<?php echo $value ?>"<?php echo $value === $age ? ' selected' : '' ?>>
                        <?php echo $value ? Text::plural('COM_KUNENA_TOOLS_TOPICS_N_DAYS', $value) : Text::_('COM_KUNENA_TOOLS_TOPICS_ANY_AGE') ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-filter" aria-hidden="true"></span>
                <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
            </button>
        </div>
    </form>

    <?php if ($drafts['items']) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.deleteDrafts">
            <input type="hidden" name="age" value="<?php echo $age ?>">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-draft"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_DRAFT') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_CHANGED') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drafts['items'] as $row) :
                            $id = (int) $row['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-draft" name="ids[]" value="<?php echo $id ?>" id="kd-draft-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-draft-<?php echo $id ?>"><?php echo $this->escape((string) $row['subject'] !== '' ? (string) $row['subject'] : Text::_('COM_KUNENA_TOOLS_DRAFTS_NO_SUBJECT')) ?></label>
                                    <br><span class="kd-form__hint">
                                        <?php if ((int) $row['message_id']) : ?>
                                            <?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_KIND_EDIT') ?>
                                        <?php elseif ((int) $row['topic_id']) : ?>
                                            <?php echo Text::sprintf('COM_KUNENA_TOOLS_DRAFTS_KIND_REPLY', $this->escape((string) $row['topic'])) ?>
                                        <?php else : ?>
                                            <?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_KIND_TOPIC') ?>
                                        <?php endif ?>
                                    </span>
                                </th>
                                <td><?php echo $this->escape((string) $row['username']) ?></td>
                                <td><?php echo $date(KunenaDraftsSchedule::lastChange($row)) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_DRAFTS_DELETE') ?>
                    </button>
                </div>
            <?php else : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
