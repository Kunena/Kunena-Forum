<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      SyncUsers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">

                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
                      method="post" id="adminForm"
                      name="adminForm">
                    <input type="hidden" name="task" value="diagnostics"/>
                    <?php echo HTMLHelper::_('form.token'); ?>

                </form>

                <?php
                if ($this->check !== '') :
                    $fields = array_keys((array) reset($this->items));
                    ?>

                    <h3><?php echo Text::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_DIAG_ON', $this->escape($this->check)); ?></h3>
                    <p>
                        <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools&layout=diagnostics') ?>"><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_BACK') ?></a>
                    </p>
                    <?php if ($this->items) :
                        ?>
                        <p>
                            <?php echo KunenaDiagnostics::canFix($this->check) ? '<a href="' . KunenaRoute::_("administrator/index.php?option=com_kunena&task=tools.diagnostics&fix={$this->check}&" . Session::getFormToken() . '=1') . '">' . Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_FIX_ISSUES') . '</a>' : '' ?>
                            <?php echo KunenaDiagnostics::canDelete($this->check) ? '<a href="' . KunenaRoute::_("administrator/index.php?option=com_kunena&task=tools.diagnostics&delete={$this->check}&" . Session::getFormToken() . '=1') . '">' . Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_DELETE_BROKEN_ITEMS') . '</a>' : '' ?>
                            <?php echo KunenaDiagnostics::canNotice($this->check) ? KunenaDiagnostics::canNotice($this->check) : '' ?>
                        </p>
                        <p><?php echo Text::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_ITEMS_SHOWN', \count($this->items), $this->itemsTotal) ?></p>
                    <?php endif ?>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <?php if ($this->items) :
                                ?>
                                <thead>
                                <tr>
                                    <?php foreach ($fields as $field) :
                                        ?>
                                        <th class="text-break"><?php echo $this->escape($field) ?></th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <?php foreach ($this->items as $row) :
                                    ?>
                                    <tr>
                                        <?php foreach ($row as $field => $value) :
                                            ?>
                                            <?php
                                            $value = (string) $value;

                                            if (mb_strlen($value) > 200) {
                                                $value = mb_substr($value, 0, 200) . '…';
                                            }
                                            ?>
                                            <td class="text-break<?php echo ($this->fieldInfo[$field] ?? '') === 'invalid' ? ' table-danger' : '' ?>"><?php echo $this->escape($value) ?></td>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                            <?php else :
                                ?>
                                <tr>
                                    <td><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_NO_ISSUES_FOUND') ?></td>
                                </tr>
                            <?php endif ?>
                        </table>
                    </div>

                <?php else :
                    ?>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS'); ?></th>
                        </tr>
                        </thead>
                        <?php foreach (KunenaDiagnostics::getList() as $item) :
                            ?>
                            <?php $count = KunenaDiagnostics::count($item) ?>
                            <tr>
                                <td><?php echo $item ?></td>
                                <?php if ($count) :
                                    ?>
                                    <td style="color:red;"><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_TEST_FAILED') ?></td>
                                    <td><?php echo Text::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_NUMBER_OF_ISSUES', "<b>{$count}</b>") ?></td>
                                    <td>
                                        <?php echo KunenaDiagnostics::canFix($item) ? '<a href="' . KunenaRoute::_("administrator/index.php?option=com_kunena&task=tools.diagnostics&fix={$item}&" . Session::getFormToken() . '=1') . '">' . Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_FIX_ISSUES') . '</a>' : '' ?>
                                        <?php echo KunenaDiagnostics::canDelete($item) ? '<a href="' . KunenaRoute::_("administrator/index.php?option=com_kunena&task=tools.diagnostics&delete={$item}&" . Session::getFormToken() . '=1') . '">' . Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_DELETE_BROKEN_ITEMS') . '</a>' : '' ?>
                                        <?php echo KunenaDiagnostics::canNotice($item) ? KunenaDiagnostics::canNotice($item) : '' ?>
                                        <a href="<?php echo KunenaRoute::_("administrator/index.php?option=com_kunena&view=tools&layout=diagnostics&check={$item}") ?>"><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_SHOW_ITEMS') ?></a></td>
                                <?php else :
                                    ?>
                                    <td style="color:green;"><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_TEST_PASSED') ?></td>
                                    <td><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_NO_ISSUES_FOUND') ?></td>
                                    <td></td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </table>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="float-end small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
