<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<div class="alert alert-info shadow-lg rounded alert-dismissible fade show" id="announcement<?php echo $this->announcement->id; ?>">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <h5>
        <a class="btn-link"
            href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=listing'); ?>"
            data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_VIEW_COMMON_ANNOUNCE_LIST') ?>">
            <?php echo $this->announcement->displayField('title'); ?>
        </a>

        <?php if ($this->announcement->showdate) :
            ?>
            <small>(<?php echo $this->announcement->displayField('created', 'date_today'); ?>)</small>
        <?php endif; ?>
    </h5>

    <div>
        <p><?php echo $this->announcement->displayField('sdescription'); ?></p>
    </div>
    <?php if (!empty($this->announcement->description)) :
        ?>
        <div>
            <a class="btn-link"
                href="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement&layout=default&id=' . $this->announcement->id); ?>"
                data-bs-toggle="tooltip" title="<?php echo $this->announcement->displayField('title') ?>">
                <?php echo Text::_('COM_KUNENA_ANN_READMORE'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

