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

$attachment = $this->attachment;

if ($attachment->isImage()) {
    echo $this->render('image');
} elseif ($attachment->isAudio()) {
    echo $this->render('audio');
} elseif ($attachment->isVideo()) {
    echo $this->render('video');
} else {
    echo $this->render('general');
}
