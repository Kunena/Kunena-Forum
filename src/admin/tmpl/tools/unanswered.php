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
use Kunena\Forum\Libraries\Tools\KunenaUnansweredTopics;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;

// The Topics view, filtered as this tool: published, no replies, older than the age (and the category).
$topics = static function (int $category) use ($report): string {
    $filter = ['state' => 'published', 'noreplies' => 1, 'age' => $report['days']] + ($category ? ['category' => $category] : []);

    return Route::_('index.php?option=com_kunena&view=topics&' . http_build_query(['filter' => $filter]));
};
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $report['total'] ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-comment-slash" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $report['total'] ? Text::plural('COM_KUNENA_TOOLS_UNANSWERED_N_FOUND', $report['total'], $report['days']) : Text::plural('COM_KUNENA_TOOLS_UNANSWERED_NONE', $report['days']) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_INTRO') ?></p>
            </div>
        </div>
        <?php if ($report['total']) : ?>
            <div class="kd-hero__actions">
                <a class="kd-btn kd-btn--primary" href="<?php echo $topics(0) ?>">
                    <span class="fa-solid fa-list" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_OPEN_ALL') ?>
                </a>
            </div>
        <?php endif ?>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="unanswered">
        <div class="kd-form__field">
            <label for="kd-unanswered-days"><?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_AGE') ?></label>
            <select class="form-select" id="kd-unanswered-days" name="days">
                <?php foreach (KunenaUnansweredTopics::AGES as $days) : ?>
                    <option value="<?php echo $days ?>"<?php echo $days === $report['days'] ? ' selected' : '' ?>><?php echo Text::plural('COM_KUNENA_TOOLS_UNANSWERED_OLDER', $days) ?></option>
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

    <?php if ($report['categories']) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_GEN_CATEGORY') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_TOPICS') ?></th>
                        <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_OLDEST') ?></th>
                        <th scope="col" class="w-15"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['categories'] as $category) : ?>
                        <tr>
                            <th scope="row"><?php echo $this->escape($category['name']) ?></th>
                            <td class="text-end"><?php echo (int) $category['count'] ?></td>
                            <td><?php echo $category['oldest'] ? HTMLHelper::_('date', (int) $category['oldest'], Text::_('DATE_FORMAT_LC4')) : '—' ?></td>
                            <td class="text-end"><a href="<?php echo $topics((int) $category['id']) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_OPEN') ?> ›</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
