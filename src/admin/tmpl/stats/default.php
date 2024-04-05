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

?>
<div class="statistics">
    <div class="card">
        <h3 class="card-header">
            <?php echo Text::_('COM_KUNENA_STATISTICS'); ?>
        </h3>

        <div class="card-body row">
            <div class="col-md-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TOTAL_MESSAGES'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->messageCount; ?></b>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TOTAL_SECTIONS'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->sectionCount; ?></b>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TODAY_OPEN_THREAD'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->todayTopicCount; ?></b>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TODAY_TOTAL_ANSWER'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->todayReplyCount; ?></b>
                    </li>
                </ul>
            </div>
            <div class="col-md-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TOTAL_SUBJECTS'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->topicCount; ?></b>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TOTAL_CATEGORIES'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->categoryCount; ?></b>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_YESTERDAY_OPEN_THREAD'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->yesterdayTopicCount; ?></b>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_YESTERDAY_TOTAL_ANSWER'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->yesterdayReplyCount; ?></b>
                    </li>
                </ul>
            </div>
            <div class="col-md-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                        <div class="me-auto">
                            <?php echo Text::_('COM_KUNENA_STAT_TOTAL_USERS'); ?>:
                        </div>
                        <b><?php echo (int) $this->kunenaStats->memberCount; ?></b>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php foreach ($this->kunenaStats->top as $top) :
    ?>
        <div class="card">
            <h3 class="card-header">
                <?php echo $top[0]->title; ?>
            </h3>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-start list-group-item-secondary">
                        <div class="w-1">
                            #
                        </div>
                        <div class="w-50">
                            <?php echo $top[0]->titleName; ?>:
                        </div>
                        <div class="w-40">
                            <?php echo $top[0]->titleCount; ?>:
                        </div>
                    </li>
                    <?php foreach ($top as $id => $item) :
                    ?>
                        <li class="list-group-item border-0 d-flex justify-content-between align-items-start">
                            <div class="w-1 text-center">
                                <?php echo $id + 1; ?>
                            </div>
                            <div class="w-50">
                                <?php echo property_exists($item, 'subject') ? $item->subject : (property_exists($item, 'username') ? $item->username : $item->link); ?>:
                            </div>
                            <div class="w-40">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $item->percent; ?>%;"><?php echo $item->count; ?></div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>