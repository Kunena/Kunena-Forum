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
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;

/*
 * The layout Profile: the profile in two columns. On the left a calm card with the summary of the user (the avatar, the rank, the
 * numbers, the details), the followers and the actions; on the right the tabs. On a small screen the card comes first
 * (assets/scss/layoutsets/_profile.scss).
 */

$bootstrap = $this->ktemplate->params->get('bootstrap');

if ($bootstrap) {
    HTMLHelper::_('bootstrap.tab');
    HTMLHelper::_('bootstrap.popover');
}

$tabs = $this->getTabs();
?>

<div class="agora-profile-columns">
    <aside class="agora-profile kfrontend">
        <header class="agora-profile-head">
            <h1>
                <?php echo Text::sprintf('COM_KUNENA_USER_PROFILE', $this->escape($this->profile->getName('', false))); ?>
            </h1>
        </header>

        <div class="agora-profile-summary">
            <?php
            echo $this->subLayout('User/Item/Summary')
                ->set('profile', $this->profile)
                ->set('config', $this->config)
                ->set('candisplaymail', $this->candisplaymail)
                ->set('me', $this->me)
                ->set('private', $this->private)
                ->set('points', $this->points)
                ->set('medals', $this->medals)
                ->set('avatar', $this->avatar)
                ->set('banInfo', $this->banInfo)
                ->set('socials', $this->socials);
            ?>
        </div>

        <div class="agora-profile-actions">
            <?php if (KunenaUserFollow::isEnabled() && $this->profile->exists()) :
                $followers = KunenaUserFollow::countFollowers($this->profile->userid);
                ?>
                <small class="text-muted"><?php echo $followers === 1 ? Text::_('COM_KUNENA_FOLLOWERS_COUNT_1') : Text::sprintf('COM_KUNENA_FOLLOWERS_COUNT', $followers); ?></small>
            <?php endif; ?>
            <?php if (KunenaUserFollow::canFollow($this->me, $this->profile)) :
                $following = KunenaUserFollow::isFollowing($this->me->userid, $this->profile->userid);
                ?>
                <a class="btn btn-primary" rel="nofollow"
                   href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=' . ($following ? 'unfollow' : 'follow') . '&userid=' . (int) $this->profile->userid . '&' . Session::getFormToken() . '=1'); ?>">
                    <?php echo KunenaIcons::star() . ' ' . Text::_($following ? 'COM_KUNENA_UNFOLLOW' : 'COM_KUNENA_FOLLOW'); ?>
                </a>
            <?php endif; ?>
            <?php if (KunenaUserIgnore::canIgnore($this->me, $this->profile)) :
                $ignored = KunenaUserIgnore::isIgnoring($this->me->userid, $this->profile->userid);
                ?>
                <a class="btn" rel="nofollow"
                   href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&task=' . ($ignored ? 'unignore' : 'ignore') . '&userid=' . (int) $this->profile->userid . '&' . Session::getFormToken() . '=1'); ?>">
                    <?php echo KunenaIcons::cancel() . ' ' . Text::_($ignored ? 'COM_KUNENA_UNIGNORE' : 'COM_KUNENA_IGNORE'); ?>
                </a>
            <?php endif; ?>
            <?php if ($this->profile->isAuthorised('edit')) : ?>
                <?php echo $this->profile->getLink(
                    KunenaIcons::edit() . ' ' . Text::_('COM_KUNENA_EDIT'),
                    Text::_('COM_KUNENA_EDIT'),
                    'nofollow',
                    'edit',
                    'btn'
                ); ?>
            <?php endif; ?>
        </div>
    </aside>

    <div class="agora-profile-main">
        <?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_summary'); ?>

        <div class="tabs agora-profile-tabs">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <?php foreach ($tabs as $name => $tab) : ?>
                    <li class="nav-item" role="presentation">
                        <button <?php echo $tab->active ? ' class="nav-link active"' : ' class="nav-link"'; ?>
                                id="<?php echo $name; ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $name; ?>"
                                type="button" role="tab"
                                aria-controls="<?php echo $name; ?>" aria-selected="<?php echo $tab->active ? 'true' : 'false'; ?>"><?php echo $tab->title; ?></button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content" id="myTabContent">
                <?php foreach ($tabs as $name => $tab) : ?>
                    <div class="tab-pane fade show <?php echo $tab->active ? ' in active show' : ''; ?>"
                         id="<?php echo $name; ?>" role="tabpanel" aria-labelledby="<?php echo $name; ?>-tab">
                        <?php echo $tab->content; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="clearfix"></div>
