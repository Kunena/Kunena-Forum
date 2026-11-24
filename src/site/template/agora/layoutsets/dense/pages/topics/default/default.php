<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Pages.Topics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/*
 * The layout Dense: the lists of topics with a side column of filters, as in a code forge. A button for a new topic and the modes of
 * the list (recent, new topics, no replies, sticky, locked; for a moderator the unapproved and the deleted ones), the current one
 * marked. On a small screen the filters are above the list as a row which scrolls sideways (assets/scss/layoutsets/_dense.scss).
 */

$content = $this->execute('Topic/Listing/Recent')
    ->setLayout('recent');

$this->addBreadcrumb(
    $content->headerText,
    'index.php?option=com_kunena&view=topics&layout=default'
);

$me      = KunenaUserHelper::getMyself();
$current = Factory::getApplication()->getInput()->getWord('mode', 'default');
$modes   = [
    'default'   => 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DEFAULT',
    'topics'    => 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS',
    'noreplies' => 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES',
    'sticky'    => 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY',
    'locked'    => 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED',
];

if ($me->isModerator()) {
    $modes['unapproved'] = 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED';
    $modes['deleted']    = 'COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED';
}

// The button for a new topic only when the visitor may start one in a category: a guest got it, then a page which refused him.
$canCreate = (bool) KunenaCategoryHelper::getCategories(false, false, 'topic.create');

if (!isset($modes[$current]) && $current === 'replies') {
    $current = 'default';
}
?>
<div class="agora-list-columns">
    <aside class="agora-list-filters-side">
        <?php if ($canCreate) : ?>
            <a class="btn btn-primary agora-side-create" href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=create'); ?>"><?php echo Text::_('COM_KUNENA_NEW_TOPIC'); ?></a>
        <?php endif; ?>
        <nav aria-label="<?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_LIST_MODES'); ?>">
            <ul>
                <?php foreach ($modes as $mode => $label) : ?>
                    <li>
                        <a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=default&mode=' . $mode); ?>"
                            <?php echo $mode === $current ? ' class="active" aria-current="page"' : ''; ?>><?php echo Text::_($label); ?></a>
                    </li>
                <?php endforeach; ?>
                <?php if ($me->exists()) : ?>
                    <li>
                        <a href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=default'); ?>"><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_MY_TOPICS'); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    <div class="agora-list-main">
        <?php echo $content; ?>
    </div>
</div>
