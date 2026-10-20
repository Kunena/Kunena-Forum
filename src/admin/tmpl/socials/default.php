<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Socials
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// The toolbar buttons check the form before it is sent.
HTMLHelper::_('behavior.formvalidator');

// The table of the socials fits the page (a table with fixed columns, else the fields make the page wider than the screen).
$this->getDocument()->getWebAssetManager()->addInlineStyle(
    '#adminForm .subform-repeatable-wrapper table { table-layout: fixed; width: 100%; }'
    . '#adminForm .subform-repeatable-wrapper th:nth-child(1) { width: 15% !important; }'
    . '#adminForm .subform-repeatable-wrapper th:nth-child(2) { width: 19% !important; }'
    . '#adminForm .subform-repeatable-wrapper th:nth-child(3) { width: 30% !important; }'
    . '#adminForm .subform-repeatable-wrapper th:nth-child(4) { width: 19% !important; }'
    . '#adminForm .subform-repeatable-wrapper th:nth-child(5) { width: 10% !important; }'
    . '#adminForm .subform-repeatable-wrapper thead td:last-child { width: 110px !important; }'
    . '#adminForm .subform-repeatable-wrapper td .form-control, #adminForm .subform-repeatable-wrapper td .form-select { min-width: 0; max-width: 100%; }'
    . '#adminForm .subform-repeatable-wrapper td .minicolors { display: block; width: 100%; }'
    . '#adminForm .subform-repeatable-wrapper td .controls { min-width: 0; width: 100%; }'
    . '#adminForm .subform-repeatable-wrapper td .btn-group { white-space: nowrap; }'
);

?>
<form action="<?php echo Route::_('index.php?option=com_kunena&view=socials'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="alert alert-info">
                    <span class="icon-info-circle" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_A_SOCIALS_DESC'); ?>
                </div>

                <?php // The table uses the whole width: no label column and no second description. ?>
                <div class="table-responsive">
                    <?php echo $this->form->getInput('socials'); ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
