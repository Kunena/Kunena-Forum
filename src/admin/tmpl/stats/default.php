<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Stats
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$stats = $this->kunenaStats;

// [label, value] of the totals, then of the activity of today and yesterday.
$totals = [
    ['COM_KUNENA_STAT_TOTAL_MESSAGES', $stats->messageCount],
    ['COM_KUNENA_STAT_TOTAL_SUBJECTS', $stats->topicCount],
    ['COM_KUNENA_STAT_TOTAL_USERS', $stats->memberCount],
    ['COM_KUNENA_STAT_TOTAL_SECTIONS', $stats->sectionCount],
    ['COM_KUNENA_STAT_TOTAL_CATEGORIES', $stats->categoryCount],
];

$activity = [
    ['COM_KUNENA_STAT_TODAY_OPEN_THREAD', $stats->todayTopicCount],
    ['COM_KUNENA_STAT_TODAY_TOTAL_ANSWER', $stats->todayReplyCount],
    ['COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD', $stats->yesterdayTopicCount],
    ['COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER', $stats->yesterdayReplyCount],
];
?>

<div id="kunena" class="kd statistics">
    <section class="kd-section" aria-labelledby="kd-stats-totals">
        <h2 id="kd-stats-totals" class="kd-section__title"><?php echo Text::_('COM_KUNENA_STATISTICS'); ?></h2>
        <div class="kd-stats kd-stats--auto">
            <?php foreach ($totals as [$label, $value]) : ?>
                <div class="kd-stat">
                    <span class="kd-stat__label"><?php echo Text::_($label); ?></span>
                    <span class="kd-stat__value"><?php echo (int) $value; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="kd-stats kd-stats--auto">
            <?php foreach ($activity as [$label, $value]) : ?>
                <div class="kd-stat">
                    <span class="kd-stat__label"><?php echo Text::_($label); ?></span>
                    <span class="kd-stat__value"><?php echo (int) $value; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="kd-columns">
        <?php foreach ($stats->top as $index => $top) : ?>
            <section class="kd-section" aria-labelledby="kd-stats-top-<?php echo (int) $index; ?>">
                <h2 id="kd-stats-top-<?php echo (int) $index; ?>" class="kd-section__title"><?php echo $top[0]->title; ?></h2>
                <ol class="kd-bars">
                    <li class="kd-bars__head" aria-hidden="true">
                        <span>#</span>
                        <span><?php echo $top[0]->titleName; ?></span>
                        <span><?php echo $top[0]->titleCount; ?></span>
                    </li>
                    <?php foreach ($top as $id => $item) : ?>
                        <li class="kd-bars__row">
                            <span class="kd-bars__rank"><?php echo $id + 1; ?></span>
                            <span class="kd-bars__name"><?php echo property_exists($item, 'subject') ? $this->escape($item->subject) : (property_exists($item, 'username') ? $item->username : $item->link); ?></span>
                            <span class="kd-bars__bar">
                                <span class="kd-bars__track"><span class="kd-bars__fill" style="width: <?php echo (float) $item->percent; ?>%"></span></span>
                                <?php echo (int) $item->count; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>
        <?php endforeach; ?>
    </div>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
