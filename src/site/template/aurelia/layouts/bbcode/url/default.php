<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

// [url="www.kunena.org" target="_blank"]Kunena.org[/url]

// Display URL.
$rel = '';
$target = '';
if (!$this->internal) {
    $rel = 'rel="nofollow noopener noreferrer"';
    $target = ' target="' . $this->escape($this->target) . '"';
}
?>
<a href="<?php echo $this->escape($this->url); ?>"
   class="bbcode_url<?php
    if (!empty($this->class)) {
        echo ' ' . $this->class;
    } ?>" <?php echo $rel . $target; ?>>
    <?php echo $this->content; ?>
</a>
