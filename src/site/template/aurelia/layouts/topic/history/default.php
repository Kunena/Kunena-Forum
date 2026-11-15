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
use Kunena\Forum\Libraries\Date\KunenaDate;

// The edit history of a message ($this->message): the edits, the newest first ($this->edits, their changes are HTML with <del> and
// <ins>, escaped), then the original version ($this->original).
$messageUrl = $this->message->getUrl($this->category);
?>
<div class="kunena-history">
    <h1 class="h3"><?php echo Text::sprintf('COM_KUNENA_HISTORY_TITLE', $this->message->displayField('subject')); ?></h1>

    <p class="kunena-history-intro text-muted">
        <?php echo Text::_('COM_KUNENA_HISTORY_LEGEND'); ?>
        <a href="<?php echo $messageUrl; ?>"><?php echo Text::_('COM_KUNENA_HISTORY_BACK'); ?></a>
    </p>

    <?php if (!$this->edits) : ?>
        <div class="alert alert-info" role="status"><?php echo Text::_('COM_KUNENA_HISTORY_NONE'); ?></div>
    <?php endif; ?>

    <ol class="kunena-history-list list-unstyled" reversed>
        <?php foreach ($this->edits as $index => $edit) :
            $date = KunenaDate::getInstance($edit->time); ?>
            <li class="kunena-history-edit card mb-3">
                <div class="card-header">
                    <?php echo Text::sprintf('COM_KUNENA_HISTORY_EDIT_BY', $edit->editor->getLink(null, null, ''), $date->toSpan('config_postDateFormat', 'config_postDateFormatHover')); ?>
                    <?php if ($edit->reason !== '') : ?>
                        <span class="kunena-history-reason">&middot; <?php echo Text::_('COM_KUNENA_REASON') . ': ' . $this->escape($edit->reason); ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($edit->subject !== '') : ?>
                        <p class="kunena-history-subject mb-2">
                            <strong><?php echo Text::_('COM_KUNENA_HISTORY_SUBJECT'); ?>:</strong> <?php echo $edit->subject; ?>
                        </p>
                    <?php endif; ?>
                    <div class="kunena-history-diff"><?php echo $edit->text; ?></div>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>

    <?php if ($this->original) : ?>
        <section class="kunena-history-original card mb-3" aria-labelledby="kunena-history-original-title">
            <div class="card-header" id="kunena-history-original-title">
                <?php echo Text::sprintf('COM_KUNENA_HISTORY_ORIGINAL', $this->original->author->getLink(null, null, ''), KunenaDate::getInstance($this->original->time)->toSpan('config_postDateFormat', 'config_postDateFormatHover')); ?>
            </div>
            <div class="card-body">
                <p class="kunena-history-subject mb-2">
                    <strong><?php echo Text::_('COM_KUNENA_HISTORY_SUBJECT'); ?>:</strong> <?php echo $this->original->subject; ?>
                </p>
                <div class="kunena-history-diff"><?php echo $this->original->text; ?></div>
            </div>
        </section>
    <?php endif; ?>
</div>
