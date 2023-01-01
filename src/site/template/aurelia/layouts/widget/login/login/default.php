<?php

/**
 * Kunena Component
 *
 * @package     Kunena.Template.Aurelia
 * @subpackage  Layout.Widget
 *
 * @copyright   Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
**/

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Factory\KunenaFactory;

?>

<?php if ($this->plglogin) :
    ?>
    <div class="d-none d-lg-block">
        <?php
        if (KunenaFactory::getTemplate()->params->get('displayDropdownMenu')) :
            ?>
            <?php echo $this->setLayout('desktop'); ?>
        <?php endif; ?>
    </div>
    <div class="d-lg-none">
        <?php if (KunenaFactory::getTemplate()->params->get('displayDropdownMenu')) :
            ?>
            <?php echo $this->setLayout('mobile'); ?>
        <?php endif; ?>
    </div>
<?php endif;
