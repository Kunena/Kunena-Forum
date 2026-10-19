<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
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

$bootstrap = $this->ktemplate->params->get('bootstrap');

if ($bootstrap) {
    HTMLHelper::_('bootstrap.framework');
}

$tabs = $this->getTabs();
?>

<h1 class="float-start">
    <?php echo Text::sprintf('COM_KUNENA_USER_PROFILE', $this->escape($this->profile->getName())); ?>
</h1>

<h2 class="float-end">
    <?php if (KunenaUserFollow::isEnabled() && $this->profile->exists()) :
        $followers = KunenaUserFollow::countFollowers($this->profile->userid);
        ?>
        <small class="text-muted me-2"><?php echo $followers === 1 ? Text::_('COM_KUNENA_FOLLOWERS_COUNT_1') : Text::sprintf('COM_KUNENA_FOLLOWERS_COUNT', $followers); ?></small>
    <?php endif; ?>
    <?php if (KunenaUserFollow::canFollow($this->me, $this->profile)) :
        $following = KunenaUserFollow::isFollowing($this->me->userid, $this->profile->userid);
        ?>
        <a class="btn" rel="nofollow"
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
    <?php if ($this->profile->isAuthorised('edit')) :
        ?>
        <?php echo $this->profile->getLink(
            KunenaIcons::edit() . ' ' . Text::_('COM_KUNENA_EDIT'),
            Text::_('COM_KUNENA_EDIT'),
            'nofollow',
            'edit',
            'btn'
        ); ?>
    <?php endif; ?>
</h2>

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

<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_summary'); ?>

<div class="tabs">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php foreach ($tabs as $name => $tab) : ?>
            <li class="nav-item" role="presentation">
                <button <?php echo $tab->active ? ' class="nav-link active"' : ' class="nav-link"'; ?>
                        id="<?php echo $name; ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $name; ?>"
                        type="button" role="tab"
                        aria-controls="home" aria-selected="true"><?php echo $tab->title; ?></button>
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

<div class="clearfix"></div>

