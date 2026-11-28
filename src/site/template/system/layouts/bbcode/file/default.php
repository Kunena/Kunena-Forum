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
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;

$title    = $this->title;
$url      = $this->url;
$filename = $this->filename;
$size     = $this->size;
?>
<div class="kmsgattach">
    <h4>
        <?php echo $title; ?>
    </h4>

    <?php if ($url) :
    ?>

        <?php echo Text::_('COM_KUNENA_FILENAME'); ?>
        <?php // The address has the name of the file of the member: escaped as the title is. ?>
        <a href="<?php echo $this->escape($url); ?>" data-bs-toggle="tooltip" title="<?php echo $this->escape($filename); ?>">
            <?php echo $this->escape(KunenaAttachmentHelper::shortenFilename($filename)); ?>
        </a>

        <br />

        <?php echo Text::_('COM_KUNENA_FILESIZE') . HTMLHelper::_('kunenaforum.formatfilesize', $size, 2); ?>

    <?php endif; ?>
</div>