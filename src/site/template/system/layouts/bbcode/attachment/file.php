<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.System
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$attachment = $this->attachment;
?>
<div class="kmsgattach">
    <h4>
        <?php echo Text::_('COM_KUNENA_FILEATTACH'); ?>
    </h4>

    <?php echo Text::_('COM_KUNENA_FILENAME'); ?>
    <?php echo $this->subLayout('Attachment/Item')->set('attachment', $attachment); ?>

    <br />

    <?php echo Text::_('COM_KUNENA_FILESIZE') . HTMLHelper::_('kunenaforum.formatfilesize', $attachment->size, 2); ?>
</div>