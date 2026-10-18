<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Prune
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
                  method="post" id="adminForm"
                  name="adminForm">
                <input type="hidden" name="task" value="cleanupip"/>
                <?php echo HTMLHelper::_('form.token'); ?>

                <div class="card mb-4">
                    <div class="card-header fw-semibold">
                        <?php echo Text::_('COM_KUNENA_LEGEND_CLEANUP_IP'); ?>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <label for="cleanup_ip_days" class="col-md-3 col-form-label">
                                <?php echo Text::_('COM_KUNENA_CLEANUP_IP_LEGEND_FROMDAYS') ?>
                            </label>
                            <div class="col-md-9">
                                <div class="input-group" style="max-width: 16rem">
                                    <input class="form-control" type="number" min="0" id="cleanup_ip_days" name="cleanup_ip_days" value="30"/>
                                    <span class="input-group-text"><?php echo Text::_('COM_KUNENA_CLEANUP_IP_LEGEND_DAYS') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-form-label">
                                <?php echo Text::_('COM_KUNENA_DELETE_USERS_IP') ?>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="deleteIpUsers" name="deleteIpUsers" value="1"/>
                                    <label class="form-check-label" for="deleteIpUsers"><?php echo Text::_('COM_KUNENA_DELETE_USERS_IP_DESC'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="float-end small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
