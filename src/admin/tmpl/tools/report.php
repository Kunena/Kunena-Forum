<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Report
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->usePreset('choicesjs')
    ->useScript('multiselect')
    ->addInlineScript(
        '
document.addEventListener("DOMContentLoaded", function() {

    var copyToClipboard = function(inputId) {
        var input = document.getElementById(inputId);
        
        if (!input) {
            console.error("Could not find element with ID: " + inputId);
            return;
        }
        
        // Modern Clipboard API
        navigator.clipboard.writeText(input.value).then(function() {
            // Only select text when copy worked without error
            input.select(); 
            input.setSelectionRange(0, 99999);
            
            // Using standard concatenation to avoid Joomla parsing issues
            console.log("Copying successful for ID: " + inputId);
        }).catch(function(err) {
            console.error("Modern copy failed. Check HTTPS/Permissions: ", err);
        });
    };

    // Button 1
    var btn1 = document.getElementById("link_sel_all");
    if (btn1) {
        btn1.addEventListener("click", function(e) {
            e.preventDefault();
            copyToClipboard("report_final");
        });
    }

    // Button 2
    var btn2 = document.getElementById("link_sel_all_complete");
    if (btn2) {
        btn2.addEventListener("click", function(e) {
            e.preventDefault();
            copyToClipboard("report_final_anonymous");
        });
    }
});'
    );
?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
                    method="post" id="adminForm" name="adminForm">
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="boxchecked" value="1" />
                    <div class="card mb-4">
                        <div class="card-header fw-semibold">
                            <span class="icon-support me-1" aria-hidden="true"></span><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE'); ?>
                        </div>
                        <div class="card-body">
                            <p class="text-body-secondary"><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE_DESC'); ?></p>
                            <button type="button" id="link_sel_all" class="btn btn-sm btn-primary mb-3">
                                <span class="icon-copy me-1" aria-hidden="true"></span><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
                            </button>
                            <label for="report_final" class="visually-hidden"><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE'); ?></label>
                            <textarea id="report_final" class="form-control w-100 font-monospace small" name="report_final"
                                rows="15"><?php echo $this->escape($this->systemReport); ?></textarea>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header fw-semibold">
                            <span class="icon-support me-1" aria-hidden="true"></span><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS'); ?>
                        </div>
                        <div class="card-body">
                            <p class="text-body-secondary"><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS_DESC'); ?></p>
                            <button type="button" id="link_sel_all_complete" class="btn btn-sm btn-primary mb-3">
                                <span class="icon-copy me-1" aria-hidden="true"></span><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
                            </button>
                            <label for="report_final_anonymous" class="visually-hidden"><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS'); ?></label>
                            <textarea id="report_final_anonymous" class="form-control w-100 font-monospace small" name="report_final_anonymous"
                                rows="15"><?php echo $this->escape($this->systemReportAnonymous); ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="float-end small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>