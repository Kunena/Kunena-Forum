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
        <a href="<?php echo $url; ?>" data-bs-toggle="tooltip" title="<?php echo $this->escape($filename); ?>">
            <?php echo $this->escape(KunenaAttachmentHelper::shortenFilename($filename)); ?>
        </a>

        <br/>

        <?php echo Text::_('COM_KUNENA_FILESIZE') . number_format($size / 1024, 0, '', ',') . ' ' .
        Text::_('COM_KUNENA_USER_ATTACHMENT_FILE_WEIGHT'); ?>

    <?php endif; ?>
</div>
