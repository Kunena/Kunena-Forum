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
use Kunena\Forum\Libraries\Html\KunenaLightbox;

// The lightbox of Kunena (js/lightbox.js) opens the links of the images; with "Use existing one" the site loads its own.
if (!KunenaLightbox::isKunena()) {
    return false;
}

$this->addStyleSheet('lightbox.css');
$this->addScript('lightbox.js');

foreach (['COM_KUNENA_LIGHTBOX', 'COM_KUNENA_LIGHTBOX_CLOSE', 'COM_KUNENA_LIGHTBOX_PREVIOUS', 'COM_KUNENA_LIGHTBOX_NEXT', 'COM_KUNENA_LIGHTBOX_COUNTER', 'COM_KUNENA_LIGHTBOX_DOWNLOAD', 'COM_KUNENA_LIGHTBOX_OPEN','COM_KUNENA_LIGHTBOX_LOADING', 'COM_KUNENA_LIGHTBOX_ERROR'] as $key) {
    Text::script($key);
}
