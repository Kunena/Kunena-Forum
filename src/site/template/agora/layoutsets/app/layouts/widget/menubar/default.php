<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

/*
 * The layout App: the menu of the forum is always open, without the button and the side panel of the other layouts. On a large
 * screen its items are tonal tabs in the bar, the current one filled; on a small screen they are a navigation bar fixed at the
 * bottom of the screen, within reach of the thumb, which scrolls sideways when the items do not fit (assets/scss/layoutsets/_app.scss). The login
 * stays at the top.
 */
?>

<nav class="navbar navbar-expand agora-navbar agora-tonal-nav" aria-label="<?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_KUNENA_MENU'); ?>">
    <div class="container-fluid">
        <div class="agora-tonal-tabs">
            <?php echo $this->subRequest('Widget/Menu'); ?>
        </div>
        <div class="agora-navbar-end">
            <?php echo $this->subRequest('Widget/Login'); ?>
        </div>
    </div>
</nav>
