<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      CPanel
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Version\KunenaVersion;

?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="row clearfix">

                <h1><?php echo Text::_('COM_KUNENA_ADMIN_MANAGESOCIALS'); ?></h1>
                <div class="col-xl-3 col-md-6">
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=modifysocials'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
                                            <?php echo Text::_('COM_KUNENA_ADMIN_TOOLS_MODIFYSOCIALS'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
                                            <?php echo Text::_('COM_KUNENA_ADMIN_TOOLS_MODIFYSOCIALS_DESC'); ?>
                                        </h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-wrench bg-cyan"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-md-6">
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=deletesocials'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
                                            <?php echo Text::_('COM_KUNENA_ADMIN_TOOLS_DELETESOCIALS'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
                                            <?php echo Text::_('COM_KUNENA_ADMIN_TOOLS_DELETESOCIALS_DESC'); ?>
                                        </h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-wrench bg-cyan"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>                
            </div>
        </div>
    </div>
</div>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>