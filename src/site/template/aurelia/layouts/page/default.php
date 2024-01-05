<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Factory\KunenaFactory;

$templateParams = KunenaFactory::getTemplate()->params;
?>

<div id="kunena" class="layout <?php echo $this->options->get('pageclass_sfx'); ?>">
    <?php

    if ($templateParams->get('displayMenu')) {
        echo $this->subLayout('Widget/MenuBar');
    }

    if ($templateParams->get('displayModule')) {
        echo $this->subLayout('Widget/Module')->set('position', 'kunena_top');
    }

    if ($templateParams->get('displayBreadcrumb')) {
        echo $this->subLayout('Widget/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
    }

    echo $this->subLayout('Widget/Module')->set('position', 'kunena_announcement');

    if ($templateParams->get('displayAnnouncement')) {
        echo $this->subRequest('Widget/Announcement');
    }

    // Display current view/layout
    echo $this->content;

    if ($templateParams->get('displayBreadcrumb')) {
        echo $this->subLayout('Widget/Breadcrumb')->set('breadcrumb', $this->breadcrumb);
    }

    if ($templateParams->get('displayModule')) {
        echo $this->subLayout('Widget/Module')->set('position', 'kunena_bottom');
    }

    if ($templateParams->get('displayFooter')) {
        echo $this->subLayout('Widget/Footer');
    }
    ?>
</div>
