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
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

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
                    <fieldset>
                        <legend>
                            <i class="icon icon-support"></i> <?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE'); ?>
                        </legend>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>
                                    <p><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE_DESC'); ?></p>
                                    <p>
                                        <a href="#" id="link_sel_all" name="link_sel_all" type="button"
                                            class="btn btn-small btn-outline-primary"><i
                                                class="icon icon-signup"></i><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
                                        </a>
                                    </p>
                                    <textarea id="report_final" class="input-block-level" name="report_final" cols="80"
                                        rows="15"><?php echo $this->escape($this->systemReport); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>
                            <i class="icon icon-support"></i> <?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS'); ?>
                        </legend>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>
                                    <p><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS_DESC'); ?></p>
                                    <p>
                                        <a href="#" id="link_sel_all_complete" name="link_sel_all_complete"
                                            type="button"
                                            class="btn btn-small btn-outline-primary"><i
                                                class="icon icon-signup"></i><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
                                        </a>
                                    </p>
                                    <textarea id="report_final_anonymous" class="input-block-level"
                                        name="report_final_anonymous" cols="80"
                                        rows="15"><?php echo $this->escape($this->systemReportAnonymous); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="pull-right small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>