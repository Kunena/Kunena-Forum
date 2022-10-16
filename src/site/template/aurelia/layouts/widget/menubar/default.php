<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-lg rounded border">
	<div class="container-fluid">
        <button class="navbar-toggler" aria-expanded="false" aria-controls="knav-offcanvas" aria-label="Toggle navigation"
                type="button" data-bs-target="#offcanvasKunena" data-bs-toggle="offcanvas">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="knav-offcanvas offcanvas offcanvas-start" id="offcanvasKunena" data-bs-scroll="false" >
            <div class="offcanvas-header">
            <h5 class="offcanvas-title"><?php echo Text::_('COM_KUNENA_TEMPLATE_AURELIA_KUNENA_MENU'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
            <?php echo $this->subRequest('Widget/Menu');
            ?>
            </div>
        </div>
        <div class="float-end">
            <?php echo $this->subRequest('Widget/Login');
            ?>
        </div>
    </div>
</nav>
