<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Pages.Category
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/*
 * The layout App: the index of the forum starts like the home of an app. A greeting, tiles with the numbers of the visitor (new
 * topics of the last day, the own posts and the thanks received; only the first one for a guest), then the latest activity of the
 * forum as a list, then the categories, who is online and the statistics as on the other layouts (assets/scss/layoutsets/_app.scss).
 */

$content = $this->execute('Category/Index');

$this->addBreadcrumb(
    Text::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT'),
    'index.php?option=com_kunena&view=category&layout=list'
);

$me       = KunenaUserHelper::getMyself();
$template = KunenaFactory::getTemplate();
$config   = KunenaFactory::getConfig();

// The topics which the visitor may read (KunenaTopicHelper checks it): the number of the last day, the five latest ones.
// The time of the first post: with the default order the time is the one of the last post, and an old topic with a new reply counted.
[$newToday] = KunenaTopicHelper::getLatestTopics(false, 0, 1, ['starttime' => time() - 86400, 'orderby' => 'tt.first_post_time DESC']);
[, $latest] = KunenaTopicHelper::getLatestTopics(false, 0, 5);

// Which of them the member has not read: the list marks them (the query of the list does not know it, it was never marked).
KunenaTopicHelper::fetchNewStatus($latest);

$tiles = [
    [
        $newToday,
        Text::_('COM_KUNENA_TEMPLATE_AGORA_TILE_NEW_TODAY'),
        KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=default&mode=topics'),
    ],
];

if ($me->exists()) {
    $tiles[] = [(int) $me->posts, rtrim(Text::_('COM_KUNENA_POSTS'), ': '), KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=posts&mode=recent&userid=' . (int) $me->userid)];

    if ($config->showThankYou) {
        $tiles[] = [(int) $me->thankyou, Text::_('COM_KUNENA_THANK_YOU_RECEIVED'), KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=posts&mode=thankyou&userid=' . (int) $me->userid)];
    }
}
?>
<section class="agora-home">
    <header class="agora-home-head">
        <p class="agora-home-site"><?php echo $this->escape($config->boardTitle); ?></p>
        <h1><?php echo $me->exists()
            ? Text::sprintf('COM_KUNENA_TEMPLATE_AGORA_GREETING', $this->escape($me->getName('', false)))
            : Text::_('COM_KUNENA_TEMPLATE_AGORA_GREETING_GUEST'); ?></h1>
    </header>

    <ul class="agora-home-tiles">
        <?php foreach ($tiles as $i => [$number, $label, $link]) : ?>
            <li>
                <a class="agora-home-tile<?php echo $i === 0 ? ' agora-home-tile-main' : ''; ?>" href="<?php echo $link; ?>">
                    <span class="agora-home-number"><?php echo (int) $number; ?></span>
                    <span class="agora-home-label"><?php echo $this->escape($label); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($latest) : ?>
        <section class="agora-home-activity">
            <h2><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_LATEST_ACTIVITY'); ?></h2>
            <ul>
                <?php foreach ($latest as $topic) :
                    $author = $topic->getLastPostAuthor();
                    $avatar = $author->getAvatarImage($template->params->get('avatarType'), 'thumb');
                    ?>
                    <li>
                        <a class="agora-home-item<?php echo $topic->unread ? ' agora-home-item-unread' : ''; ?>" href="<?php echo $topic->getUrl(null, true, 'last'); ?>">
                            <span class="agora-home-avatar" aria-hidden="true"><?php echo $avatar; ?></span>
                            <span class="agora-home-text">
                                <span><?php echo Text::sprintf('COM_KUNENA_TEMPLATE_AGORA_ACTIVITY_REPLY', '<b>' . $this->escape($author->getName('', false)) . '</b>', $this->escape($topic->subject)); ?></span>
                                <span class="agora-home-meta"><?php echo $this->escape($topic->getCategory()->name) . ' · ' . $topic->getLastPostTime()->toKunena('config_postDateFormat'); ?></span>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
</section>

<?php
echo $content;
echo $this->subRequest('Widget/WhoIsOnline');
echo $this->subRequest('Widget/Statistics');
