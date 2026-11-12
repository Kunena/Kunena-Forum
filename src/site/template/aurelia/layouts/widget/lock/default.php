<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;

// The badge of a locked topic: the lock icon, followed by the reason of the lock when it has one.
$reason = (string) $this->topic->lock_reason;
?>
<span class="badge bg-warning">
    <span data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_LOCKED'); ?>"><?php echo KunenaIcons::lock(); ?></span>
    <?php if ($reason !== '') : ?>
        <?php echo htmlspecialchars(Text::_($reason), ENT_QUOTES, 'UTF-8'); ?>
    <?php endif; ?>
</span>
