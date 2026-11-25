<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$tools = [
    ['modifysocials', 'COM_KUNENA_ADMIN_TOOLS_MODIFYSOCIALS', 'COM_KUNENA_ADMIN_TOOLS_MODIFYSOCIALS_DESC', 'pen-to-square'],
    ['deletesocials', 'COM_KUNENA_ADMIN_TOOLS_DELETESOCIALS', 'COM_KUNENA_ADMIN_TOOLS_DELETESOCIALS_DESC', 'trash-can'],
];
?>

<div id="kunena" class="kd">
    <section class="kd-section" aria-labelledby="kd-managesocials-title">
        <h2 id="kd-managesocials-title" class="kd-section__title"><?php echo Text::_('COM_KUNENA_ADMIN_MANAGESOCIALS'); ?></h2>
        <div class="kd-cards">
            <?php foreach ($tools as [$layout, $label, $desc, $icon]) : ?>
                <a class="kd-card" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=' . $layout); ?>">
                    <span class="kd-card__icon fa-solid fa-<?php echo $icon; ?>" aria-hidden="true"></span>
                    <span class="kd-card__title"><?php echo Text::_($label); ?></span>
                    <span class="kd-card__desc"><?php echo Text::_($desc); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
