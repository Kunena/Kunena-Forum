<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

// The reactions of a message ($this->message): the ones which were given ($this->counts, [reaction => number]) with the one of the
// user ($this->mine), and the list to choose from ($this->list, empty when the user may not react) with the task ($this->url).
$counts = (array) $this->counts;
$list   = (array) $this->list;

if (!$counts && !$list) {
    return;
}

$link = function (string $reaction): string {
    return KunenaRoute::_($this->url . '&reaction=' . rawurlencode($reaction));
};
?>
<div class="kreactions" role="group" aria-label="<?php echo Text::_('COM_KUNENA_REACTIONS'); ?>">
    <?php foreach ($counts as $reaction => $count) :
        $reaction = (string) $reaction;
        $mine     = $reaction === $this->mine;
        $label    = Text::plural('COM_KUNENA_REACTION_N', (int) $count, $this->escape($reaction))
            . ($mine ? ' ' . Text::_('COM_KUNENA_REACTION_YOURS') : ''); ?>
        <?php if (\in_array($reaction, $list, true)) : ?>
            <a class="kreaction<?php echo $mine ? ' kreaction-mine' : ''; ?>" href="<?php echo $link($reaction); ?>" rel="nofollow"
               title="<?php echo $this->escape(Text::sprintf($mine ? 'COM_KUNENA_REACTION_REMOVE' : 'COM_KUNENA_REACTION_REACT', $reaction)); ?>">
                <span aria-hidden="true"><?php echo $this->escape($reaction); ?></span>
                <span class="kreaction-count" aria-hidden="true"><?php echo (int) $count; ?></span>
                <span class="visually-hidden"><?php echo $label; ?></span>
            </a>
        <?php else : ?>
            <span class="kreaction<?php echo $mine ? ' kreaction-mine' : ''; ?>">
                <span aria-hidden="true"><?php echo $this->escape($reaction); ?></span>
                <span class="kreaction-count" aria-hidden="true"><?php echo (int) $count; ?></span>
                <span class="visually-hidden"><?php echo $label; ?></span>
            </span>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($list) : ?>
        <div class="dropdown kreaction-add">
            <button type="button" class="kreaction kreaction-add-button" data-bs-toggle="dropdown" aria-expanded="false"
                    title="<?php echo Text::_('COM_KUNENA_REACTION_ADD'); ?>">
                <?php echo KunenaIcons::plus(); ?>
                <span class="visually-hidden"><?php echo Text::_('COM_KUNENA_REACTION_ADD'); ?></span>
            </button>
            <ul class="dropdown-menu kreaction-menu">
                <?php foreach ($list as $reaction) :
                    $mine = $reaction === $this->mine; ?>
                    <li>
                        <a class="dropdown-item<?php echo $mine ? ' active' : ''; ?>" href="<?php echo $link($reaction); ?>" rel="nofollow"
                           aria-label="<?php echo $this->escape(Text::sprintf($mine ? 'COM_KUNENA_REACTION_REMOVE' : 'COM_KUNENA_REACTION_REACT', $reaction)); ?>">
                            <?php echo $this->escape($reaction); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
