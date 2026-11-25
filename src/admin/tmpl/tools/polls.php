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
use Kunena\Forum\Libraries\Tools\KunenaPollsOverview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$polls = $this->tool['polls'];
$state = $this->tool['state'];
$topic = static fn (array $row) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['category_id'] . '&id=' . (int) $row['threadid'], false);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-square-poll-vertical" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_POLLS_N_FOUND', \count($polls)) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="polls">
        <div class="kd-form__field">
            <label for="kd-polls-state"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></label>
            <select class="form-select" id="kd-polls-state" name="state">
                <?php foreach (KunenaPollsOverview::STATES as $value) : ?>
                    <option value="<?php echo $value ?>"<?php echo $value === $state ? ' selected' : '' ?>><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_STATE_' . strtoupper($value)) ?></option>
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

    <?php if ($polls) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="state" value="<?php echo $this->escape($state) ?>">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-poll"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_POLL') ?></th>
                            <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_VOTES') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_ENDS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($polls as $row) :
                            $id = (int) $row['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-poll" name="ids[]" value="<?php echo $id ?>" id="kd-poll-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-poll-<?php echo $id ?>"><?php echo $this->escape((string) $row['title']) ?></label>
                                    <br><span class="kd-form__hint">
                                        <?php if ($row['subject'] !== null) : ?>
                                            <a href="<?php echo $this->escape($topic($row)) ?>" target="_blank" rel="noopener"><?php echo $this->escape((string) $row['subject']) ?></a>
                                            · <?php echo $this->escape((string) $row['name']) ?>
                                        <?php else : ?>
                                            <?php echo Text::_('COM_KUNENA_TOOLS_POLLS_NO_TOPIC') ?>
                                        <?php endif ?>
                                    </span>
                                </th>
                                <td class="text-end">
                                    <?php echo (int) $row['votes'] ?>
                                    <br><span class="kd-form__hint"><?php echo Text::plural('COM_KUNENA_TOOLS_POLLS_N_VOTERS', (int) $row['voters']) ?></span>
                                </td>
                                <td>
                                    <?php if ($row['ended']) : ?>
                                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_ENDED') ?></span>
                                    <?php else : ?>
                                        <span class="kd-pill kd-pill--ok"><?php echo Text::_('COM_KUNENA_TOOLS_POLLS_OPEN') ?></span>
                                    <?php endif ?>
                                    <br><span class="kd-form__hint">
                                        <?php echo $row['ends'] ? HTMLHelper::_('date', (int) $row['ends'], Text::_('DATE_FORMAT_LC5')) : Text::_('COM_KUNENA_TOOLS_POLLS_NO_END') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--ghost" name="task" value="tools.closePolls" data-kd-confirm>
                        <span class="fa-solid fa-lock" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_POLLS_CLOSE') ?>
                    </button>
                    <button type="submit" class="kd-btn kd-btn--danger" name="task" value="tools.deletePolls" data-kd-confirm>
                        <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_POLLS_DELETE') ?>
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
