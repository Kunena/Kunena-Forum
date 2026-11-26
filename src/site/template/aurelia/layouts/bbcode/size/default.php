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

// [size=6]HUGE![/size]

// Change text size.
// The sizes 1 to 7 (the editor has seven). The tag takes a number only: any other number was written as "font-size:7", which is
// no size for a browser, so the largest size of the editor showed as normal text. A larger number is the largest size, a number
// between two sizes the smaller of them.
$size_css = [1 => '0.6em', '0.8em', '1em', '1.2em', '1.4em', '1.8em', '2.2em'];
$size     = (int) floor((float) $this->size);

if ($size < 1) {
    $size = 3;
}

$class = 'style="font-size:' . $size_css[min($size, 7)] . '"';
?>
<span <?php echo $class; ?>><?php echo $this->content; ?></span>
