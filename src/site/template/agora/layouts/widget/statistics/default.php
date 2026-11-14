<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

// The statistics of the index: the numbers of the forum as tiles (as on the statistics page), then the activity of today and
// yesterday, the latest member and the link to the statistics page.
$tiles = [
    [KunenaIcons::pm(), (int) $this->messageCount, Text::_('COM_KUNENA_STAT_TOTAL_MESSAGES')],
    [KunenaIcons::file(), (int) $this->topicCount, Text::_('COM_KUNENA_STAT_TOTAL_SUBJECTS')],
    [KunenaIcons::members(), (int) $this->memberCount, Text::_('COM_KUNENA_STAT_TOTAL_USERS')],
    [KunenaIcons::drawer(), (int) $this->categoryCount, Text::_('COM_KUNENA_STAT_TOTAL_CATEGORIES')],
    [KunenaIcons::grid(), (int) $this->sectionCount, Text::_('COM_KUNENA_STAT_TOTAL_SECTIONS')],
];

$activity = [
    [(int) $this->todayTopicCount, Text::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD')],
    [(int) $this->todayReplyCount, Text::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER')],
    [(int) $this->yesterdayTopicCount, Text::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD')],
    [(int) $this->yesterdayReplyCount, Text::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER')],
];
?>
<div class="kfrontend agora-wstats">
    <div class="btn-toolbar float-end">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary border btn-sm" data-bs-toggle="collapse"
                data-bs-target="#kstats" aria-controls="kstats" aria-expanded="true"
                aria-label="<?php echo $this->escape(Text::sprintf('COM_KUNENA_TOGGLE_NAMED', Text::_('COM_KUNENA_STATISTICS'))); ?>"><?php echo KunenaIcons::collapse(); ?></button>
        </div>
    </div>
    <h2 class="card-header">
        <?php if ($this->statisticsUrl) : ?>
            <a href="<?php echo $this->statisticsUrl; ?>"><?php echo Text::_('COM_KUNENA_STATISTICS'); ?></a>
        <?php else : ?>
            <?php echo Text::_('COM_KUNENA_STATISTICS'); ?>
        <?php endif; ?>
    </h2>
    <div class="collapse show" id="kstats">
        <div class="card-body">
            <ul class="agora-stats-tiles agora-wstats-tiles">
                <?php foreach ($tiles as [$icon, $value, $label]) : ?>
                    <li class="agora-stats-tile">
                        <span class="agora-stats-tile-icon" aria-hidden="true"><?php echo $icon; ?></span>
                        <span class="agora-stats-tile-value"><?php echo $this->formatLargeNumber($value); ?></span>
                        <span class="agora-stats-tile-label"><?php echo $label; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="agora-wstats-foot">
                <dl class="agora-wstats-activity">
                    <?php foreach ($activity as [$value, $label]) : ?>
                        <div>
                            <dt><?php echo $label; ?></dt>
                            <dd><?php echo $value; ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>

                <div class="agora-wstats-member">
                    <span class="agora-wstats-member-icon" aria-hidden="true"><?php echo KunenaIcons::user(); ?></span>
                    <span>
                        <span class="agora-wstats-member-label"><?php echo Text::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?></span>
                        <span class="agora-wstats-member-name"><?php echo $this->latestMemberLink; ?></span>
                    </span>
                </div>

                <?php if ($this->statisticsUrl) : ?>
                    <a class="btn btn-sm btn-outline-secondary agora-wstats-more" href="<?php echo $this->statisticsUrl; ?>">
                        <?php echo Text::_('COM_KUNENA_STAT_MORE_ABOUT_STATS'); ?>
                        <?php echo KunenaIcons::chevronright(); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
