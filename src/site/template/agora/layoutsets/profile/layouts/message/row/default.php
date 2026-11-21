<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/*
 * The layout Profile: a message in a list of posts (the posts of a profile, the recent posts, the thanks) as a calm card: where and
 * when, the title of the message, the start of its text, the topic. The table of the list stays (one cell over all the columns),
 * the style sheet lays the cards out in a grid (assets/scss/layoutsets/_card-grid.scss).
 */

$this->addStyleSheet('rating.css');

$message         = $this->message;
$topic           = $message->getTopic();
$category        = $message->getCategory();
$this->ktemplate = KunenaFactory::getTemplate();
$config          = KunenaFactory::getConfig();
$cols            = empty($this->checkbox) ? 5 : 6;
$txt             = $topic->getActions();
$tooltips        = KunenaTemplate::getInstance()->tooltips();
$excerpt         = trim((string) preg_replace('/\s+/', ' ', (string) KunenaParser::stripBBCode((string) $message->message, 0, false)));
$excerpt         = mb_strimwidth($excerpt, 0, 180, '…');
?>
<tr class="category<?php echo $this->escape($category->class_sfx) . $txt; ?> agora-row-card">
    <td colspan="<?php echo $cols; ?>">
        <article class="agora-card agora-post-card<?php echo $topic->unread ? ' topic-item-unread' : ''; ?>">
            <span class="agora-card-time">
                <?php echo $this->getCategoryLink($category, null, $category->description, $tooltips); ?>
                <?php if ($config->postDateFormat != 'none') : ?>
                    · <?php echo $message->getTime()->toKunena('config_postDateFormat'); ?>
                <?php endif; ?>
            </span>

            <h3 class="agora-card-title">
                <?php echo $this->getTopicLink($topic, $message, $this->escape($message->subject), null, $tooltips . ' topictitle', $category, true, false); ?>
            </h3>

            <?php if ($excerpt !== '') : ?>
                <p class="agora-post-excerpt"><?php echo $this->escape($excerpt); ?></p>
            <?php endif; ?>

            <footer class="agora-card-foot">
                <span class="replies"><span class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span> <?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?></span>
                <span class="lastpostlink"><?php echo $this->getTopicLink($topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, $tooltips, $category, false, true); ?></span>
            </footer>

            <?php if (!empty($this->checkbox)) : ?>
                <label class="agora-card-check">
                    <input class="kcheck" type="checkbox" name="posts[<?php echo $message->id; ?>]" value="1"
                           aria-label="<?php echo $this->escape(Text::sprintf('COM_KUNENA_SELECT_POST_NAMED', $topic->subject)); ?>" />
                </label>
            <?php endif; ?>
        </article>
    </td>

    <?php
    if (!empty($this->position)) {
        echo $this->subLayout('Widget/Module')
            ->set('position', $this->position)
            ->set('cols', $cols)
            ->setLayout('table_row');
    }
    ?>
</tr>
