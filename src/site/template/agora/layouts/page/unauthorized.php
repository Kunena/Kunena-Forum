<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Page
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Factory\KunenaFactory;

$template = KunenaFactory::getTemplate();
?>
<div id="kunena" class="layout agora"<?php echo $template->getSchemeAttributes(); ?>>
    <?php echo $template->getSchemeScript(); ?>
    <?php
    echo $this->subLayout('Widget/MenuBar');

    // Display current view/layout
    echo $this->content;

    echo $this->subLayout('Widget/Footer');
    ?>
</div>
