<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Config
 * @subpackage      Configuration
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" method="post" name="settingFormModal" id="settingFormModal">
    <div class="p-3">
        <div class="row">
            <p><?php echo Text::_('COM_KUNENA_CONFIG_MODAL_DESCRIPTION'); ?></p>
        </div>
    </div>
    <div class="btn-toolbar p-3">
        <button type="submit" class="btn btn-success ms-auto" onclick="document.getElementById('settingFormModal').submit();"><?php echo Text::_('JSUBMIT'); ?></button>
    </div>
    <input type="hidden" name="task" value="config.setDefault" />
    <?php echo HTMLHelper::_('form.token') ?>
</form>