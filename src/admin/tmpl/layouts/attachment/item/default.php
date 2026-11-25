<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Layouts.Attachment
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

$attachment = $this->attachment;
$config     = KunenaFactory::getConfig();

// getUrl() gives a path in the media folder (a file which is not protected), to complete with the address of the site, or a
// route through the forum (a protected file). The forum checks the session of the site, not the one of the administration:
// a protected image is shown by the administration itself.
if (!preg_match('#^(https?:)?/#', $attachment->getUrl())) {
    $url = Uri::root() . $attachment->getUrl();
    $src = Uri::root() . $attachment->getUrl(true);
} elseif ($attachment->isImage()) {
    $url = Route::_('index.php?option=com_kunena&task=attachments.preview&id=' . (int) $attachment->id);
    $src = Route::_('index.php?option=com_kunena&task=attachments.preview&thumb=1&id=' . (int) $attachment->id);
} else {
    $url = $src = $attachment->getUrl();
}

if ($config->accessComponent) {
    // An image opens in the lightbox of Kunena, with the other images of the list to go through.
    $lightbox = $attachment->isImage() ? ' data-kunena-lightbox="attachments"' : '';
    ?>
    <a href="<?php echo $url; ?>" title="<?php echo $attachment->getFilename(); ?>"<?php echo $lightbox; ?>>
        <?php
        if ($attachment->isImage()) {
            echo '<img loading="lazy" class="kd-thumb" src="' . $src . '" height="40" width="40" alt="' . $attachment->getFilename() . '" />';
        } else {
            echo '<i class="icon-flag-2 icon-big"></i>';
        }
        ?>
    </a>
    <?php
} else {
    if ($attachment->isImage()) {
        echo '<i class="icon-picture icon-big"></i>';
    } else {
        echo '<i class="icon-flag-2 icon-big"></i>';
    }
}
