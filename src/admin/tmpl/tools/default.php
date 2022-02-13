<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      CPanel
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Forum\KunenaForum;

?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="row clearfix">

                <h1>TOOLS</h1>
                <div class="col-xl-3 col-md-6">
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=report'); ?>">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <?php echo Text::_('COM_KUNENA_REPORT_SYSTEM'); ?>
                                    </h6>
                                    <h3 class="fw-700 text-cyan">
                                        <?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=prune'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_C_PRUNETAB'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_C_PRUNETAB_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=syncUsers'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_SYNC_USERS'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_SYNC_USERS_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=recount'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_A_RECOUNT'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_A_RECOUNT_DESC'); ?>
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

                <div class="clearfix"></div>
                <h1>system</h1>

                <div class="col-xl-3 col-md-6">
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=menu'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_A_MENU_MANAGER'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_A_MENU_MANAGER_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=purgerestatements'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_A_PURGE_RE_STATEMENTS_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=cleanupip'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=diagnostics'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS_DESC'); ?>
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
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=uninstall'); ?>">
                        <div class="card proj-t-card comp-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-25">
	                                        <?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PROCESS'); ?>
                                        </h6>
                                        <h3 class="fw-700 text-cyan">
	                                        <?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PROCESS_DESC'); ?>
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

				<?php if (KunenaForum::isDev())
					:
					?>
                    <div class="col-xl-3 col-md-6">
                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=install'); ?>">
                            <div class="card proj-t-card comp-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="mb-25">
	                                            <?php echo Text::_('COM_KUNENA_GIT_INSTALL'); ?>
                                            </h6>
                                            <h3 class="fw-700 text-cyan">
	                                            <?php echo Text::_('COM_KUNENA_GIT_INSTALL_DESC'); ?>
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
				<?php endif; ?>
        </div>
    </div>
    <div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
