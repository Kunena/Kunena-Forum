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

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Html\KunenaMap;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserBadges;

if ($this->config->showUserStats) {
    $rankImage = $this->profile->getRank(0, 'image');
    $rankTitle = $this->profile->getRank(0, 'title');
}

// The numbers of the user: posts, karma, profile views, thank you and points (each one when the forum shows it).
$stats = [];

if (!empty($this->profile->posts)) {
    $stats[] = [(int) $this->profile->posts, Text::_('COM_KUNENA_POSTS')];
}

if ($this->config->showUserStats && !empty($this->profile->karma) && $this->config->showKarma) {
    $stats[] = [(int) $this->profile->karma, Text::_('COM_KUNENA_KARMA')];
}

if (!empty($this->profile->uhits)) {
    $stats[] = [(int) $this->profile->uhits, Text::_('COM_KUNENA_PROFILE_VIEWS')];
}

if (!empty($this->profile->thankyou) && $this->config->showThankYou) {
    $stats[] = [(int) $this->profile->thankyou, Text::_('COM_KUNENA_THANK_YOU_RECEIVED')];
}

if (!empty($this->points)) {
    $stats[] = [$this->points, Text::_('COM_KUNENA_AUP_POINTS')];
}

$userBadges = KunenaUserBadges::render($this->profile);
$type       = $this->profile->getType(0, true);
?>
<div class="agora-profile-hero">
    <div class="agora-profile-avatar kwho-<?php echo $type; ?>">
        <?php echo $this->profile->getLink($this->avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->profile->getName()), '', '', KunenaTemplate::getInstance()->tooltips(), null, $this->config->avatarEdit); ?>
    </div>

    <div class="agora-profile-identity">
        <div class="agora-profile-tags">
            <span class="agora-profile-type <?php echo $type; ?>"><?php echo Text::_($this->profile->getType()); ?></span>

            <?php if ($this->config->userStatus) : ?>
                <?php echo $this->subLayout('User/Item/Status')->set('user', $this->profile); ?>
            <?php endif; ?>

            <?php if ($this->config->showUserStats && $this->config->showRanking) : ?>
                <span class="agora-profile-rank">
                    <?php echo $this->escape($rankTitle); ?>
                    <?php echo $rankImage; ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($userBadges !== '') : ?>
            <div class="agora-profile-badges"><?php echo $userBadges; ?></div>
        <?php endif; ?>

        <?php if ($this->banInfo && $this->banInfo->reason_public) : ?>
            <p class="agora-profile-ban">
                <strong><?php echo Text::_('COM_KUNENA_MYPROFILE_BANINFO'); ?>:</strong>
                <?php echo $this->escape($this->banInfo->reason_public); ?>
            </p>
        <?php endif; ?>

        <div class="agora-profile-contact">
            <?php if ($this->private) : ?>
                <?php echo $this->private->showNewIcon($this->profile->userid, 'btn btn-outline-primary border btn-sm', 'glyphicon glyphicon-comment'); ?>
            <?php endif; ?>

            <?php if ($this->candisplaymail) : ?>
                <a class="btn btn-outline-primary border btn-sm" href="mailto:<?php echo $this->escape($this->profile->email); ?>"
                   rel="nofollow" aria-label="<?php echo Text::_('COM_KUNENA_MYPROFILE_EMAIL'); ?>"><?php echo KunenaIcons::email(); ?></a>
            <?php endif; ?>

            <?php if (!empty($this->profile->getWebsiteName()) && !empty($this->profile->getWebsiteURL())) : ?>
                <a class="btn btn-outline-primary border btn-sm" rel="nofollow noopener noreferrer" target="_blank"
                   href="<?php echo htmlspecialchars($this->profile->getWebsiteURL(), ENT_COMPAT, 'UTF-8'); ?>"><?php echo KunenaIcons::globe() . ' ' . $this->escape($this->profile->getWebsiteName()); ?></a>
            <?php elseif (empty($this->profile->getWebsiteName()) && !empty($this->profile->getWebsiteURL())) : ?>
                <a class="btn btn-outline-primary border btn-sm" rel="nofollow noopener noreferrer" target="_blank"
                   href="<?php echo htmlspecialchars($this->profile->getWebsiteURL(), ENT_COMPAT, 'UTF-8'); ?>"><?php echo KunenaIcons::globe(); ?></a>
            <?php elseif (!empty($this->profile->getWebsiteName()) && empty($this->profile->getWebsiteURL())) : ?>
                <button class="btn btn-outline-primary border btn-sm"><?php echo KunenaIcons::globe() . ' ' . $this->escape($this->profile->getWebsiteName()); ?></button>
            <?php endif; ?>
        </div>

        <div class="agora-profile-socials">
            <?php echo $this->subLayout('User/Item/Socials')->set('socials', $this->socials); ?>
        </div>
    </div>
</div>

<?php if ($stats) : ?>
    <dl class="agora-profile-stats">
        <?php foreach ($stats as [$value, $label]) : ?>
            <div>
                <dt><?php echo rtrim($label, ': '); ?></dt>
                <dd><?php echo $value; ?></dd>
            </div>
        <?php endforeach; ?>
    </dl>
<?php endif; ?>

<dl class="agora-profile-details">
    <?php if ($this->config->userlistJoinDate || $this->me->isModerator()) : ?>
        <div>
            <dt><?php echo KunenaIcons::calendar(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_REGISTERDATE'); ?></dt>
            <dd data-bs-toggle="tooltip" title="<?php echo $this->profile->getRegisterDate()->toKunena('ago'); ?>"><?php echo $this->profile->getRegisterDate()->toKunena('date_today', 'utc'); ?></dd>
        </div>
    <?php endif; ?>

    <?php if ($this->config->userlistLastVisitDate || $this->me->isModerator()) : ?>
        <div>
            <dt><?php echo KunenaIcons::clock(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_LASTLOGIN'); ?></dt>
            <dd data-bs-toggle="tooltip" title="<?php echo $this->profile->getLastVisitDate()->toKunena('ago'); ?>"><?php echo $this->profile->getLastVisitDate()->toKunena('config_postDateFormat'); ?></dd>
        </div>
    <?php endif; ?>

    <div>
        <dt><?php echo KunenaIcons::globe(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?></dt>
        <dd>UTC <?php echo $this->profile->getTime()->toTimezone(); ?></dd>
    </div>

    <div>
        <dt><?php echo KunenaIcons::clock(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?></dt>
        <dd><?php echo $this->profile->getTime()->toKunena('time'); ?></dd>
    </div>

    <?php if (!empty($this->profile->location)) : ?>
        <div>
            <dt><?php echo KunenaIcons::location(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_LOCATION'); ?></dt>
            <dd>
                <a href="<?php echo $this->escape(KunenaMap::linkUrl((string) $this->profile->location)); ?>"
                   target="_blank"
                   rel="nofollow noopener noreferrer"><?php echo $this->escape($this->profile->location); ?></a>
            </dd>
        </div>
    <?php endif; ?>

    <?php if ($this->profile->getGender()) : ?>
        <div>
            <dt><?php echo KunenaIcons::user(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_GENDER'); ?></dt>
            <dd><?php echo $this->profile->getGender(); ?></dd>
        </div>
    <?php endif; ?>

    <?php if ($this->profile->birthdate && $this->profile->birthdate != '1000-01-01') : ?>
        <div>
            <dt><?php echo KunenaIcons::birthdate(); ?> <?php echo Text::_('COM_KUNENA_MYPROFILE_BIRTHDATE'); ?></dt>
            <dd><?php echo KunenaDate::getInstance($this->profile->birthdate)->toSpan('date', 'ago', 'utc'); ?></dd>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->medals)) : ?>
        <div>
            <dt><?php echo KunenaIcons::star(); ?> <?php echo Text::_('COM_KUNENA_AUP_MEDALS'); ?></dt>
            <dd><?php echo implode(' ', $this->medals); ?></dd>
        </div>
    <?php endif; ?>
</dl>

<?php if ($this->profile->getPersonalText() || $this->profile->getSignature()) : ?>
    <div class="agora-profile-texts">
        <?php if ($this->profile->getPersonalText()) : ?>
            <section class="agora-profile-text">
                <h2><?php echo Text::_('COM_KUNENA_MYPROFILE_ABOUTME'); ?></h2>
                <div><?php echo $this->profile->getPersonalText(); ?></div>
            </section>
        <?php endif; ?>

        <?php if ($this->profile->getSignature()) : ?>
            <section class="agora-profile-text">
                <h2><?php echo Text::_('COM_KUNENA_MYPROFILE_SIGNATURE'); ?></h2>
                <div><?php echo $this->profile->getSignature(); ?></div>
            </section>
        <?php endif; ?>
    </div>
<?php endif; ?>
