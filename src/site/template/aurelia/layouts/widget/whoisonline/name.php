<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Statistics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

?>

<?php if (!empty($this->onlineList)) : ?>
    <div>
        <?php
        foreach ($this->onlineList as $user) {
            $onlinelist[] = '<span class="kwho-member" data-kwho-role="' . $user->getType(0, true) . '">' . ($user->getLink(null, null, '')) . '</span>';
        }
        ?>
        <?php echo implode('<span class="kwho-comma">, </span>', $onlinelist); ?>
    </div>
<?php endif; ?>

<?php if (!empty($this->hiddenList)) : ?>
    <div>
        <span><?php echo Text::_('COM_KUNENA_HIDDEN_USERS'); ?>:</span>

        <?php
        foreach ($this->hiddenList as $user) {
            $hiddenlist[] = '<span class="kwho-member" data-kwho-role="' . $user->getType(0, true) . '">' . ($user->getLink(null, null, '')) . '</span>';
        }
        ?>
        <?php echo implode('<span class="kwho-comma">, </span>', $hiddenlist); ?>
    </div>
<?php endif; ?>
