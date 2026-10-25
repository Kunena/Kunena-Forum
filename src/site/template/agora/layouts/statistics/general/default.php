<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Statistics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

// The numbers of the forum: an icon, the number and its label (the number of members links to the list of the users).
$members = (int) $this->memberCount;

if ($this->userlistUrl) {
    $members = '<a href="' . $this->userlistUrl . '">' . $members . '</a>';
}

$tiles = [
    [KunenaIcons::members(), $members, Text::_('COM_KUNENA_STAT_TOTAL_USERS')],
    [KunenaIcons::pm(), (int) $this->messageCount, Text::_('COM_KUNENA_STAT_TOTAL_MESSAGES')],
    [KunenaIcons::file(), (int) $this->topicCount, Text::_('COM_KUNENA_STAT_TOTAL_SUBJECTS')],
    [KunenaIcons::grid(), (int) $this->sectionCount, Text::_('COM_KUNENA_STAT_TOTAL_SECTIONS')],
    [KunenaIcons::drawer(), (int) $this->categoryCount, Text::_('COM_KUNENA_STAT_TOTAL_CATEGORIES')],
];

// The activity of today and yesterday.
$activity = [
    [(int) $this->todayTopicCount, Text::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD')],
    [(int) $this->yesterdayTopicCount, Text::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD')],
    [(int) $this->todayReplyCount, Text::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER')],
    [(int) $this->yesterdayReplyCount, Text::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER')],
];
?>
<div class="statistics agora-stats">
    <header class="agora-stats-head">
        <span class="agora-stats-head-icon" aria-hidden="true"><?php echo KunenaIcons::stats(); ?></span>
        <h1><?php echo Text::_('COM_KUNENA_STATISTICS'); ?></h1>
    </header>

    <?php if ($this->config->showGenStats) : ?>
        <section class="agora-stats-overview" aria-label="<?php echo Text::_('COM_KUNENA_STATISTICS'); ?>">
            <ul class="agora-stats-tiles">
                <?php foreach ($tiles as [$icon, $value, $label]) : ?>
                    <li class="agora-stats-tile">
                        <span class="agora-stats-tile-icon" aria-hidden="true"><?php echo $icon; ?></span>
                        <span class="agora-stats-tile-value"><?php echo $value; ?></span>
                        <span class="agora-stats-tile-label"><?php echo $label; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="agora-stats-more">
                <div class="agora-stats-activity">
                    <h2 class="agora-stats-activity-title">
                        <span class="agora-stats-tile-icon" aria-hidden="true"><?php echo KunenaIcons::calendar(); ?></span>
                        <?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_STATS_ACTIVITY'); ?>
                    </h2>
                    <dl class="agora-stats-activity-list">
                        <?php foreach ($activity as [$value, $label]) : ?>
                            <div>
                                <dt><?php echo $label; ?></dt>
                                <dd><?php echo $value; ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <div class="agora-stats-tile agora-stats-tile-member">
                    <span class="agora-stats-tile-icon" aria-hidden="true"><?php echo KunenaIcons::user(); ?></span>
                    <span class="agora-stats-tile-value"><?php echo $this->latestMemberLink; ?></span>
                    <span class="agora-stats-tile-label"><?php echo Text::_('COM_KUNENA_STAT_LATEST_MEMBERS'); ?></span>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($this->top) : ?>
        <div class="agora-stats-boards">
            <?php foreach ($this->top as $top) : ?>
                <section class="agora-stats-board kfrontend">
                    <h2 class="card-header"><?php echo $top[0]->title; ?></h2>
                    <div class="agora-stats-board-columns" aria-hidden="true">
                        <span><?php echo $top[0]->titleName; ?></span>
                        <span><?php echo $top[0]->titleCount; ?></span>
                    </div>
                    <ol class="agora-stats-list">
                        <?php foreach ($top as $id => $item) :
                            // The lists of the users (posters, profiles, thank you) show the avatar; the lists of topics and polls do not.
                            $avatar = $item instanceof KunenaTopic ? '' : KunenaUserHelper::get((int) $item->id)->getAvatarImage('agora-stats-avatar', 'thumb');
                            $rank   = $id + 1;
                            ?>
                            <li class="agora-stats-row<?php echo $rank <= 3 ? ' agora-stats-rank-' . $rank : ''; ?>">
                                <span class="agora-stats-rank"><?php echo $rank; ?></span>
                                <?php if ($avatar) : ?>
                                    <span class="agora-stats-avatar-box"><?php echo $avatar; ?></span>
                                <?php endif; ?>
                                <span class="agora-stats-name"><?php echo $item->link; ?></span>
                                <span class="agora-stats-count"><?php echo (int) $item->count; ?></span>
                                <span class="agora-stats-bar" aria-hidden="true">
                                    <span style="width: <?php echo (int) $item->percent; ?>%;"></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
