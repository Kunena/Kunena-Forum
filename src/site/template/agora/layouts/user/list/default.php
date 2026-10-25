<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
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
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$config    = $this->config;
$ordering  = $this->state->get('list.ordering');
$direction = $this->state->get('list.direction');

// Load tribute script and css to have autocomplete on the user field (search.js needs it)
$this->addScript('tribute.js');
$this->addStyleSheet('tribute.css');
$this->addScript('assets/js/search.js');

// The columns of the list of Aurelia which can be sorted, as buttons: the name, then the options which are shown.
$sorts = [$config->username ? ['COM_KUNENA_USERNAME', 'username'] : ['COM_KUNENA_REALNAME', 'name']];

foreach (
    [
        'userlistPosts'         => ['COM_KUNENA_USRL_POSTS', 'posts'],
        'userlistKarma'         => ['COM_KUNENA_USRL_KARMA', 'karma'],
        'userlistEmail'         => ['COM_KUNENA_USRL_EMAIL', 'email'],
        'userlistJoinDate'      => ['COM_KUNENA_USRL_JOIN_DATE', 'registerDate'],
        'userlistLastVisitDate' => ['COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate'],
        'userlistUserHits'      => ['COM_KUNENA_USRL_HITS', 'uhits'],
    ] as $option => $sort
) {
    if ($config->$option) {
        $sorts[] = $sort;
    }
}

$this->ktemplate = KunenaFactory::getTemplate();
?>
<div class="agora-members">
    <header class="agora-members-head">
        <div class="agora-members-title">
            <span class="agora-stats-head-icon" aria-hidden="true"><?php echo KunenaIcons::members(); ?></span>
            <h1>
                <?php echo Text::_('COM_KUNENA_MEMBERS'); ?>
                <small><?php echo (int) $this->pagination->total; ?></small>
            </h1>
        </div>
        <div class="agora-members-search">
            <?php echo $this->subLayout('Widget/Search')
                ->set('state', $this->state->get('list.search'))
                ->setLayout('user'); ?>
        </div>
    </header>

    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list'); ?>"
          method="post" id="kuserlist-form" name="kuserlist-form">
        <input type="hidden" name="filter_order" value="<?php echo $ordering; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $direction; ?>"/>
        <?php echo HTMLHelper::_('form.token'); ?>

        <div class="agora-members-toolbar">
            <ul class="agora-members-sort" aria-label="<?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_SORT_BY'); ?>">
                <li class="agora-members-sort-label"><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_SORT_BY'); ?></li>
                <?php foreach ($sorts as [$title, $column]) : ?>
                    <li class="agora-members-sort-item<?php echo $ordering === $column ? ' active' : ''; ?>">
                        <?php echo HTMLHelper::_('kunenagrid.sort', $title, $column, $direction, $ordering, '', '', 'kuserlist-form'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php echo $this->subLayout('Widget/Pagination/List')
                ->set('pagination', $this->pagination)
                ->set('display', true); ?>
        </div>

        <ol class="agora-members-grid user-list" start="<?php echo (int) $this->pagination->limitstart + 1; ?>">
            <?php foreach ($this->users as $user) :
                $avatar = $config->userlistAvatar ? $user->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb') : null;
                ?>
                <li class="agora-member">
                    <div class="agora-member-head">
                        <?php if ($config->userlistAvatar) : ?>
                            <span class="agora-member-avatar post-image kwho-<?php echo $user->getType(0, true); ?>"><?php echo $avatar; ?></span>
                        <?php endif; ?>
                        <div class="agora-member-name">
                            <strong><?php echo $user->getLink(null, null, ''); ?></strong>
                            <?php if ($config->userListUserType) : ?>
                                <span class="agora-member-type"><?php echo Text::_($user->getType()); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($config->userlistOnline && $config->userStatus) : ?>
                            <span class="agora-member-status"><?php echo $this->subLayout('User/Item/Status')->set('user', $user); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($config->userlistPosts || $config->userlistKarma || $config->userlistUserHits) : ?>
                        <dl class="agora-member-stats">
                            <?php if ($config->userlistPosts) : ?>
                                <div><dt><?php echo Text::_('COM_KUNENA_USRL_POSTS'); ?></dt><dd><?php echo (int) $user->posts; ?></dd></div>
                            <?php endif; ?>
                            <?php if ($config->userlistKarma) : ?>
                                <div><dt><?php echo Text::_('COM_KUNENA_USRL_KARMA'); ?></dt><dd><?php echo (int) $user->karma; ?></dd></div>
                            <?php endif; ?>
                            <?php if ($config->userlistUserHits) : ?>
                                <div><dt><?php echo Text::_('COM_KUNENA_USRL_HITS'); ?></dt><dd><?php echo (int) $user->uhits; ?></dd></div>
                            <?php endif; ?>
                        </dl>
                    <?php endif; ?>

                    <?php if ($config->userlistJoinDate || $config->userlistLastVisitDate || $config->userlistEmail) : ?>
                        <ul class="agora-member-meta">
                            <?php if ($config->userlistJoinDate) : ?>
                                <li data-bs-toggle="tooltip" title="<?php echo $user->getRegisterDate()->toKunena('ago'); ?>">
                                    <?php echo KunenaIcons::calendar(); ?>
                                    <span><?php echo Text::_('COM_KUNENA_USRL_JOIN_DATE'); ?>:</span>
                                    <?php echo $user->getRegisterDate()->toKunena('datetime_today'); ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($config->userlistLastVisitDate) : ?>
                                <li data-bs-toggle="tooltip" title="<?php echo $user->getLastVisitDate()->toKunena('ago'); ?>">
                                    <?php echo KunenaIcons::clock(); ?>
                                    <span><?php echo Text::_('COM_KUNENA_USRL_LAST_LOGIN'); ?>:</span>
                                    <?php echo $user->getLastVisitDate()->toKunena('datetime_today'); ?>
                                </li>
                            <?php endif; ?>
                            <?php // The same rule as on the profile page: the address of a user who hides it is not shown (to the moderators it is). ?>
                            <?php if ($config->userlistEmail && $user->email && $this->me->canDisplayEmail($user)) : ?>
                                <li class="agora-member-email">
                                    <?php echo KunenaIcons::email(); ?>
                                    <?php echo HTMLHelper::_('email.cloak', $user->email); ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>

        <?php echo $this->subLayout('Widget/Pagination/List')
            ->set('pagination', $this->pagination)
            ->set('display', true); ?>
    </form>
</div>
