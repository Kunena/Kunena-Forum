<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicAnswer;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

// The accepted answer of the topic ($this->topic, in $this->category), at the top of the topic: its author, the beginning of its
// text and a link to it.
$config = KunenaConfig::getInstance();

if (isset($config->acceptedAnswers) && !$config->acceptedAnswers) {
    return;
}

$answer = KunenaTopicAnswer::getAnswer($this->topic);

if (!$answer || !$answer->isAuthorised('read')) {
    return;
}

$me     = KunenaUserHelper::getMyself();
$author = $answer->getAuthor();
$id     = 'kunena-topic-answer-' . (int) $this->topic->id;

// A guest does not see the text of the replies when the category hides them: only the link.
$excerpt = '';

if ($me->exists() || !$this->category->hidesRepliesForGuests()) {
    $excerpt = trim(preg_replace('/\s+/', ' ', (string) KunenaParser::stripBBCode((string) $answer->message, 200, false)));
}
?>
<section class="kunena-topic-answer card border-success mb-3" aria-labelledby="<?php echo $id; ?>">
    <div class="card-body">
        <h2 class="h6 card-title text-success mb-1" id="<?php echo $id; ?>">
            <?php echo KunenaIcons::check(); ?>
            <?php echo Text::_('COM_KUNENA_TOPIC_ACCEPTED_ANSWER'); ?>
        </h2>
        <p class="kunena-topic-answer-by small text-muted mb-2">
            <?php echo Text::sprintf('COM_KUNENA_TOPIC_ACCEPTED_ANSWER_BY', $author->getLink(null, null, '')); ?>
            &middot; <?php echo $answer->getTime()->toSpan('config_postDateFormat', 'config_postDateFormatHover'); ?>
        </p>
        <?php if ($excerpt !== '') : ?>
            <p class="kunena-topic-answer-text mb-2"><?php echo $this->escape($excerpt); ?></p>
        <?php endif; ?>
        <a class="btn btn-sm btn-outline-success" href="<?php echo $answer->getUrl($this->category); ?>">
            <?php echo Text::_('COM_KUNENA_TOPIC_ACCEPTED_ANSWER_GO'); ?>
        </a>
    </div>
</section>
