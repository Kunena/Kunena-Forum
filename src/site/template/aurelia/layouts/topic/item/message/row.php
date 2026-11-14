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

if ($this->message->id == $this->selected) {
    $link = $this->message->displayField('subject');
} else {
    $link = $this->getTopicLink(
        $this->message->getTopic(),
        $this->message,
        $this->message->displayField('subject')
    );
}
?>
<?php if ($this->message->id == $this->selected) : ?>
<tr class="message-<?php echo $this->message->getState(); ?> table-active" aria-current="true">
<?php else : ?>
<tr class="message-<?php echo $this->message->getState(); ?>">
<?php endif; ?>
    <td class="message-<?php echo $this->message->isNew() ? 'new' : 'read'; ?>">
        <span class="ktree ktree-<?php echo implode('" aria-hidden="true"></span><span class="ktree ktree-', (array) $this->message->indent); ?>" aria-hidden="true"></span>
        <?php echo $link; ?>
    </td>
    <td>
        <?php echo $this->message->getAuthor()->getLink(); ?>
    </td>
    <td data-bs-toggle="tooltip" title="<?php echo $this->message->getTime()->toKunena('config_postDateFormatHover'); ?>">
        <?php echo $this->message->getTime()->toKunena('config_postDateFormat'); ?>
    </td>
</tr>
