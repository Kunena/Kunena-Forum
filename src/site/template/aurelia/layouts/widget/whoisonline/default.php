<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Statistics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

?>

<div class="kfrontend shadow-lg rounded mt-4 border">
    <div class="btn-toolbar float-end">
        <div class="btn-group">
            <div class="btn btn-outline-primary border btn-sm" data-bs-toggle="collapse" data-bs-target="#kwho"><?php echo KunenaIcons::collapse(); ?></div>
        </div>
    </div>
    <h2 class="card-header">
        <?php if ($this->usersUrl) :
        ?>
            <a href="<?php echo $this->usersUrl; ?>">
                <?php echo Text::_('COM_KUNENA_MEMBERS'); ?>
            </a>
        <?php else :
        ?>
            <?php echo Text::_('COM_KUNENA_MEMBERS'); ?>
        <?php endif; ?>
    </h2>

    <div class="shadow-lg rounded collapse show" id="kwho">
        <div class="card-body">
            <div class="container">
                <div class="row">

                    <div class="col-md-1">
                        <ul class="list-unstyled">
                            <li class="btn-link">
                                <?php echo KunenaIcons::members(); ?>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-11">
                        <ul class="list-unstyled">
                            <li>
                                <?php echo Text::sprintf('COM_KUNENA_VIEW_COMMON_WHO_TOTAL', $this->membersOnline); ?>
                            </li>
                            <?php
                            $template  = KunenaTemplate::getInstance();
                            $direction = $template->params->get('whoisonlineName');

                            if ($direction == 'both') :
                            ?>
                                <li><?php echo $this->setLayout('both'); ?></li>
                            <?php
                            elseif ($direction == 'avatar') :
                            ?>
                                <li><?php echo $this->setLayout('avatar'); ?></li>
                            <?php else :
                            ?>
                                <li><?php echo $this->setLayout('name'); ?></li>
                            <?php
                            endif;
                            ?>

                            <?php if (!empty($this->onlineList)) :
                                // The legend is also a filter: select a role to see only the online users with that role.
                                $this->addScript('assets/js/whoisonline.js');

                                // Every role in the legend is one chip. Guests are not listed by name, so their chip is not a filter.
                                $roles = [
                                    'admin'     => ['label' => 'COM_KUNENA_COLOR_ADMINISTRATOR', 'types' => ['admin', 'localadmin'], 'class' => 'kwho-admin'],
                                    'globalmod' => ['label' => 'COM_KUNENA_COLOR_GLOBAL_MODERATOR', 'types' => ['globalmod'], 'class' => 'kwho-globalmoderator'],
                                    'moderator' => ['label' => 'COM_KUNENA_COLOR_MODERATOR', 'types' => ['moderator'], 'class' => 'kwho-moderator'],
                                    'banned'    => ['label' => 'COM_KUNENA_COLOR_BANNED', 'types' => ['banned', 'blocked'], 'class' => 'kwho-banned'],
                                    'user'      => ['label' => 'COM_KUNENA_COLOR_USER', 'types' => ['user'], 'class' => 'kwho-user'],
                                    'guest'     => ['label' => 'COM_KUNENA_COLOR_GUEST', 'types' => [], 'class' => 'kwho-guest'],
                                ];
                                $chipClass = 'border rounded-pill bg-body-tertiary text-body px-2 py-0 small';

                                // How many of the listed users have each type.
                                $typeCounts = [];

                                foreach (array_merge((array) $this->onlineList, (array) $this->hiddenList) as $whoUser) {
                                    $whoType              = $whoUser->getType(0, true);
                                    $typeCounts[$whoType] = ($typeCounts[$whoType] ?? 0) + 1;
                                }
                                ?>
                                <li class="mt-2">
                                    <span><?php echo Text::_('COM_KUNENA_LEGEND'); ?>:</span>
                                    <span class="d-inline-flex flex-wrap align-items-center gap-1" role="group" aria-label="<?php echo Text::_('COM_KUNENA_WHO_FILTER_LABEL'); ?>">
                                        <button type="button" class="<?php echo $chipClass; ?> kwho-filter active bg-primary-subtle border-primary-subtle" data-kwho-roles="" aria-pressed="true">
                                            <?php echo Text::_('COM_KUNENA_WHO_FILTER_ALL'); ?>
                                        </button>
                                        <?php foreach ($roles as $role) :
                                            $roleCount = 0;

                                            foreach ($role['types'] as $roleType) {
                                                $roleCount += $typeCounts[$roleType] ?? 0;
                                            }
                                            ?>
                                            <?php if ($role['types']) : ?>
                                                <button type="button" class="<?php echo $chipClass; ?> kwho-filter<?php echo $roleCount ? '' : ' opacity-50'; ?>" data-kwho-roles="<?php echo implode(' ', $role['types']); ?>" aria-pressed="false"<?php echo $roleCount ? '' : ' disabled'; ?>>
                                                    <span class="<?php echo $role['class']; ?>"><?php echo KunenaIcons::user(); ?><?php echo Text::_($role['label']); ?></span>
                                                    <span class="badge text-bg-secondary ms-1"><?php echo $roleCount; ?></span>
                                                </button>
                                            <?php else : ?>
                                                <span class="<?php echo $chipClass; ?> opacity-50">
                                                    <span class="<?php echo $role['class']; ?>"><?php echo KunenaIcons::user(); ?><?php echo Text::_($role['label']); ?></span>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </span>
                                    <div class="kwho-empty text-body-secondary small mt-1" role="status" hidden><?php echo Text::_('COM_KUNENA_WHO_FILTER_NONE'); ?></div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>